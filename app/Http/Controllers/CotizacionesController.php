<?php

namespace App\Http\Controllers;


//use App\Models\CotizacionDetalle;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Producto;
use App\Models\Servicios;
use App\Models\Transporte;
use App\Models\Cotizaciones;
use App\Models\TPersonal;
use App\Models\OrdenTrabajo;
use App\Services\OrdenTrabajoEstadoService;
use App\Models\CotizacionServicio;
use App\Models\CotizacionTransporte;
use App\Models\CotizacionProducto;
use App\Models\CotizacionTPersonal;
use App\Models\CotizacionCondiciones;
//use App\Models\ComprobanteSerie;

use Barryvdh\DomPDF\Facade\Pdf;


use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class CotizacionesController extends Controller
{
//protected OrdenTrabajoEstadoService $ordenTrabajoEstadoService;

    public function __construct(){
        $this->middleware('can:cotizaciones_list')->only(['index']);
        $this->middleware('can:cotizaciones_create')->only(['store']);
        $this->middleware('can:cotizaciones_edit')->only(['show', 'update']);
        $this->middleware('can:cotizaciones_delete')->only(['destroy']);
    }
    /**
     * Display a listing of the resource.
     */
 

    public function index(Request $request)
{
    if ($request->ajax()) {
        $data = Cotizaciones::with(['cliente', 'user', 'ordenTrabajo.sedes'])
            ->select(['id', 'user_id', 'fecha', 'cliente_id', 'ot_codigo', 'forma_pago', 'total', 'estado'])
            ->orderBy('id', 'desc');

        return DataTables::of($data)
            ->addColumn('usuario', function ($row) {
                return optional($row->user)->name ?? '—';
            })
            ->addColumn('cliente', function ($row) {
                return optional($row->cliente)->razon_social ?? '—';
            })
            ->addColumn('ordentrabajo', function ($row) {
                return optional($row->ordentrabajo)->ot_codigo ?? '—';
            })
            ->addColumn('sede', function ($row) {
                return optional($row->ordenTrabajo->sedes)->nombre ?? '—';
            })
            ->addColumn('descripcion_ot', function ($row) {
                return optional($row->ordenTrabajo)->descripcion ?? '—';
            })
            ->addColumn('codigo_ot_cliente', function ($row) {
                return $row->ordenTrabajo->codigo_ot_cliente ?? '-';
            })
            ->editColumn('fecha', function ($row) {
                return \Carbon\Carbon::parse($row->fecha)->format('d/m/Y');
            })
            ->editColumn('total', function ($row) {
                return '<span class="fw-bold text-dark">S/ ' . number_format($row->total, 2) . '</span>';
            })
            ->editColumn('estado', function ($row) {
                $status = strtolower($row->estado ?? '');
                $badgeClass = match ($status) {
                    'pendiente' => 'warning',
                    'aprobado', 'registrado' => 'success',
                    'anulado' => 'danger',
                    default => 'info',
                };
                return "<span class='badge-status-chip stat-chip-{$badgeClass} text-uppercase px-2 py-1'>{$row->estado}</span>";
            })
            ->addColumn('action', function ($row) {
                $buttons = '<div class="d-flex align-items-center justify-content-center gap-1">';

                if (auth()->user()->can('cotizaciones_edit') && optional($row->ordenTrabajo)->estado < 10) {
                    $buttons .= view('components.button-edit', ['id' => $row->id])->render();
                }

                if (auth()->user()->can('cotizaciones_delete') && optional($row->ordenTrabajo)->estado < 10) {
                    $buttons .= view('components.button-delete', ['id' => $row->id])->render();
                }

                $buttons .= '<a href="' . route('cotizaciones.imprimir', $row->id) . '" 
                    target="_blank" 
                    class="btn btn-action-print" 
                    title="Imprimir con IGV" aria-label="Imprimir con IGV">
                    <i class="bi bi-file-earmark-pdf"></i>
                </a>';

                $buttons .= '<a href="' . route('cotizaciones.imprimirsinigv', $row->id) . '" 
                    target="_blank" 
                    class="btn btn-action-pdf" 
                    title="Imprimir sin IGV" aria-label="Imprimir sin IGV">
                    <i class="bi bi-file-earmark-text"></i>
                </a>';

                $buttons .= '</div>';

                return $buttons;
            })
            ->filterColumn('cliente', function ($query, $keyword) {
                $query->whereHas('cliente', function ($q) use ($keyword) {
                    $q->where('razon_social', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('ordentrabajo', function ($query, $keyword) {
                $query->whereHas('ordentrabajo', function ($q) use ($keyword) {
                    $q->where('codigo_ot_cliente', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['total', 'estado', 'action'])
            ->make(true);
    }

    $stats = [
        'total_cotizaciones' => Cotizaciones::count(),
        'total_monto' => Cotizaciones::sum('total'),
        'total_aprobadas' => Cotizaciones::whereIn('estado', ['aprobado', 'registrado'])->count(),
        'total_pendientes' => Cotizaciones::where('estado', 'pendiente')->count(),
    ];

    return view('cotizaciones.index', compact('stats'));
}

 public function update(Request $request, $id)
{
    DB::beginTransaction();

    try {
        \Log::info('✏️ Update cotización request:', $request->all());

        $cotizacion = Cotizaciones::findOrFail($id);

        $data = $request->all();
        $totalGeneral = 0;

        // =========================
        // 🔹 ACTUALIZAR CABECERA
        // =========================
        $cotizacion->update([
            'cliente_id'    => $data['cliente_id'],
            'ot_codigo'     => $data['ot_codigo'] ?? null,
            'forma_pago'    => $data['forma_pago'],
            //'validez'       => $data['validez'],
            'observaciones' => $data['observaciones'] ?? null,
            //'tiempo_entrega'=> $data['tiempo_entrega'],
            //'garantia'      => $data['garantia'],
            'estado'        => $data['estado'] ?? $cotizacion->estado,
        ]);

        // =========================
        // 🔹 ELIMINAR DETALLES ANTERIORES
        // =========================
        $cotizacion->tpersonal()->delete();
        $cotizacion->servicios()->delete();
        $cotizacion->transportes()->delete();
        $cotizacion->productos()->delete();
        $cotizacion->condiciones()->delete();

         // =========================
        // 🔹 TRANSPORTE PERSONAL
        // =========================
        if (!empty($data['tpersonal_detalles'])) {
            foreach ($data['tpersonal_detalles'] as $item) {
                $precio = (float) $item['precio'];
                $cantidad = (float) $item['cantidad'];
                $subtotal = $precio * $cantidad;

                CotizacionTPersonal::create([
                    'cotizacion_id'  => $cotizacion->id,
                    'tpersonal_id'  => $item['tpersonal_id'],
                    'cantidad'       => $cantidad,
                    'precio_unitario'=> $precio,
                    'subtotal'       => $subtotal,
                ]);

                $totalGeneral += $subtotal;
            }
        }


        // =========================
        // 🔹 CONDICIONES
        // =========================
        if (!empty($data['condicion_detalles'])) {
        foreach ($data['condicion_detalles'] as $item) {

        CotizacionCondiciones::create([
            'cotizacion_id' => $cotizacion->id,
            'condicion_id'  => $item['condicion_id'],
                            ]);

                }
        }        

        // =========================
        // 🔹 SERVICIOS
        // =========================
        if (!empty($data['servicio_detalles'])) {
            foreach ($data['servicio_detalles'] as $item) {
                $precio = (float) $item['precio'];
                $cantidad = (float) $item['cantidad'];
                $subtotal = $precio * $cantidad;

                CotizacionServicio::create([
                    'cotizacion_id'  => $cotizacion->id,
                    'servicio_id'    => $item['servicio_id'],
                    'cantidad'       => $cantidad,
                    'precio_unitario'=> $precio,
                    'subtotal'       => $subtotal,
                ]);

                $totalGeneral += $subtotal;
            }
        }

        // =========================
        // 🔹 TRANSPORTE
        // =========================
        if (!empty($data['transporte_detalles'])) {
            foreach ($data['transporte_detalles'] as $item) {
                $precio = (float) $item['precio'];
                $cantidad = (float) $item['cantidad'];
                $subtotal = $precio * $cantidad;

                CotizacionTransporte::create([
                    'cotizacion_id'  => $cotizacion->id,
                    'transporte_id'  => $item['transporte_id'],
                    'cantidad'       => $cantidad,
                    'precio_unitario'=> $precio,
                    'subtotal'       => $subtotal,
                ]);

                $totalGeneral += $subtotal;
            }
        }

        // =========================
        // 🔹 MATERIALES / PRODUCTOS
        // =========================
        if (!empty($data['material_detalles'])) {
            foreach ($data['material_detalles'] as $item) {
                $precio = (float) $item['precio_venta'];
                $cantidad = (float) $item['cantidad'];
                $subtotal = $precio * $cantidad;

                CotizacionProducto::create([
                    'cotizacion_id'  => $cotizacion->id,
                    'producto_id'    => $item['producto_id'],
                    'cantidad'       => $cantidad,
                    'precio_unitario'=> $precio,
                    'subtotal'       => $subtotal,
                ]);

                $totalGeneral += $subtotal;
            }
        }

        // =========================
        // 🔹 ACTUALIZAR TOTAL
        // =========================
        $cotizacion->update([
            'total' => $totalGeneral
        ]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Cotización actualizada correctamente.',
            'cotizacion_id' => $cotizacion->id
        ]);

    } catch (\Exception $e) {
        DB::rollBack();

        \Log::error('❌ Error al actualizar cotización:', [
            'error' => $e->getMessage(),
            'line'  => $e->getLine()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error al actualizar la cotización'
        ], 500);
    }
}



public function show($id)
{
    try {
        // 🔹 Cargamos la cotización con todas sus relaciones
        $cotizacion = Cotizaciones::with([
            'cliente',
            'user',
            'ordenTrabajo',
            'servicios.servicio',
            'tpersonal.tpersonal.departamento',
            'tpersonal.tpersonal.provincia',
            'tpersonal.tpersonal.distrito',
            'transportes.transporte',
            'transportes.transporte.departamento',
            'transportes.transporte.provincia',
            'transportes.transporte.distrito',
            'productos.producto',
            'condiciones.condiciones'
        ])->findOrFail($id);

        \Log::info('PRODUCTO RELACIONADO:', [
        $cotizacion->productos->first()?->producto
        ]);

        // 🔹 Estructuramos la respuesta
        $response = [
            'id'            => $cotizacion->id,
            'cliente_id'    => $cotizacion->cliente_id,
            'cliente'       => optional($cotizacion->cliente)->razon_social,
            'user'          => optional($cotizacion->user)->name,
            //'ot_id'         => optional($cotizacion->ordentrabajo)->id,
            //'ot_codigo'     => optional($cotizacion->ordentrabajo)->codigo,

            //'ot_id' => $cotizacion->orden_trabajo_id,
            //'ot_codigo' => optional($cotizacion->ordentrabajo)->codigo_ot_cliente,

            'ot_codigo'  => $cotizacion->ot_codigo,
            'ot_cliente' => optional($cotizacion->ordenTrabajo)->codigo_ot_cliente,


            'forma_pago'    => $cotizacion->forma_pago,
            //'validez'       => $cotizacion->validez,
            'estado'        => $cotizacion->estado,
            'total'         => $cotizacion->total,
            //'garantia'      => $cotizacion->garantia,
            //'tiempo_entrega'=> $cotizacion->tiempo_entrega,
            'observaciones' => $cotizacion->observaciones,
            'fecha'         => $cotizacion->fecha,

            // 🔹 Detalles de servicios
            'servicios' => $cotizacion->servicios->map(function ($item) {
                return [
                    'id'                => $item->id,
                    'servicio_id'       => $item->servicio_id,
                    'descripcion'       => optional($item->servicio)->descripcion,
                    'cantidad'          => $item->cantidad,
                    'precio_unitario'   => $item->precio ?? ($item->cantidad > 0 ? $item->subtotal / $item->cantidad : 0),
                    'subtotal'          => $item->subtotal,
                ];
            }),



            // 🔹 Detalles de condiciones
            'condiciones' => $cotizacion->condiciones->map(function ($item) {
                return [
                    'id'                => $item->id,
                    'condicion_id'       => $item->condicion_id,
                    'descripcion'       => optional($item->condiciones)->descripcion,                    
                ];
            }),


            //Detalles de transporte de personal
           'tpersonal' => $cotizacion->tpersonal->map(function ($item) {
            $tpersonal = $item->tpersonal;

            return [
            'id'            => $item->id,
            'tpersonal_id' => $item->tpersonal_id,

            // ✅ Ubigeo completo limpio
            'name' => collect([
            trim(optional(optional($tpersonal)->departamento)->name ?? ''),
            trim(optional(optional($tpersonal)->provincia)->name ?? ''),
            trim(optional(optional($tpersonal)->distrito)->name ?? ''),
            ])->filter()->implode(' / '),

            'cantidad'       => $item->cantidad,
            'precio_unitario'=> $item->precio  ?? ($item->cantidad > 0 ? $item->subtotal / $item->cantidad : 0),
            'subtotal'       => $item->subtotal,
            ];
            }),

            // 🔹 Detalles de transporte
            'transporte' => $cotizacion->transportes->map(function ($item) {
            $transporte = $item->transporte;

            return [
            'id'            => $item->id,
            'transporte_id' => $item->transporte_id,

            // ✅ Ubigeo completo limpio
            'name' => collect([
            trim(optional(optional($transporte)->departamento)->name ?? ''),
            trim(optional(optional($transporte)->provincia)->name ?? ''),
            trim(optional(optional($transporte)->distrito)->name ?? ''),
            ])->filter()->implode(' / '),

            'cantidad'       => $item->cantidad,
            'precio_unitario'=> $item->precio  ?? ($item->cantidad > 0 ? $item->subtotal / $item->cantidad : 0),
            'subtotal'       => $item->subtotal,
            ];
            }),

           // 🔹 Detalles de materiales / productos
'productos' => $cotizacion->productos->map(function ($item) {

    $producto = $item->producto;

    return [
        'id'            => $item->id,
        'producto_id'   => $item->producto_id,
        'nombre'        => optional($producto)->nombre,
        'cantidad'      => $item->cantidad,

        // 🔥 Precio guardado en la cotización
        'precio_venta'  => $item->precio ?? ($item->cantidad > 0 ? $item->subtotal / $item->cantidad : 0),

        'subtotal'      => $item->subtotal,

        // 🔥 NUEVO → Precio base real del producto
        'precio_base'   => optional($producto)->precio_venta,

        // 🔥 NUEVO → Escalas de descuento del producto
        'descuento_5'   => optional($producto)->porcentaje5 ?? 0,
        'descuento_10'  => optional($producto)->porcentaje10 ?? 0,
        'descuento_50'  => optional($producto)->porcentaje50 ?? 0,
        'descuento_100' => optional($producto)->porcentaje100 ?? 0,
    ];
}),
        ];

        return response()->json([
            'success' => true,
            'data' => $response
        ], 200);

    } catch (\Throwable $e) {
        \Log::error('Error al mostrar cotización', [
            'cotizacion_id' => $id,
            'error' => $e->getMessage(),
            'archivo' => $e->getFile(),
            'linea' => $e->getLine(),
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener los datos de la cotización.'
        ], 500);
    }
}


public function obtenerParaVenta($codigo)
{
    $cotizaciones = Cotizaciones::with([
        'cliente',
        'materiales.producto',
        'servicios.servicio',
        'transportes.transporte',
        'tpersonal.tpersonal'
    ])->where('codigo',$codigo)->firstOrFail();

    return response()->json($cotizaciones);
}


    /**
     * Store a newly created resource in storage.
     */


public function store(Request $request)
{
    try {

        // ✅ VALIDACIÓN (TU FUNCIÓN)
        $data = $this->validateData($request);

        \Log::info('🧾 STORE COTIZACION', $request->all());

        $totalGeneral = 0;

        // ============================
        // CREAR COTIZACIÓN BASE
        // ============================
        $cotizacion = Cotizaciones::create([
            'cliente_id'    => $data['cliente_id'],
            'ot_codigo'     => $data['ot_codigo'],
            'forma_pago'    => $data['forma_pago'],
            //'validez'       => $data['validez'],
            //'garantia'      => $data['garantia'],
            //'tiempo_entrega'=> $data['tiempo_entrega'],
            'observaciones' => $data['observaciones'] ?? null,
            'fecha'         => now(),
            'total'         => 0,
            'estado'        => 'Registrado',
            'user_id'       => auth()->id(),
        ]);

        // ============================
        // 🚚 TRANSPORTE PERSONAL
        // ============================
        if (!empty($data['tpersonal_detalles'])) {
            foreach ($data['tpersonal_detalles'] as $item) {
                $subtotal = $item['cantidad'] * $item['precio'];

                CotizacionTPersonal::create([
                    'cotizacion_id'   => $cotizacion->id,
                    'tpersonal_id'    => $item['tpersonal_id'],
                    'cantidad'        => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'subtotal'        => $subtotal,
                ]);

                $totalGeneral += $subtotal;
            }
        }

        // ============================
        // ⚙️ SERVICIOS
        // ============================
        if (!empty($data['servicio_detalles'])) {
            foreach ($data['servicio_detalles'] as $item) {
                $subtotal = $item['cantidad'] * $item['precio'];

                CotizacionServicio::create([
                    'cotizacion_id'   => $cotizacion->id,
                    'servicio_id'     => $item['servicio_id'],
                    'cantidad'        => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'subtotal'        => $subtotal,
                ]);

                $totalGeneral += $subtotal;
            }
        }

        // ============================
        // ⚙️ CONDICIONES
        // ============================
        if (!empty($data['condicion_detalles'])) {
        foreach ($data['condicion_detalles'] as $item) {

            CotizacionCondiciones::create([
            'cotizacion_id' => $cotizacion->id,
            'condicion_id'  => $item['condicion_id'],
                ]);

            }
        }

        // ============================
        // 🚚 TRANSPORTE
        // ============================
        if (!empty($data['transporte_detalles'])) {
            foreach ($data['transporte_detalles'] as $item) {
                $subtotal = $item['cantidad'] * $item['precio'];

                CotizacionTransporte::create([
                    'cotizacion_id'   => $cotizacion->id,
                    'transporte_id'   => $item['transporte_id'],
                    'cantidad'        => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'subtotal'        => $subtotal,
                ]);

                $totalGeneral += $subtotal;
            }
        }

        // ============================
        // 🧱 PRODUCTOS
        // ============================
        if (!empty($data['material_detalles'])) {
            foreach ($data['material_detalles'] as $item) {
                $subtotal = $item['cantidad'] * $item['precio_venta'];

                CotizacionProducto::create([
                    'cotizacion_id'   => $cotizacion->id,
                    'producto_id'     => $item['producto_id'],
                    'cantidad'        => $item['cantidad'],
                    'precio_unitario' => $item['precio_venta'],
                    'subtotal'        => $subtotal,
                ]);

                $totalGeneral += $subtotal;
            }
        }       

        // ============================
        // TOTAL
        // ============================
        $cotizacion->update(['total' => $totalGeneral]);

      // 🔄 CAMBIAR ESTADO DE LA OT
// ============================
$orden = OrdenTrabajo::find($data['ot_codigo']); // ahora ot_codigo es el ID de la OT

if ($orden) {
    \Log::info('🔄 Cambiando estado OT', [
        'ot_id' => $orden->id,
        'estado_anterior' => $orden->estado
    ]);

    OrdenTrabajoEstadoService::cambiarEstado($orden, 3); // 3 = id del nuevo estado
}



        return response()->json([
            'success'       => true,
            'message'       => 'Cotización guardada correctamente',
            'cotizacion_id' => $cotizacion->id
        ]);

    }
    // ❌ VALIDACIÓN
    catch (ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Errores de validación',
            'errors'  => $e->errors()
        ], 422);
    }
    // ❌ ERROR GENERAL
    catch (\Exception $e) {

        \Log::error('❌ Error store cotización', [
            'error' => $e->getMessage(),
            'line'  => $e->getLine()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error interno del servidor'
        ], 500);
    }
}
 
private function validateData(Request $request)
{
    $data = $request->validate([
        // 🔹 Cabecera
        'cliente_id' => 'required|exists:clientes,id',
        'ot_codigo'  => 'required|string|max:50',
        'forma_pago' => 'required|string|in:contado,credito',
        //'validez'    => 'required|integer|min:1',
        //'garantia'  => 'required|string|max:255',
        //'tiempo_entrega'  => 'required|string|max:255',
        'observaciones' => 'nullable|string|min:3', 

        // 🔹 Transporte personal
        'tpersonal_detalles' => 'nullable|array',
        'tpersonal_detalles.*.tpersonal_id' => 'required|integer',
        'tpersonal_detalles.*.cantidad' => 'required|numeric|min:1',
        'tpersonal_detalles.*.precio' => 'required|numeric|min:0',

        // 🔹 Servicios
        'servicio_detalles' => 'nullable|array',
        'servicio_detalles.*.servicio_id' => 'required|integer',
        'servicio_detalles.*.cantidad' => 'required|numeric|min:1',
        'servicio_detalles.*.precio' => 'required|numeric|min:0',

        // 🔹 Transporte
        'transporte_detalles' => 'nullable|array',
        'transporte_detalles.*.transporte_id' => 'required|integer',
        'transporte_detalles.*.cantidad' => 'required|numeric|min:1',
        'transporte_detalles.*.precio' => 'required|numeric|min:0',

        // 🔹 Materiales
        'material_detalles' => 'nullable|array',
        'material_detalles.*.producto_id' => 'required|integer',
        'material_detalles.*.cantidad' => 'required|numeric|min:1',
        'material_detalles.*.precio_venta' => 'required|numeric|min:0',

        // 🔹 Condiciones
        'condicion_detalles' => 'required|array|min:1',
        'condicion_detalles.*.condicion_id' => 'required|integer|exists:condiciones,id',

    ]);

    // 🔥 VALIDACIÓN CLAVE: al menos un detalle
    $hasDetails =
        !empty($data['tpersonal_detalles'] ?? []) ||
        !empty($data['servicio_detalles'] ?? []) ||
        !empty($data['transporte_detalles'] ?? []) ||
        !empty($data['material_detalles'] ?? []) ;
        //!empty($data['condicion_detalles'] ?? []);

    if (!$hasDetails) {
        throw \Illuminate\Validation\ValidationException::withMessages([
            'detalles' => 'Debe agregar al menos un material, condicion, servicio, transporte o personal.'
        ]);
    }

    return $data;
}


public function porCliente(Request $request, $clienteId)
{
    $query = Cotizaciones::with('ordenTrabajo')
        ->where('cliente_id', $clienteId);

    // 🔥 FILTRAR POR ESTADO DE LA OT
    if ($request->has('estado')) {
        $query->whereHas('ordenTrabajo', function ($q) use ($request) {
            $q->where('estado', $request->estado);
        });
    }

    $cotizaciones = $query
        ->orderBy('id', 'desc')
        ->get();

    return response()->json($cotizaciones);
}



   

public function imprimir($id)
{
    try {
        $cotizacion = Cotizaciones::with([
            'cliente',
            'user',
            'ordenTrabajo.sedes',
            'ordenTrabajo.responsables',
            'servicios.servicio',
            'tpersonal.tpersonal',
            'transportes.transporte.distrito',
            'productos.producto'
        ])->findOrFail($id);

        $totalServicios  = $cotizacion->servicios->sum('subtotal');
        $totalTPersonal  = $cotizacion->tpersonal->sum('subtotal');
        $totalTransporte = $cotizacion->transportes->sum('subtotal');
        $totalProductos  = $cotizacion->productos->sum('subtotal');

        $totalGeneral = $totalServicios + $totalTPersonal + $totalTransporte + $totalProductos;

        $opGravada = round($totalGeneral / 1.18, 2);
        $igv       = round($totalGeneral - $opGravada, 2);

        $pdf = Pdf::loadView('cotizaciones.pdf', compact(
            'cotizacion',
            'totalServicios',
            'totalTPersonal',
            'totalTransporte',
            'totalProductos',
            'totalGeneral',
            'opGravada',
            'igv'
        ));

        return $pdf->stream("Cotizacion_{$cotizacion->id}.pdf");

    } catch (\Exception $e) {
        \Log::error('❌ Error al generar PDF', [
            'error' => $e->getMessage()
        ]);

        abort(500, 'No se pudo generar el PDF');
    }
}

public function imprimirsinigv($id)
{
    try {
        $cotizacion = Cotizaciones::with([
            'cliente',
            'user',
            'ordenTrabajo.sedes',
            'ordenTrabajo.responsables',
            'servicios.servicio',
            'tpersonal.tpersonal',
            'transportes.transporte.distrito',
            'productos.producto'
        ])->findOrFail($id);

        $totalServicios  = $cotizacion->servicios->sum('subtotal');
        $totalTPersonal  = $cotizacion->tpersonal->sum('subtotal');
        $totalTransporte = $cotizacion->transportes->sum('subtotal');
        $totalProductos  = $cotizacion->productos->sum('subtotal');

        $totalGeneral = $totalServicios + $totalTPersonal + $totalTransporte + $totalProductos;

        $opGravada = round($totalGeneral / 1.18, 2);
        $igv       = round($totalGeneral - $opGravada, 2);

        $pdf = Pdf::loadView('cotizaciones.pdfsigv', compact(
            'cotizacion',
            'totalServicios',
            'totalTPersonal',
            'totalTransporte',
            'totalProductos',
            'totalGeneral',
            'opGravada',
            'igv'
        ));

        return $pdf->stream("Cotizacion_{$cotizacion->id}.pdf");

    } catch (\Exception $e) {
        \Log::error('❌ Error al generar PDF', [
            'error' => $e->getMessage()
        ]);

        abort(500, 'No se pudo generar el PDF');
    }
}



    public function destroy($id)
{
    try {
        // Buscar la cotización
        $cotizacion = Cotizaciones::findOrFail($id);

        // Eliminar detalles relacionados (en cascada)
        $cotizacion->tpersonal()->delete();
        $cotizacion->servicios()->delete();
        $cotizacion->transportes()->delete();
        $cotizacion->productos()->delete();
        $cotizacion->condiciones()->delete();

        // Finalmente eliminar la cotización
        $cotizacion->delete();

        \Log::info("🗑️ Cotización eliminada correctamente (ID: {$id})");

        return response()->json([
            'success' => true,
            'message' => 'Cotización eliminada correctamente.'
        ]);

    } catch (\Exception $e) {
        \Log::error('❌ Error al eliminar cotización:', ['error' => $e->getMessage()]);

        return response()->json([
            'success' => false,
            'message' => 'Error al eliminar la cotización: ' . $e->getMessage()
        ], 500);
    }
}



    
}
