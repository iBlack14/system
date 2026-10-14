<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Producto;
use App\Models\Servicios;
use App\Models\Transporte;
use App\Models\Ventas;
use App\Models\TPersonal;
use App\Models\OrdenTrabajo;
use App\Services\OrdenTrabajoEstadoService;
use App\Models\VentasServicio;
use App\Models\VentasTransporte;
use App\Models\VentasProducto;
use App\Models\VentasTPersonal;
use App\Models\ComprobanteSerie;
use Barryvdh\DomPDF\Facade\Pdf;


use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class VentasController extends Controller
{
//protected OrdenTrabajoEstadoService $ordenTrabajoEstadoService;

    public function __construct(){
        $this->middleware('can:ventas_list')->only(['index']);
        $this->middleware('can:ventas_create')->only(['store']);
        $this->middleware('can:ventas_edit')->only(['show', 'update']);
        $this->middleware('can:ventas_delete')->only(['destroy']);
    }
    /**
     * Display a listing of the resource.
     */
 

    public function index(Request $request)
{
    if ($request->ajax()) {
        $data = Ventas::with(['cliente', 'user', 'ordenTrabajo'])
            ->select(['id', 'user_id', 'fecha', 'cliente_id','serie','correlativo', 'ot_codigo', 'forma_pago', 'total', 'estado'])
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
            ->addColumn('codigo_ot_cliente', function ($row) {
                return $row->ordenTrabajo->codigo_ot_cliente ?? '-';
            })
            ->editColumn('fecha', function ($row) {
                return \Carbon\Carbon::parse($row->fecha)->format('d/m/Y');
            })
            ->editColumn('total', function ($row) {
                return 'S/ ' . number_format($row->total, 2);
            })
            ->editColumn('estado', function ($row) {
                $badgeClass = match ($row->estado) {
                    'pendiente' => 'warning',
                    'aprobado' => 'success',
                    'anulado' => 'danger',
                    default => 'secondary',
                };
                return "<span class='badge bg-{$badgeClass} text-uppercase'>{$row->estado}</span>";
            })
            ->addColumn('action', function ($row) {
                $buttons = '';

                if (auth()->user()->can('ventas_edit')&& optional($row->ordenTrabajo)->estado < 13) {
                    $buttons .= view('components.button-edit', ['id' => $row->id])->render();
                }

                if (auth()->user()->can('ventas_delete')&& optional($row->ordenTrabajo)->estado < 13) {
                    $buttons .= view('components.button-delete', ['id' => $row->id])->render();
                }
               

                // $buttons .= '<a href="' . route('ventas.imprimir', $row->id) . '" 
                // target="_blank" 
                // class="btn btn-sm btn-dark ms-1" 
                // title="Ver Cotización con  IGV">
                // <i class="bi bi-cash-coin"></i>
                // </a>';

                $ticketButton= '<a href="' . route('ventas.imprimir', $row->id) . '" 
                        target="_blank" 
                        class="btn btn-sm btn-secondary" 
                        title="Ver Comprobante">
                        <i class="bi bi-printer"></i>
                     </a>';

                

                //return $buttons;
                
                //----Codigo para poner un icono para enviar correo-----
                //$buttons .= '<a href="' . route('ventas.enviarCorreo', $row->id) . '" 
                //target="_blank" 
                //class="btn btn-sm btn-secondary ms-1" 
                //title="Enviar Correo">
                //<i class="bi bi-envelope-fill"></i>
                //</a>';

                return $buttons . $ticketButton;

            })
            ->filterColumn('cliente', function ($query, $keyword) {
                $query->whereHas('cliente', function ($q) use ($keyword) {
                    $q->where('razon_social', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('ordentrabajo', function ($query, $keyword) {
                $query->whereHas('ordentrabajo', function ($q) use ($keyword) {
                    $q->where('ot_codigo', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['estado', 'action'])
            ->make(true);
    }

    return view('ventas.index');
}

 public function update(Request $request, $id)
{
    DB::beginTransaction();

    try {
        \Log::info('✏️ Update ventas request:', $request->all());

        $ventas = Ventas::with('productos')->findOrFail($id);

        // ✅ VALIDACIÓN
        $data = $this->validateData($request);

        $totalGeneral = 0;

        // =========================
        // 🔹 OBTENER OT DESDE COTIZACIÓN
        // =========================
        $ot_codigo = $request->ot_codigo 
            ?? optional(\App\Models\Cotizaciones::find($data['cotizacion_id']))->ot_codigo;

        // =========================
        // 🔹 ACTUALIZAR CABECERA
        // =========================
        $ventas->update([
            'cliente_id'               => $data['cliente_id'],
            'cotizacion_id'            => $data['cotizacion_id'] ?? null,
            'ot_codigo'                => $ot_codigo,
            'forma_pago'               => $data['forma_pago'],
            'comprobante_tipo_codigo'  => $request->comprobante_tipo_codigo,
            'serie'                    => $request->serie,
            'correlativo'              => $request->correlativo,
            'observaciones'            => $data['observaciones'] ?? null,
            'estado'                   => $data['estado'] ?? $ventas->estado,
        ]);

        // =========================
        // 🔹 STOCK: CALCULAR DIFERENCIA
        // =========================
        $oldProductos = $ventas->productos->keyBy('producto_id');
        $newProductos = collect($data['material_detalles'] ?? [])->keyBy('producto_id');

        // 🔹 RECORRER TODOS LOS PRODUCTOS (viejos + nuevos)
        $allKeys = $oldProductos->keys()->merge($newProductos->keys())->unique();

        foreach ($allKeys as $productoId) {

            $oldCantidad = $oldProductos[$productoId]->cantidad ?? 0;
            $newCantidad = $newProductos[$productoId]['cantidad'] ?? 0;

            $diferencia = $newCantidad - $oldCantidad;

            if ($diferencia != 0) {

                \App\Models\Producto::updateStock(
                    $diferencia < 0, // true = devuelve, false = descuenta
                    [[
                        'producto_id' => $productoId,
                        'cantidad'    => abs($diferencia)
                    ]]
                );
            }
        }

        // =========================
        // 🔹 ELIMINAR DETALLES
        // =========================
        $ventas->tpersonal()->delete();
        $ventas->servicios()->delete();
        $ventas->transportes()->delete();
        $ventas->productos()->delete();

        // =========================
        // 🔹 TRANSPORTE PERSONAL
        // =========================
        if (!empty($data['tpersonal_detalles'])) {
            foreach ($data['tpersonal_detalles'] as $item) {

                if (empty($item['tpersonal_id'])) continue;

                $subtotal = $item['precio'] * $item['cantidad'];

                VentasTPersonal::create([
                    'ventas_id'       => $ventas->id,
                    'tpersonal_id'    => $item['tpersonal_id'],
                    'cantidad'        => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'subtotal'        => $subtotal,
                ]);

                $totalGeneral += $subtotal;
            }
        }

        // =========================
        // 🔹 SERVICIOS
        // =========================
        if (!empty($data['servicio_detalles'])) {
            foreach ($data['servicio_detalles'] as $item) {

                if (empty($item['servicio_id'])) continue;

                $subtotal = $item['precio'] * $item['cantidad'];

                VentasServicio::create([
                    'ventas_id'       => $ventas->id,
                    'servicio_id'     => $item['servicio_id'],
                    'cantidad'        => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'subtotal'        => $subtotal,
                ]);

                $totalGeneral += $subtotal;
            }
        }

        // =========================
        // 🔹 TRANSPORTE
        // =========================
        if (!empty($data['transporte_detalles'])) {
            foreach ($data['transporte_detalles'] as $item) {

                if (empty($item['transporte_id'])) continue;

                $subtotal = $item['precio'] * $item['cantidad'];

                VentasTransporte::create([
                    'ventas_id'       => $ventas->id,
                    'transporte_id'   => $item['transporte_id'],
                    'cantidad'        => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'subtotal'        => $subtotal,
                ]);

                $totalGeneral += $subtotal;
            }
        }

        // =========================
        // 🔹 PRODUCTOS
        // =========================
        if (!empty($data['material_detalles'])) {
            foreach ($data['material_detalles'] as $item) {

                if (empty($item['producto_id'])) continue;

                $subtotal = $item['precio_venta'] * $item['cantidad'];

                VentasProducto::create([
                    'ventas_id'       => $ventas->id,
                    'producto_id'     => $item['producto_id'],
                    'cantidad'        => $item['cantidad'],
                    'precio_unitario' => $item['precio_venta'],
                    'subtotal'        => $subtotal,
                ]);

                $totalGeneral += $subtotal;
            }
        }

        // =========================
        // 🔹 TOTAL
        // =========================
        $ventas->update([
            'total' => $totalGeneral
        ]);

        DB::commit();

        return response()->json([
            'success'   => true,
            'message'   => 'Venta actualizada correctamente.',
            'ventas_id' => $ventas->id
        ]);

    } catch (\Exception $e) {

        DB::rollBack();

        \Log::error('❌ Error al actualizar venta:', [
            'error' => $e->getMessage(),
            'line'  => $e->getLine()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error al actualizar la venta'
        ], 500);
    }
}



public function show($id)
{
    try {
        // 🔹 Cargamos la venta con todas sus relaciones
        $ventas = Ventas::with([
            'cliente',
            'user',
            'ordentrabajo',
            'servicios.servicio',
            //'tpersonal.tpersonal',
            'transportes.transporte',
            'productos.producto'
            //'condiciones.condiciones'
        ])->findOrFail($id);

        \Log::info('PRODUCTO RELACIONADO:', [
            $ventas->productos->first()?->producto
                ]);

        // 🔹 Estructuramos la respuesta
        $response = [
            'id'            => $ventas->id,
            'cliente_id'    => $ventas->cliente_id,
            'cliente'       => optional($ventas->cliente)->razon_social,
            'user'          => optional($ventas->user)->name,
            //'ot_id'         => optional($cotizacion->ordentrabajo)->id,
            //'ot_codigo'     => optional($cotizacion->ordentrabajo)->codigo,

            //'ot_id' => $cotizacion->orden_trabajo_id,
            //'ot_codigo' => optional($cotizacion->ordentrabajo)->codigo_ot_cliente,

            'ot_codigo'  => $ventas->ot_codigo,
            'ot_cliente' => optional($ventas->ordenTrabajo)->codigo_ot_cliente,

            // 🔥 NUEVOS CAMPOS (CLAVE)
            'comprobante_tipo_codigo' => $ventas->comprobante_tipo_codigo,
            'cotizacion_id'           => $ventas->cotizacion_id,
            'serie'                   => $ventas->serie,
            'correlativo'             => $ventas->correlativo,


            'forma_pago'    => $ventas->forma_pago,
            //'validez'       => $cotizacion->validez,
            //'estado'        => $ventas->estado,
            'total'         => $ventas->total,
            //'garantia'      => $cotizacion->garantia,
            //'tiempo_entrega'=> $cotizacion->tiempo_entrega,
            'observaciones' => $ventas->observaciones,
            'fecha'         => $ventas->fecha,

            // 🔹 Detalles de servicios
            'servicios' => $ventas->servicios->map(function ($item) {
                return [
                    'id'                => $item->id,
                    'servicio_id'       => $item->servicio_id,
                    'descripcion'       => optional($item->servicio)->descripcion,
                    'cantidad'          => $item->cantidad,
                    'precio_unitario'   => $item->precio ?? ($item->cantidad > 0 ? $item->subtotal / $item->cantidad : 0),
                    'subtotal'          => $item->subtotal,
                ];
            }),

            //Detalles de transporte de personal
           'tpersonal' => $ventas->tpersonal->map(function ($item) {
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
            'transporte' => $ventas->transportes->map(function ($item) {
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
            'productos' => $ventas->productos->map(function ($item) {

                $producto = $item->producto;

                return [
                'id'            => $item->id,
                'producto_id'   => $item->producto_id,
                'nombre'        => optional($producto)->nombre,
                'cantidad'      => $item->cantidad,

                // 🔥 Precio guardado en la cotización
                'precio_venta'  => $item->precio ?? ($item->cantidad > 0 ? $item->subtotal / $item->cantidad : 0),
                'subtotal'      => $item->subtotal,
        
                        ];
            }),
        ];

        return response()->json([
            'success' => true,
            'data' => $response
        ], 200);

    } catch (\Exception $e) {
        \Log::error("❌ Error al mostrar cotización: {$e->getMessage()}");
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener los datos de la cotización.'
        ], 500);
    }
}



//     public function porCliente($clienteId)
// {
//     $ordenes = OrdenTrabajo::where('cliente_id', $clienteId)
//         ->select('codigo', 'codigo_ot_cliente')
//         ->get();

//     return response()->json($ordenes);
// }


    /**
     * Store a newly created resource in storage.
     */

public function store(Request $request)
{
    DB::beginTransaction();

    try {

        // ============================
        // 🔥 OBTENER OT DESDE COTIZACIÓN
        // ============================
        if ($request->filled('cotizacion_id')) {

            $cotizacion = \App\Models\Cotizaciones::find($request->cotizacion_id);

            if (!$cotizacion) {
                throw ValidationException::withMessages([
                    'cotizacion_id' => ['Cotización inválida']
                ]);
            }

            // 🔥 FORZAR OT REAL
           $request->merge([
            'ot_codigo' => (string) $cotizacion->ot_codigo
            ]);
        }

        // ============================
        // ✅ VALIDACIÓN
        // ============================
        $data = $this->validateData($request);

        \Log::info('🧾 STORE VENTAS', $request->all());

        // 🔥 Validación extra crítica
        if (empty($data['ot_codigo'])) {
            throw ValidationException::withMessages([
                'ot_codigo' => ['Debe seleccionar una OT válida']
            ]);
        }

        $totalGeneral = 0;

        // ============================
        // 🧾 CREAR VENTA
        // ============================
        $ventas = Ventas::create([
        'cliente_id'              => $data['cliente_id'],
        'ot_codigo'               => $data['ot_codigo'],
        'forma_pago'              => $data['forma_pago'],
        'observaciones'           => $data['observaciones'] ?? null,

            // 🔥 NUEVOS CAMPOS
        'comprobante_tipo_codigo' => $data['comprobante_tipo_codigo'],
        'cotizacion_id'           => $data['cotizacion_id'] ?? null,
        'serie'                   => $data['serie'],
        'correlativo'             => $data['correlativo'],

        'fecha'                   => now(),
        'total'                   => 0,
        'estado'                  => 'Registrado',
        'user_id'                 => auth()->id(),
        ]);

        // ============================
        // 🚚 TRANSPORTE PERSONAL
        // ============================
        if (!empty($data['tpersonal_detalles']) && is_array($data['tpersonal_detalles'])) {
            foreach ($data['tpersonal_detalles'] as $item) {

                if (empty($item['tpersonal_id'])) continue;

                $subtotal = $item['cantidad'] * $item['precio'];

                VentasTPersonal::create([
                    'ventas_id'       => $ventas->id,
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
        if (!empty($data['servicio_detalles']) && is_array($data['servicio_detalles'])) {
            foreach ($data['servicio_detalles'] as $item) {

                if (empty($item['servicio_id'])) continue;

                $subtotal = $item['cantidad'] * $item['precio'];

                VentasServicio::create([
                    'ventas_id'       => $ventas->id,
                    'servicio_id'     => $item['servicio_id'],
                    'cantidad'        => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'subtotal'        => $subtotal,
                ]);

                $totalGeneral += $subtotal;
            }
        }

        // ============================
        // 🚚 TRANSPORTE
        // ============================
        if (!empty($data['transporte_detalles']) && is_array($data['transporte_detalles'])) {
            foreach ($data['transporte_detalles'] as $item) {

                if (empty($item['transporte_id'])) continue;

                $subtotal = $item['cantidad'] * $item['precio'];

                VentasTransporte::create([
                    'ventas_id'       => $ventas->id,
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
        if (!empty($data['material_detalles']) && is_array($data['material_detalles'])) {
            foreach ($data['material_detalles'] as $item) {

                if (empty($item['producto_id'])) continue;

                $subtotal = $item['cantidad'] * $item['precio_venta'];

                VentasProducto::create([
                    'ventas_id'       => $ventas->id,
                    'producto_id'     => $item['producto_id'],
                    'cantidad'        => $item['cantidad'],
                    'precio_unitario' => $item['precio_venta'],
                    'subtotal'        => $subtotal,
                ]);

                $totalGeneral += $subtotal;                
            }

            // ============================
                // 📦 ACTUALIZAR STOCK
                // ============================
                if (!empty($data['material_detalles'])) {
                    \App\Models\Producto::updateStock(false, $data['material_detalles']);
                }
        }

        // ============================
        // 💰 TOTAL
        // ============================
        $ventas->update([
            'total' => $totalGeneral
        ]);

        // ============================
        // 🔄 CAMBIAR ESTADO OT
        // ============================
        $orden = OrdenTrabajo::find($data['ot_codigo']);

        if ($orden) {
            OrdenTrabajoEstadoService::cambiarEstado($orden, 10);
        }

        DB::commit();

        return response()->json([
            'success'   => true,
            'message'   => 'Venta guardada correctamente',
            'ventas_id' => $ventas->id
        ]);

    } 
    catch (ValidationException $e) {

        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Errores de validación',
            'errors'  => $e->errors()
        ], 422);
    } 
    catch (\Exception $e) {

    DB::rollBack();

    \Log::error('❌ Error store ventas', [
        'error' => $e->getMessage(),
        'line'  => $e->getLine()
    ]);

    // 🔥 Si es error de stock, devolvemos el mensaje exacto
    $message = str_contains($e->getMessage(), 'stock') 
                ? $e->getMessage() 
                : 'Error interno del servidor';

    return response()->json([
        'success' => false,
        'message' => $message
    ], 422); // 422 para errores de negocio
}
}

 
private function validateData(Request $request)
{
    $data = $request->validate([
        // 🔹 Cabecera
        'cliente_id' => 'required|exists:clientes,id',
        //'ot_codigo'  => 'required|string|max:50',
        'ot_codigo' => 'nullable|string|max:50',
        'cotizacion_id' => 'required|exists:cotizaciones,id',
        'forma_pago' => 'required|string|in:contado,credito',
        'comprobante_tipo_codigo' => 'required|string|max:2',
        //'cotizacion_id'           => 'nullable|integer',
        'serie'                   => 'required|string|max:10',
        'correlativo'             => 'required|integer',
        'observaciones' => 'nullable|string|min:3', 

        // 🔹 Transporte personal
        'tpersonal_detalles' => 'nullable|array',
        'tpersonal_detalles.*.tpersonal_id' => 'required_with:tpersonal_detalles|integer',
        'tpersonal_detalles.*.cantidad' => 'required|numeric|min:1',
        'tpersonal_detalles.*.precio' => 'required|numeric|min:0',

        // 🔹 Servicios
        'servicio_detalles' => 'nullable|array',
        'servicio_detalles.*.servicio_id' => 'required_with:servicio_detalles|integer',
        'servicio_detalles.*.cantidad' => 'required|numeric|min:1',
        'servicio_detalles.*.precio' => 'required|numeric|min:0',

        // 🔹 Transporte
        'transporte_detalles' => 'nullable|array',
        'transporte_detalles.*.transporte_id' => 'required_with:transporte_detalles|integer',
        'transporte_detalles.*.cantidad' => 'required|numeric|min:1',
        'transporte_detalles.*.precio' => 'required|numeric|min:0',

        // 🔹 Materiales
        'material_detalles' => 'nullable|array',
        'material_detalles.*.producto_id' => 'required_with:material_detalles|integer',
        'material_detalles.*.cantidad' => 'required|numeric|min:1',
        'material_detalles.*.precio_venta' => 'required|numeric|min:0',       

    ]);

    // 🔥 VALIDACIÓN CLAVE: al menos un detalle
    $hasDetails =
        !empty($data['tpersonal_detalles'] ?? []) ||
        !empty($data['servicio_detalles'] ?? []) ||
        !empty($data['transporte_detalles'] ?? []) ||
        !empty($data['material_detalles'] ?? []) ;        

    if (!$hasDetails) {
        throw \Illuminate\Validation\ValidationException::withMessages([
            'detalles' => 'Debe agregar al menos un material, condicion, servicio, transporte o personal.'
        ]);
    }

    return $data;
}

public function getSerie(Request $request)
    {
        $request->validate([
            'comprobante_tipo_codigo' => 'required|exists:comprobante_tipos,codigo'
        ]);

        $codigo = $request->comprobante_tipo_codigo;

        // Buscar directamente en comprobante_series
        $serieConfig = ComprobanteSerie::where('comprobante_tipo_codigo', $codigo)->first();

        // Si no existe, devolver null
        if (!$serieConfig) {
            return response()->json([
                'serie' => null,
                'numero' => null
            ]);
        }
        // Devolver los valores almacenados en la tabla
        return response()->json([
            'serie' => $serieConfig->serie,
            'numero' => $serieConfig->correlativo
        ]);
    }

public function imprimirsinigv($id)
{
    try {
        $ventas = Ventas::with([
            'cliente',
            'user',
            'ordenTrabajo.sedes',
            'ordenTrabajo.responsables',
            'servicios.servicio',
            'tpersonal.tpersonal',
            'transportes.transporte.distrito',
            'productos.producto'
        ])->findOrFail($id);

        $totalServicios  = $ventas->servicios->sum('subtotal');
        $totalTPersonal  = $ventas->tpersonal->sum('subtotal');
        $totalTransporte = $ventas->transportes->sum('subtotal');
        $totalProductos  = $ventas->productos->sum('subtotal');

        $totalGeneral = $totalServicios + $totalTPersonal + $totalTransporte + $totalProductos;

        $opGravada = round($totalGeneral / 1.18, 2);
        $igv       = round($totalGeneral - $opGravada, 2);

        $pdf = Pdf::loadView('ventas.pdfsigv', compact(
            'ventas',
            'totalServicios',
            'totalTPersonal',
            'totalTransporte',
            'totalProductos',
            'totalGeneral',
            'opGravada',
            'igv'
        ));

        return $pdf->stream("Ventas_{$ventas->id}.pdf");

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
        $ventas = Ventas::findOrFail($id);

        // Eliminar detalles relacionados (en cascada)
        $ventas->tpersonal()->delete();
        $ventas->servicios()->delete();
        $ventas->transportes()->delete();
        $ventas->productos()->delete();      

        // Finalmente eliminar la cotización
        $ventas->delete();

        \Log::info("🗑️ Venta eliminada correctamente (ID: {$id})");

        return response()->json([
            'success' => true,
            'message' => 'Venta eliminada correctamente.'
        ]);

    } catch (\Exception $e) {
        \Log::error('❌ Error al eliminar cotización:', ['error' => $e->getMessage()]);

        return response()->json([
            'success' => false,
            'message' => 'Error al eliminar la cotización: ' . $e->getMessage()
        ], 500);
    }
}


public function printTicket($id)
{
    $venta = Ventas::with([
        'cliente',
        'servicios.servicio',
        'tpersonal.tpersonal',
        'transportes.transporte',
        'productos.producto.afectacionTipo'
    ])->findOrFail($id);

    $empresa = (object)[
        'razon_social' => 'Acotec EIRL',
        'direccion' => 'JR. Amazonas Mz. 69 Lt 31 Las Palmeras - Pacasmayo - La Libertad - Perú',
        'ruc' => '20601096391'
    ];

    // =========================
    // 🔹 COLECCIONES SEGURAS (CLAVE 🔥)
    // =========================
    $servicios   = collect($venta->servicios);
    $tpersonal   = collect($venta->tpersonal);
    $transportes = collect($venta->transportes);
    $productos   = collect($venta->productos);

    // =========================
    // 🔹 TOTALES
    // =========================
    $totalServicios  = $servicios->sum('subtotal');
    $totalTPersonal  = $tpersonal->sum('subtotal');
    $totalTransporte = $transportes->sum('subtotal');
    $totalProductos  = $productos->sum('subtotal');

    $totalGeneral = $totalServicios + $totalTPersonal + $totalTransporte + $totalProductos;

    $opGravada = round($totalGeneral / 1.18, 2);
    $igv       = round($totalGeneral - $opGravada, 2);

    // =========================
    // 🔹 QR SUNAT
    // =========================
    $qrContent = implode('|', [
        $empresa->ruc,
        $venta->comprobante_tipo_codigo,
        $venta->serie,
        str_pad($venta->correlativo, 8, '0', STR_PAD_LEFT),
        number_format($igv, 2, '.', ''),
        number_format($totalGeneral, 2, '.', ''),
        optional($venta->fecha ?? $venta->created_at)->format('d/m/Y'),
        optional($venta->cliente)->documento_tipo_codigo,
        optional($venta->cliente)->numero_documento
    ]);

    try {
        $renderer = new ImageRenderer(
            new RendererStyle(120),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        $qrSvg = $writer->writeString($qrContent);

        $qr = base64_encode($qrSvg);
        $qrDataUri = 'data:image/svg+xml;base64,' . $qr;

    } catch (\Exception $e) {
        $qr = null;
        $qrDataUri = null;
    }

    $pdf = Pdf::loadView('ventas.ticket', compact(
        'venta',
        'empresa',
        'qr',
        'qrDataUri',
        'servicios',
        'tpersonal',
        'transportes',
        'productos',
        'totalServicios',
        'totalTPersonal',
        'totalTransporte',
        'totalProductos',
        'totalGeneral',
        'opGravada',
        'igv'
    ))->setPaper([0,0,240,800]);

    return $pdf->stream("ticket_{$venta->id}.pdf");
}







    
}