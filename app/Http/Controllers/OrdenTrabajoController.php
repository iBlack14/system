<?php

namespace App\Http\Controllers;

use App\Models\OrdenTrabajo;
use App\Models\Tecnicos;
use App\Models\User;
use App\Models\Cliente;
use App\Models\Sedes;
use App\Models\Bancos;
use App\Models\OrdenesServicio;
use App\Models\OrdenTrabajoEstado;
use App\Models\Responsables;
use App\Models\OrdenTrabajoEstadoCatalogo;
use App\Services\OrdenTrabajoEstadoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;


class OrdenTrabajoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     public function __construct(){
        $this->middleware('can:ordenestrabajo_list')->only(['index']);
        $this->middleware('can:ordenestrabajo_create')->only(['store']);
        $this->middleware('can:ordenestrabajo_edit')->only(['show', 'update']);
        $this->middleware('can:ordenestrabajo_delete')->only(['destroy']);
    }


public function index(Request $request)
{
    if ($request->ajax()) {

        $query = OrdenTrabajo::with([
                'cliente',
                'tecnico',
                'user',
                'sedes',
                'responsables',
                'estadoCatalogo',
                'responsables'
            ])
            ->select('ordenes_trabajo.*')
            ->where('estado', '!=', 13) // Oculta solo estado 13
            ->orderByDesc('id');

        return DataTables::of($query)

            ->addColumn('usuario', fn ($row) =>
                optional($row->user)->name ?? '-'
            )

            ->addColumn('cliente', fn ($row) =>
                optional($row->cliente)->razon_social ?? '-'
            )

            ->filterColumn('responsables', function ($query, $keyword) {
                $query->whereHas('responsables', function ($q) use ($keyword) {
                    $q->where('nombre', 'like', "%{$keyword}%");
                });
            })

            ->filterColumn('usuario', function ($query, $keyword) {
                $query->whereHas('user', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })

            ->addColumn('tecnico', fn ($row) =>
                optional($row->tecnico)->nombre ?? '-'
            )

            ->addColumn('sedes', fn ($row) =>
                optional($row->sedes)->nombre ?? '-'
            )

            ->addColumn('responsables', fn ($row) =>
                optional($row->responsables)->nombre ?? '-'
            )

            ->addColumn('estado', function ($row) {

                if (!$row->estadoCatalogo) {
                    return '<span class="badge bg-secondary">Sin estado</span>';
                }

                $color = $row->estadoCatalogo->color;
                $nombre = $row->estadoCatalogo->nombre;
                $colorTexto = $row->estadoCatalogo->color_texto;

                return '<span class="badge bg-' . $color . ' text-' . $colorTexto . ' border border-' . $color . ' fw-semibold">
                    ' . $nombre . '
                </span>';
            })

            ->addColumn('action', function ($row) {

                $buttons = '';

                /* ✏️ EDITAR */
                if (
                    auth()->user()->can('ordenestrabajo_edit') &&
                    $row->estado <= 7
                ) {
                    $buttons .= view('components.button-edit', [
                        'id' => $row->id
                    ])->render();
                }

                /* 🗑 ELIMINAR */
                if (
                    auth()->user()->can('ordenestrabajo_delete') &&
                    $row->estado <= 10
                ) {
                    $buttons .= view('components.button-delete', [
                        'id' => $row->id
                    ])->render();
                }

                /* ================= EJECUTAR OT ================= */
                if (
                    $row->estado >= 3 &&
                    $row->estado <= 8 &&
                    auth()->user()->can('ordenestrabajo_edit')
                ) {
                    $buttons .= '
                        <button class="btn btn-sm btn-info ms-1"
                            title="Ejecutar OT"
                            onclick="ordenTrabajoManager.showEjecutarModal(' . $row->id . ')">
                            <i class="bi bi-check2-square"></i>
                        </button>
                    ';
                }

                /* ================= VER PDF ================= */
                if (
                    $row->estado >= 4 &&
                    $row->estado <= 13 &&
                    !empty($row->archivo_pdf)
                ) {
                    $buttons .= '
                        <a href="' . asset('uploads/ordenes_trabajo/' . $row->archivo_pdf) . '"
                           target="_blank"
                           class="btn btn-sm btn-success ms-1"
                           title="Ver parte firmado">
                            <i class="bi bi-file-earmark-pdf-fill"></i>
                        </a>
                    ';
                }

                /* ================= SUBIR DOCUMENTACIÓN ================= */
                if (
                    $row->estado >= 5 &&
                    $row->estado <= 8 &&
                    auth()->user()->can('ordenestrabajo_edit')
                ) {
                    $buttons .= '
                        <button class="btn btn-sm btn-success-subtle ms-1"
                            title="Subir documentación OT"
                            onclick="ordenTrabajoManager.showDocumentacionModal(' . $row->id . ')">
                            <i class="bi bi-upload"></i>
                        </button>
                    ';
                }

                /* ================= VER FACTURA ================= */
                if (
                    $row->estado >= 9 &&
                    !empty($row->archivo_factura)
                ) {
                    $buttons .= '
                        <a href="' . asset('uploads/ordenes_trabajo/facturacion/' . $row->archivo_factura) . '"
                           target="_blank"
                           class="btn btn-sm btn-success ms-1"
                           title="Ver Factura">
                            <i class="bi bi-coin"></i>
                        </a>
                    ';
                }

                /* ================= VER SUSTENTO ================= */
                if (
                    $row->estado >= 9 &&
                    !empty($row->archivo_sustento)
                ) {
                    $buttons .= '
                        <a href="' . asset('uploads/ordenes_trabajo/facturacion/' . $row->archivo_sustento) . '"
                           target="_blank"
                           class="btn btn-sm btn-warning"
                           title="Ver Sustento">
                            <i class="bi bi-person-vcard-fill"></i>
                        </a>
                    ';
                }

                /* ================= SUBIR FACTURACIÓN ================= */
                if (
                    $row->estado >= 8 &&
                    auth()->user()->can('ordenestrabajo_edit')
                ) {
                    $buttons .= '
                        <button class="btn btn-sm btn-primary ms-1"
                            title="Subir documentos de Facturación"
                            onclick="ordenTrabajoManager.showFacturacionModal(' . $row->id . ')">
                            <i class="bi bi-file-arrow-up"></i>
                        </button>
                    ';
                }

                /* ================= PROCESO DE PAGO ================= */
                if (
                    $row->estado >= 10 &&
                    auth()->user()->can('ordenestrabajo_edit')
                ) {
                    $buttons .= '
                        <button class="btn btn-sm btn-warning ms-1"
                            title="Subir captura de proceso de pago"
                            onclick="ordenTrabajoManager.showProcesopagoModal(' . $row->id . ')">
                            <i class="bi bi-images"></i>
                        </button>
                    ';
                }

                /* ================= REGISTRAR PAGO ================= */
                if (
                    $row->estado >= 12 &&
                    auth()->user()->can('ordenestrabajo_edit')
                ) {
                    $buttons .= '
                        <button class="btn btn-sm btn-success ms-1"
                            title="Registrar pago"
                            onclick="ordenTrabajoManager.showCanceladoModal(' . $row->id . ')">
                            <i class="bi bi-cash-coin"></i>
                        </button>
                    ';
                }

                return $buttons;
            })

            ->rawColumns(['action', 'estado'])
            ->make(true);
    }

    return view('ordenestrabajo.index');
}



 



    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
{
    DB::beginTransaction();

    try {

        $data = $this->validateData($request);
        $data['estado'] = 1;

        $orden = new OrdenTrabajo($data);
        $orden->user_id = auth()->id(); // 🔥 aquí
        $orden->save();

        OrdenTrabajoEstadoService::cambiarEstado($orden, 1);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Orden de Trabajo creada correctamente',
            'ordenestrabajo' => $orden
        ]);

    } catch (\Throwable $e) {

        DB::rollBack();
        \Log::error('Error al crear OT: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Error al registrar la Orden de Trabajo'
        ], 500);
    }
}


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {

            $registro = OrdenTrabajo::with([
                'cliente',
                'tecnico',
                'sedes',
                'responsables',
                'ventas'
            ])
            ->select(
                'ordenes_trabajo.*', // 🔥 IMPORTANTE
                'sede_id',
                'responsable_id'
            )
            ->findOrFail($id);

            return response()->json($registro);

        } catch (\Exception $e) {
            \Log::error("Error en show: " . $e->getMessage());
            return response()->json(['error' => 'Registro no encontrado'], 404);
        }
    }  

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
{
    $data = $this->validateData($request, $id);

    // 🔒 evitar sobrescribir usuario creador
    unset($data['user_id']);

    $registro = OrdenTrabajo::findOrFail($id);
    $registro->update($data);

    return response()->json([
        'success' => true,
        'message' => 'Registro actualizado correctamente'
    ]);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
         try {
            $registro = OrdenTrabajo::findOrFail($id);
            $registro->delete();

            return response()->json([
                'success' => true,
                'message' => 'Registro eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el registro'
            ], 500);
        }
    }

    protected function validateData(Request $request, $id = null)
{
    $validated = $request->validate([
        'codigo_ot_cliente' => 'required|string|max:50',
        'cliente_id'        => 'required|exists:clientes,id',
        'sede_id'           => 'required|exists:sedes,id',
        'responsable_id'    => 'required|exists:responsables,id',
        'tecnico_id'        => 'required|exists:tecnicos,id',
        'motivo'            => 'required|string|max:255',
        'descripcion'       => 'required|string',
        'estado'            => 'required|string|max:50',
        'observaciones'     => 'nullable|string|min:3'
    ]);

    // ✅ SOLO en creación
    if (is_null($id)) {
        $validated['user_id'] = auth()->id();
    }

    return $validated;
}
    public function select(Request $request)
    {
    $clienteId = $request->get('cliente_id');

    $query = OrdenTrabajo::select('id', 'codigo', 'codigo_ot_cliente');

    if ($clienteId) {
        $query->where('cliente_id', $clienteId);
    }

    return response()->json($query->get());
    }

public function porCliente(Request $request, $id)
{
    $query = OrdenTrabajo::with('ordenServicio')
        ->where('cliente_id', $id);

    // 🔹 Filtrar solo si se envía estado
    if ($request->has('estado')) {
        $query->where('estado', $request->estado);
    }

    $ordenes = $query
        ->get()
        ->map(function($ot){
            return [
                'id' => $ot->id,
                'codigo_ot_cliente' => $ot->codigo_ot_cliente,
                'descripcion' => $ot->descripcion,
                'monto' => optional($ot->ordenServicio)->monto ?? 0,
                'codigo_orden_servicio' => optional($ot->ordenServicio)->codigo_orden_servicio,
            ];
        });

    return response()->json($ordenes);
}

public function porClienteNota($clienteId, $notaId)
{
    $ots = OrdenTrabajo::with(['cliente','ordenServicio'])
        ->where('cliente_id', $clienteId)
        ->where(function ($query) use ($notaId) {

            // 🔹 OTs disponibles
            $query->where('estado', 11)

            // 🔹 OTs ya asignadas a ESTA nota
            ->orWhere(function ($q) use ($notaId) {
                $q->where('estado', 12)
                  ->where('nota_credito_id', $notaId);
            });

        })
        ->get()
        ->map(function($ot){
            return [
                'id' => $ot->id,
                'codigo_ot_cliente' => $ot->codigo_ot_cliente,
                'descripcion' => $ot->descripcion,
                'monto' => optional($ot->ordenServicio)->monto ?? 0,
                'codigo_orden_servicio' => optional($ot->ordenServicio)->codigo_orden_servicio,
            ];
        });

    return response()->json($ots);
}


public function porClienteMultiEstado($id)
{
    $ordenes = OrdenTrabajo::where('cliente_id', $id)
        ->whereIn('estado', [1, 4]) // 👈 inicial y final
        ->select('id as codigo', 'codigo_ot_cliente', 'estado')
        ->orderBy('codigo_ot_cliente')
        ->get();

    return response()->json($ordenes);
}



public function porEstado($estado)
{
    $estadoCatalogo = OrdenTrabajoEstadoCatalogo::findOrFail($estado);

    $ordenes = OrdenTrabajo::with([
            'cliente',
            'sedes',
            'estadoCatalogo',
            'informes',
            'ordenServicio',
            'cotizacion',
            'historialEstados',
            'notaCredito',
            'ventas'
        ])
        ->where('estado', $estado)
        ->orderByDesc('id')
        ->get();

    // 🔥 Definimos columnas según estado
    $columnasPorEstado = [

        1 => [ // REGISTRADO
            'codigo_ot_cliente',
            'cliente',
            'sedes',
            'motivo',
            'estado',
            'observaciones',
            'tiempo',
            'tiempo_creacion'
        ],

        2 => [ // ASIGNADO / EN PROCESO
            'codigo_ot_cliente',
            'cliente',
            'sedes',
            'motivo',
            'estado',
            'informe_pdf',
            'tiempo',
            'tiempo_creacion'
        ],

        3 => [ // COTIZADO
            'codigo_ot_cliente',
            'cliente',
            'sedes',
            'motivo',
            'estado',
            'monto_cotizado',
            'ver_pdf',
            'tiempo',
            'tiempo_creacion'
        ],

        4 => [ // EJECUTADO
            'codigo_ot_cliente',
            'cliente',
            'sedes',
            'motivo',
            'estado',
            'parte_firmado_pdf',            
            'tiempo',
            'tiempo_creacion'
        ],

        5 => [
            'codigo_ot_cliente',
            'cliente',
            'sedes',
            'motivo',            
            'estado',
            'informe_pdf',
            'tiempo',
            'tiempo_creacion'
        ],

        6 => [
            'codigo_ot_cliente',
            'cliente',
            'sedes',
            'motivo',
            'estado',
            'doc_cargada',
            'tiempo',
            'tiempo_creacion'
        ],

        7 => [
            'codigo_ot_cliente',
            'codigo_orden_servicio',
            'cliente',    
            'sedes',  
            'motivo',      
            'estado',
            'montoos',
            'pdf_ordenservicio',
            'tiempo',
            'tiempo_creacion'
        ],

        8 => [
            'codigo_ot_cliente',
            'cliente',
            'sedes',
            'motivo',
            'estado',
            'codigo_conformidad',
            'monto',
            'tiempo',
            'tiempo_creacion'
        ],

        
        9 => [
            'codigo_ot_cliente',
            'cliente',
            'sedes',
            'motivo',
            'estado',            
            'visor_factura',
            //'visor_nota_credito',
            'visor_sustento',
            'tiempo',
            'tiempo_creacion'
        ],

        10 => [
            'codigo_ot_cliente',
            'cliente',
            'sedes',
            'comprobante_tipo',
            'serie',
            'correlativo',
            'motivo',
            'estado',
            'total',            
            'tiempo',
            'tiempo_creacion'
        ],


        11 => [
            'codigo_ot_cliente',
            'cliente',
            'sedes',
            'comprobante_tipo',
            'serie',
            'correlativo',
            'motivo',
            'estado',
            'total',
            'visor_subida_portal',
            'tiempo',
            'tiempo_creacion'
        ],

        12 => [
            'codigo_ot_cliente',
            'cliente',
            'sedes',
            'comprobante_tipo',
            'serie',
            'correlativo',
            'motivo',
            'estado',
            'total',
            'archivo_nota',
            'valida_nota',
            'detalles_nota',
            'tiempo',
            'tiempo_creacion'
        ],

        13 => [
            'codigo_ot_cliente',
            'cliente',
            'sedes',
            'comprobante_tipo',
            'serie',
            'correlativo',
            'motivo',
            'estado',            
            'fecha_cancelada',
            'monto_depositado',
            'monto_detraccion',
            'numero_operacion_bancaria',
            'banco',
            'visor_deposito',
            'tiempo',
            'tiempo_creacion'
        ],
    ];

    $columnas = $columnasPorEstado[$estado] ?? [
        'codigo_ot_cliente',
        'cliente',
        'estado'
    ];

    return view('ordenestrabajo.por-estado', compact(
        'ordenes',
        'estadoCatalogo',
        'columnas'
    ));
}




public function ejecutar(Request $request, $id)
{
    $request->validate([
        'archivo' => 'required|mimes:pdf|max:5120'
    ]);

    DB::beginTransaction();

    try {

        $orden = OrdenTrabajo::findOrFail($id);

        // 🔒 VALIDAR ESTADO (solo desde EJECUTANDO = 2)
        /* if ($orden->estado != 3) {
            return redirect()
                ->route('ordenestrabajo.index')
                ->with('error', 'La OT no se puede ejecutar en su estado actual');
        } */

        /* ================= SUBIR PDF ================= */
        if ($request->hasFile('archivo')) {

            $file = $request->file('archivo');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

            $file->move(
                public_path('uploads/ordenes_trabajo'),
                $filename
            );

            $orden->archivo_pdf = $filename;
        }

        /* ================= ACTUALIZAR OT ================= */
        //$orden->estado = 4; // EJECUTADO
        $orden->fecha_atencion = now();
        $orden->save();

        /* ================= HISTORIAL DE ESTADO ================= */
        if ($orden->estado == 3) {
        OrdenTrabajoEstadoService::cambiarEstado($orden, 4);
        }
        DB::commit();

        return redirect()
            ->route('ordenestrabajo.index')
            ->with('success', 'OT ejecutada correctamente');

    } catch (\Throwable $e) {

        DB::rollBack();
        Log::error('Error al ejecutar OT: ' . $e->getMessage());

        return redirect()
            ->route('ordenestrabajo.index')
            ->with('error', 'Error al ejecutar la Orden de Trabajo');
    }
}

public function documentacion(Request $request, $id)
{
    $request->validate([
        'archivo' => 'required|mimes:jpeg,jpg,png|max:5120'
    ]);

    DB::beginTransaction();

    try {

        $orden = OrdenTrabajo::findOrFail($id);

        // 🔒 VALIDAR ESTADO (solo desde EJECUTANDO = 2)
       /*  if ($orden->estado != 5) {
            return redirect()
                ->route('ordenestrabajo.index')
                ->with('error', 'No se puede subir la documentación de la OT en su estado actual');
        } */

        /* ================= SUBIR IMAGEN ================= */
        if ($request->hasFile('archivo')) {

            $file = $request->file('archivo');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

            $file->move(
                public_path('uploads/ot_documentacion'),
                $filename
            );

            $orden->captura_info = $filename;
        }

        /* ================= ACTUALIZAR OT ================= */
        //$orden->estado = 6; // 
        $orden->fecha_atencion = now();
        $orden->save();

        /* ================= HISTORIAL DE ESTADO ================= */
        if ($orden->estado == 5) {
        OrdenTrabajoEstadoService::cambiarEstado($orden, 6);
        }

        DB::commit();

        return redirect()
            ->route('ordenestrabajo.index')
            ->with('success', 'Se subió la documentación correctamente');

    } catch (\Throwable $e) {

        DB::rollBack();
        Log::error('Error al subir la documentación de la OT: ' . $e->getMessage());

        return redirect()
            ->route('ordenestrabajo.index')
            ->with('error', 'Error al subir la documentación de la Orden de Trabajo');
    }
}



public function detalle($id)
{
    $ot = OrdenTrabajo::select(
            'ordenes_trabajo.id',
            'ordenes_trabajo.descripcion',
            'ordenes_trabajo.estado',
            'orden_trabajo_estado_catalogo.nombre as estado_nombre'
        )
        ->leftJoin(
            'orden_trabajo_estado_catalogo',
            'orden_trabajo_estado_catalogo.id',
            '=',
            'ordenes_trabajo.estado'
        )
        ->where('ordenes_trabajo.id', $id)
        ->firstOrFail();

    return response()->json([
        'descripcion'     => $ot->descripcion,
        'estado'          => $ot->estado,          // ← número (1,4,5)
        'estado_nombre'   => $ot->estado_nombre,   // ← texto
    ]);
}




public function facturacion(Request $request, $id)
{
    $request->validate([
        'archivo_factura'  => 'required|mimes:pdf|max:5120',
        'archivo_sustento' => 'required|mimes:pdf|max:5120',
    ]);

    DB::beginTransaction();

    try {

        $orden = OrdenTrabajo::findOrFail($id);

        // 🔒 VALIDAR ESTADO (solo si está CONFORME = 8)
       /*  if ($orden->estado != 8) {
            return redirect()
                ->route('ordenestrabajo.index')
                ->with('error', 'La OT no se puede facturar en su estado actual');
        } */

        //$ruta = public_path('uploads/ordenes_trabajo/facturacion');

        /* ================= SUBIR FACTURA ================= */        

        if ($request->hasFile('archivo_factura')) {

            $file = $request->file('archivo_factura');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

            $file->move(
                public_path('uploads/ordenes_trabajo/facturacion'),
                $filename
            );

            $orden->archivo_factura = $filename;
        }             

        /* ================= SUBIR SUSTENTO ================= */
        if ($request->hasFile('archivo_sustento')) {

            $file = $request->file('archivo_sustento');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

            $file->move(
                public_path('uploads/ordenes_trabajo/facturacion'),
                $filename
            );

            $orden->archivo_sustento = $filename;
        }


        /* ================= ACTUALIZAR OT ================= */
        //$orden->estado = 8; // FACTURADO / EJECUTADO
        //$orden->fecha_atencion = now();
        $orden->save();

        //dd('Estado actual:', $orden->estado);

        /* ================= HISTORIAL ================= */
        if ($orden->estado = 8) {
        OrdenTrabajoEstadoService::cambiarEstado($orden, 9);
        }        

        DB::commit();

        return redirect()
            ->route('ordenestrabajo.index')
            ->with('success', 'Documentos de facturación subidos correctamente');

    } catch (\Throwable $e) {

        DB::rollBack();

        Log::error('Error al subir facturación OT', [
            'ot_id' => $id,
            'error' => $e->getMessage()
        ]);

        return redirect()
            ->route('ordenestrabajo.index')
            ->with('error', 'Error al subir los documentos de facturación');
    }
}

public function notacredito(Request $request, $id)
{
    $request->validate([
        'archivo_nota'  => 'required|mimes:pdf|max:5120',
        'valida_nota' => 'required|mimes:pdf|max:5120',
        'detalles_nota' => 'required|mimes:pdf|max:5120',
    ]);

    DB::beginTransaction();

    try {

        $orden = OrdenTrabajo::findOrFail($id);

        // 🔒 VALIDAR ESTADO (solo si está CONFORME = 8)
       /*  if ($orden->estado != 8) {
            return redirect()
                ->route('ordenestrabajo.index')
                ->with('error', 'La OT no se puede facturar en su estado actual');
        } */

        //$ruta = public_path('uploads/ordenes_trabajo/facturacion');

        /* ================= SUBIR NOTA ================= */
       
        if ($request->hasFile('archivo_nota')) {

            $file = $request->file('archivo_nota');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

            $file->move(
                public_path('uploads/ordenes_trabajo/notacredito'),
                $filename
            );

            $orden->archivo_nota = $filename;
        }

        /* ================= SUBIR VALIDA NOTA ================= */        

        if ($request->hasFile('valida_nota')) {

            $file = $request->file('valida_nota');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

            $file->move(
                public_path('uploads/ordenes_trabajo/notacredito'),
                $filename
            );

            $orden->valida_nota = $filename;
        }             

        /* ================= SUBIR DETALLES NOTA ================= */
        if ($request->hasFile('detalles_nota')) {

            $file = $request->file('detalles_nota');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

            $file->move(
                public_path('uploads/ordenes_trabajo/notacredito'),
                $filename
            );

            $orden->detalles_nota = $filename;
        }


        /* ================= ACTUALIZAR OT ================= */
        //$orden->estado = 8; // FACTURADO / EJECUTADO
        //$orden->fecha_atencion = now();
        $orden->save();

        //dd('Estado actual:', $orden->estado);

        /* ================= HISTORIAL ================= */
        if ($orden->estado = 11) {
        OrdenTrabajoEstadoService::cambiarEstado($orden, 12);
        }        

        DB::commit();

        return redirect()
            ->route('ordenestrabajo.index')
            ->with('success', 'Documentos de nota de crédito subidos correctamente');

    } catch (\Throwable $e) {

        DB::rollBack();

        Log::error('Error al subir documentos nota de crédito', [
            'ot_id' => $id,
            'error' => $e->getMessage()
        ]);

        return redirect()
            ->route('ordenestrabajo.index')
            ->with('error', 'Error al subir los documentos de nota de crédito');
    }
}



public function cancelado(Request $request, $id)
{
    $request->validate([
        'banco_id'       => 'required|exists:bancos,id',
        'monto_pagado'   => 'required|numeric|min:0.01',
        'fecha_pago'     => 'required|date',
        'num_operacion' => 'nullable|string|max:100',
        'monto_detraccion' => 'required|numeric|min:0.00',
        'voucher_pago' => 'required|file|mimes:jpg,jpeg,png|max:5120',
    ]);

    DB::beginTransaction();

    try {

        $orden = OrdenTrabajo::findOrFail($id);

        /* ================= RUTA ================= */
        $ruta = public_path('uploads/ordenes_trabajo/pagos');

        $nombreVoucher = null;

        /* ================= SUBIR VOUCHER ================= */
        if ($request->hasFile('voucher_pago')) {
            $file = $request->file('voucher_pago');

            $nombreVoucher = time().'_voucher_'.uniqid().'.'.$file->getClientOriginalExtension();

            $file->move($ruta, $nombreVoucher);
        }

        /* ================= REGISTRAR PAGO ================= */
        DB::table('pagos')->insert([
            'ot_codigo'      => $orden->id,
            'banco_id'       => $request->banco_id,
            'monto_pagado'   => $request->monto_pagado,
            'fecha_pago'     => $request->fecha_pago,
            'num_operacion'  => $request->num_operacion,
            'voucher_pago'   => $nombreVoucher,
            'monto_detraccion'=>$request->monto_detraccion,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        /* ================= CAMBIO DE ESTADO (OPCIONAL) ================= */
        // Ejemplo: estado 11 = CANCELADO
        // Ajusta según tu lógica
        if ($orden->estado == 12) {
            OrdenTrabajoEstadoService::cambiarEstado($orden, 13);
        }

        DB::commit();

        return redirect()
            ->route('ordenestrabajo.index')
            ->with('success', 'Pago registrado correctamente');

    } catch (\Throwable $e) {

        DB::rollBack();

        Log::error('Error al registrar pago', [
            'ot_id' => $id,
            'error' => $e->getMessage()
        ]);

        return redirect()
            ->route('ordenestrabajo.index')
            ->with('error', 'Error al registrar el pago');
    }
}


public function procesopago(Request $request, $id)
{
    $request->validate([
        'archivo' => 'required|mimes:jpeg,jpg,png|max:5120'
    ]);

    DB::beginTransaction();

    try {

        $orden = OrdenTrabajo::findOrFail($id);

        // 🔒 VALIDAR ESTADO (solo desde EJECUTANDO = 2)
       /*  if ($orden->estado != 9) {
            return redirect()
                ->route('ordenestrabajo.index')
                ->with('error', 'No se puede subir la documentación de la OT en su estado actual');
        } */

        /* ================= SUBIR IMAGEN ================= */
        if ($request->hasFile('archivo')) {

            $file = $request->file('archivo');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

            $file->move(
                public_path('uploads/ordenes_trabajo/procesopago'),
                $filename
            );

            $orden->proceso_pago = $filename;
        }

        /* ================= ACTUALIZAR OT ================= */
        //$orden->estado = 10; // 
        //$orden->fecha_atencion = now();
        $orden->save();

        /* ================= HISTORIAL DE ESTADO ================= */
        if ($orden->estado == 10) {
        OrdenTrabajoEstadoService::cambiarEstado($orden, 11);
        }

        DB::commit();

        return redirect()
            ->route('ordenestrabajo.index')
            ->with('success', 'Se subió la captura de proceso de pago correctamente');

    } catch (\Throwable $e) {

        DB::rollBack();
        Log::error('Error al subir la captura de proceso de pago de la OT: ' . $e->getMessage());

        return redirect()
            ->route('ordenestrabajo.index')
            ->with('error', 'Error al subir la captura de proceso de pago de la Orden de Trabajo');
    }
}

public function informes()
    {
        return $this->hasMany(Informe::class);
    }

public function informeInicial()
    {
        return $this->hasOne(Informe::class)
            ->where('tipo', 'inicial');
    }

public function informeFinal()
    {
        return $this->hasOne(Informe::class)
            ->where('tipo', 'final');
    }

public function timeline($id)
{
    /*
    =====================================
    OBTENER ORDEN
    =====================================
    */

    $orden = OrdenTrabajo::with([
        'cliente',
        'historialEstados.estadoNuevo',
        'historialEstados.usuario',
        'pagos'
    ])->findOrFail($id);

    /*
    =====================================
    ORDENAR HISTORIAL
    =====================================
    */

    $historial = $orden->historialEstados
        ->sortBy('created_at')
        ->values();

    $timeline = [];

    /*
    =====================================
    CONSTRUIR TIMELINE
    =====================================
    */

    for ($i = 0; $i < count($historial); $i++) {

        $actual = $historial[$i];
        $siguiente = $historial[$i + 1] ?? null;

        $estadoId = optional($actual->estadoNuevo)->id;

        /*
        =====================================
        CANCELADO (ESTADO 13)
        =====================================
        */

        if ($estadoId == 13) {

            // INICIO = CREACIÓN DE LA OT (FIJO)
            $inicio = \Carbon\Carbon::parse($orden->created_at);

            // FIN = FECHA REAL DE PAGO
            $fin = !empty($orden->pagos?->fecha_pago)
                ? \Carbon\Carbon::parse($orden->pagos->fecha_pago)
                : now();

        } else {

            /*
            =====================================
            ESTADOS NORMALES
            =====================================
            */

            $inicio = \Carbon\Carbon::parse($actual->created_at);

            $fin = $siguiente
                ? \Carbon\Carbon::parse($siguiente->created_at)
                : now();
        }

        /*
        =====================================
        DURACIÓN REAL
        =====================================
        */

        $minutos = $inicio->diffInMinutes($fin);

        $dias = intdiv($minutos, 1440);
        $resto = $minutos % 1440;

        $horas = intdiv($resto, 60);
        $min = $resto % 60;

        $duracion = '';

        if ($dias > 0) $duracion .= $dias . ' días ';
        if ($horas > 0) $duracion .= $horas . ' horas ';
        if ($min > 0) $duracion .= $min . ' min ';

        if (trim($duracion) === '') {
            $duracion = 'Menos de 1 minuto';
        }

        /*
        =====================================
        FECHA FIN MOSTRAR
        =====================================
        */

        if ($estadoId == 13) {

            $fechaFin = $orden->pagos->fecha_pago
                ? \Carbon\Carbon::parse($orden->pagos->fecha_pago)->format('d/m/Y H:i')
                : 'Sin pago';

        } else {

            $fechaFin = $siguiente
                ? $fin->format('d/m/Y H:i')
                : 'Actualmente';
        }

        /*
        =====================================
        RESULTADO
        =====================================
        */

        $timeline[] = [
            'estado' => optional($actual->estadoNuevo)->nombre ?? 'Sin estado',
            'color' => optional($actual->estadoNuevo)->color ?? 'secondary',
            'fecha_inicio' => $inicio->format('d/m/Y H:i'),
            'fecha_fin' => $fechaFin,
            'duracion' => trim($duracion),
            'usuario' => optional($actual->usuario)->name ?? '-',
        ];
    }

    return view('ordenestrabajo.timeline', compact('orden', 'timeline'));
}

}
