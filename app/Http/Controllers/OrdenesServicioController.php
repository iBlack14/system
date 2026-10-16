<?php

namespace App\Http\Controllers;

use App\Models\OrdenesServicio;
use App\Models\OrdenTrabajo;
use App\Services\OrdenTrabajoEstadoService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrdenesServicioController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct(){
        $this->middleware('can:ordenesservicio_list')->only(['index']);
        $this->middleware('can:ordenesservicio_create')->only(['store']);
        $this->middleware('can:ordenesservicio_edit')->only(['show', 'update']);
        $this->middleware('can:ordenesservicio_delete')->only(['destroy']);
    }
    public function index(Request $request)
    {
        //
        if ($request->ajax()) {
            $data = OrdenesServicio::with([
                'cliente',
                'user',
                'ordenTrabajo'                
                ])
        ->select('ordenes_servicio.*')
        ->orderByDesc('id');

            return DataTables::of($data)
            ->addColumn('usuario', function ($row) {
                    return optional($row->user)->name ?? '-';
                })
                ->addColumn('cliente', function ($row) {
                    return optional($row->cliente)->razon_social ?? '-';
                })

                ->addColumn('codigo_ot_cliente', function ($row) {
                return $row->ordenTrabajo->codigo_ot_cliente ?? '-';
                })
                
                ->addColumn('action', function ($row) {
                $buttons = '';

                if (auth()->user()->can('ordenesservicio_edit') && optional($row->ordenTrabajo)->estado <= 7) {
                    $buttons .= view('components.button-edit', ['id' => $row->id])->render();
                }

                if (auth()->user()->can('ordenesservicio_delete')) {
                    $buttons .= view('components.button-delete', ['id' => $row->id])->render();
                }
               

                if (
                auth()->user()->can('ordenesservicio_edit') &&
                empty($row->codigo_conformidad)
                ) {
                $buttons .= '
                <button class="btn btn-sm btn-success ms-1"
                title="Ingresar Código de Conformidad"
                onclick="ordenesServicioManager.showConformidadOSModal('.$row->id.')">
                <i class="bi bi-patch-check-fill"></i>
                </button>
                ';
                }


                if (!empty($row->archivo_pdf)) {
                $buttons .= '
                <a href="'.asset('uploads/ordenes_servicio/'.$row->archivo_pdf).'"
                download
                class="btn btn-sm btn-danger ms-1"
                title="Descargar PDF de la orden de servicio"
                aria-label="Descargar PDF de la orden de servicio">
                <i class="bi bi-download"></i>
                </a>
                ';
                }

                
                return $buttons;

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

         // Agregar user_id automáticamente
    $data['user_id'] = auth()->id();

        return view('ordenesservicio.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    // La validación debe conservar la respuesta 422 de Laravel para que el
    // formulario pueda señalar los campos incorrectos.
    $data = $this->validateData($request);

    DB::beginTransaction();

    try {

        \Log::info('FILES RECIBIDOS', [
    'files' => $request->allFiles()
]);


        // ============================
        // SUBIR ARCHIVO PDF
        // ============================
        if ($request->hasFile('archivo_pdf')) {

            $file = $request->file('archivo_pdf');

            $nombreArchivo = time() . '_' . uniqid() . '.pdf';

            $file->move(
                public_path('uploads/ordenes_servicio'),
                $nombreArchivo
            );

            // Guardar SOLO el nombre
            $data['archivo_pdf'] = $nombreArchivo;
        }

        // ============================
        // CREAR ORDEN DE SERVICIO
        // ============================
        $registro = OrdenesServicio::create($data);

        // ============================
        // CAMBIAR ESTADO DE LA OT
        // ============================
        $orden = OrdenTrabajo::find($registro->ot_codigo);

        if ($orden) {

            \Log::info('🔄 Cambiando estado OT desde OS', [
                'ot_id' => $orden->id,
                'estado_anterior' => $orden->estado
            ]);

            OrdenTrabajoEstadoService::cambiarEstado($orden, 7);
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Orden de Servicio creada correctamente',
            'ordenesservicio' => $registro
        ]);

    } catch (\Throwable $e) {

        DB::rollBack();

        \Log::error('❌ Error al crear OS con PDF', [
            'error' => $e->getMessage()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error al registrar la Orden de Servicio'
        ], 500);
    }
}


    /**
     * Display the specified resource.
     */
 public function show($id)
{
    try {
        $registro = OrdenesServicio::with([
            'cliente',
            'user',
            'ordenTrabajo'
        ])->findOrFail($id);

        return response()->json([
            'id'               => $registro->id,
            'cliente_id'       => $registro->cliente_id,
            'cliente'          => $registro->cliente,
            'codigo_orden_servicio' => $registro->codigo_orden_servicio,
            'ot_codigo'        => $registro->ot_codigo, // 👈 ES ID
            'ot_codigo_cliente' => optional($registro->ordenTrabajo)->codigo_ot_cliente,
            'ot_descripcion'   => $registro->ordenTrabajo->descripcion ?? null,
            'fecha_entrega'    => $registro->fecha_entrega,
            'forma_pago'       => $registro->forma_pago,
            'lugar_entrega'    => $registro->lugar_entrega,
            'descripcion'      => $registro->descripcion,
            'penalidad'        => $registro->penalidad,
            'monto'            => $registro->monto,
            //'codigo_conformidad'=> $registro->codigo_conformidad,
            'estado'           => $registro->estado,
            'observaciones'    => $registro->observaciones,
            'dias'             => $registro->dias,
            'archivo_pdf'      => $registro->archivo_pdf,
            'archivo_pdf_url'  => $registro->archivo_pdf
                ? asset('uploads/ordenes_servicio/'.$registro->archivo_pdf)
                : null,
        ]);

    } catch (\Exception $e) {
        \Log::error("Error en show OS", ['error' => $e->getMessage()]);
        return response()->json(['error' => 'Registro no encontrado'], 404);
    }
}
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = $this->validateData($request, $id);
        $registro = OrdenesServicio::findOrFail($id);

        if ($request->hasFile('archivo_pdf')) {
            $file = $request->file('archivo_pdf');
            $nombreArchivo = time().'_'.uniqid().'.pdf';
            $file->move(public_path('uploads/ordenes_servicio'), $nombreArchivo);

            if ($registro->archivo_pdf) {
                $anterior = public_path('uploads/ordenes_servicio/'.$registro->archivo_pdf);
                if (is_file($anterior)) {
                    @unlink($anterior);
                }
            }

            $data['archivo_pdf'] = $nombreArchivo;
        } else {
            unset($data['archivo_pdf']);
        }

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
            $registro = OrdenesServicio::findOrFail($id);
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

        'ot_codigo' => 'required|exists:ordenes_trabajo,id',

        // Código de Orden de Servicio del Cliente
        'codigo_orden_servicio' => 'required|string|max:50',

        // descripcion
        'descripcion' => 'required|string|max:255',

        // Forma de pago
        'forma_pago' => 'required|in:contado,credito',

        // Días (solo obligatorio si es crédito)
        'dias' => 'nullable|integer|min:0',

        // Lugar de entrega
        'lugar_entrega' => 'required|string|max:255',

        // Fecha de entrega
        'fecha_entrega' => 'required|date|after_or_equal:today',

        // Penalidad
        'penalidad' => 'required|string|max:255',

        // Estado
        'estado' => 'required|string|max:50',

        // Estado
        'monto' => 'required|numeric|between:0,999999.99',

        // Observaciones
        'observaciones' => 'nullable|string|max:255',

        // Cliente
        'cliente_id' => 'required|exists:clientes,id',

        // archivo_pdf
        'archivo_pdf' => 'nullable|mimes:pdf|max:5120',

    ]);

    // VALIDACIÓN CONDICIONAL PARA CRÉDITO
    if ($request->forma_pago === 'credito') {
        $request->validate([
            'dias' => 'required|integer|min:1'
        ]);

    } else {
        // Si es CONTADO → asegurar que los días sean 0
        $validated['dias'] = 0;
    }

    // Agregar user_id automáticamente
    $validated['user_id'] = auth()->id();

    return $validated;
}

public function dashboard()
{
    $totalOrdenes = OrdenesServicio::count();

    return view('dashboard.index', compact('totalOrdenes'));
}

public function conformidad(Request $request, $id)
{
    // 1️⃣ Validar dato recibido desde el modal
    $request->validate([
        'codigo_conformidados' => 'required|string|max:50'
    ]);

    DB::beginTransaction();

    try {

        // 2️⃣ Buscar Orden de Servicio
        $ordenServicio = OrdenesServicio::findOrFail($id);

        // 3️⃣ Guardar código de conformidad
        $ordenServicio->codigo_conformidad = strtoupper(
            trim($request->codigo_conformidados)
        );        

        $ordenServicio->save();

        // ============================
        // CAMBIAR ESTADO DE LA OT
        // ============================
        $ordenTrabajo = $ordenServicio->ordenTrabajo;

        if ($ordenTrabajo) {

            \Log::info('🔄 Cambiando estado OT desde conformidad OS', [
                'ot_id' => $ordenTrabajo->id,
                'estado_anterior' => $ordenTrabajo->estado
            ]);

            // 8 = CONFORME (según tu sistema)
            OrdenTrabajoEstadoService::cambiarEstado($ordenTrabajo, 8);
        }

        DB::commit();      

        // 5️⃣ Redirección correcta
        return redirect()
            ->route('ordenesservicio.index')
            ->with('success', 'Código de conformidad registrado correctamente');

    } catch (\Throwable $e) {

        DB::rollBack();

        Log::error('Error al registrar conformidad OS', [
            'orden_servicio_id' => $id,
            'error' => $e->getMessage()
        ]);

        return redirect()
            ->route('ordenesservicio.index')
            ->with('error', 'Error al registrar el código de conformidad');
    }
}



}
