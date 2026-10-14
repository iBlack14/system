<?php

namespace App\Http\Controllers;

use App\Models\NotaCredito;
use App\Models\OrdenTrabajo;
use App\Services\OrdenTrabajoEstadoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;


class NotaCreditoController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct(){
        $this->middleware('can:notacredito_list')->only(['index']);
        $this->middleware('can:notacredito_create')->only(['store']);
        $this->middleware('can:notacredito_edit')->only(['show','update']);
        $this->middleware('can:notacredito_delete')->only(['destroy']);
    }



    public function index(Request $request)
    {
        //

        if ($request->ajax()) {

            $data = NotaCredito::with('ordenesTrabajo.cliente')
                ->select('*')
                ->orderBy('id','desc');

            return DataTables::of($data)

            ->addColumn('archivo', function($row){
                return $row->archivo_nota ?? '-';
            })

            ->filterColumn('cliente', function ($query, $keyword) {
                $query->whereHas('ordenesTrabajo.cliente', function ($q) use ($keyword) {
                    $q->where('razon_social', 'like', "%{$keyword}%");
                });
            })

            ->filterColumn('ots', function ($query, $keyword) {
                $query->whereHas('ordenesTrabajo', function ($q) use ($keyword) {
                    $q->where('codigo_ot_cliente', 'like', "%{$keyword}%");
                });
            })

            ->filterColumn('serie', function ($query, $keyword) {
                $query->where('serie', 'like', "%{$keyword}%");
            })

            ->filterColumn('correlativo', function ($query, $keyword) {
                $query->where('correlativo', 'like', "%{$keyword}%");
            })

            ->addColumn('cantidad_ots', function($row){
                return $row->ordenesTrabajo->count();
            })

            ->addColumn('cliente', function($row){

                if ($row->ordenesTrabajo->isNotEmpty()) {
                    return $row->ordenesTrabajo->first()->cliente->razon_social ?? '-';
                }

                return '-';
            })

            ->addColumn('archivo', function($row){
                if ($row->archivo_nota) {
                    return '<a href="'.asset('storage/'.$row->archivo_nota).'" 
                    target="_blank" 
                    title="Ver Nota de Crédito">
                    <i class="bi bi-file-earmark-pdf text-danger" style="font-size:18px;"></i>
                    </a>';
                }

                    return '<span class="text-muted">-</span>';
            })

            ->addColumn('valida_nota', function($row){
                if ($row->valida_nota) {
                    return '<a href="'.asset('storage/'.$row->valida_nota).'" 
                    target="_blank" 
                    title="Ver validación de Nota de Crédito">
                    <i class="bi bi-shield-check text-success" style="font-size:18px;"></i>
                    </a>';
                }

            return '<span class="text-muted">-</span>';
            })

            ->addColumn('detalles_nota', function($row){
                if ($row->detalles_nota) {
                    return '<a href="'.asset('storage/'.$row->detalles_nota).'" 
                                target="_blank" 
                                title="Ver detalle de Nota de Crédito">
                                <i class="bi bi-file-earmark-text text-primary" style="font-size:18px;"></i>
                            </a>';
                }

                return '<span class="text-muted">-</span>';
            })

            ->addColumn('ots', function($row){

                if ($row->ordenesTrabajo->isEmpty()) {
                    return '<span class="text-muted">Sin OTs</span>';
                }

                $html = '';

                foreach ($row->ordenesTrabajo as $ot) {
                    $html .= '<span class="badge bg-primary me-1">'
                        . e($ot->codigo_ot_cliente)
                        . '</span>';
                }

                return $html;
            })

            ->addColumn('action', function ($row) {

                $buttons = '';

                if (auth()->user()->can('notacredito_edit')) {
                    $buttons .= view('components.button-edit', ['id' => $row->id])->render();
                }

                if (auth()->user()->can('notacredito_delete')&& optional($row->ordenTrabajo)->estado <= 13) {
                    $buttons .= view('components.button-delete', ['id' => $row->id])->render();
                }                          

                return $buttons;
            })

            ->rawColumns(['action','archivo','valida_nota','detalles_nota','cliente','ots'])
            ->make(true);
        }

        return view('notacredito.index');
    }

    

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
{
    DB::beginTransaction();

    try {

        $request->validate([
            'serie'          => 'required|string|max:10',
            'correlativo'    => 'required|string|max:20',
            'archivo_nota'   => 'required|file|mimes:pdf|max:5120',
            'valida_nota'    => 'required|file|mimes:pdf|max:5120',
            'detalles_nota'  => 'required|file|mimes:pdf|max:5120',
            'monto_nota'     => 'nullable|numeric|min:0',
            'ots'   => 'required|array|min:1',
            'ots.*' => 'required|integer|exists:ordenes_trabajo,id'
        ]);
        

        if (NotaCredito::where('serie',$request->serie)
            ->where('correlativo',$request->correlativo)
            ->exists()) {

            return response()->json([
                'success' => false,
                'message' => 'Ya existe la nota'
            ], 422);
        }

        /* archivos */
        $rutaNota = $request->file('archivo_nota')?->store('notas_credito','public');
        $rutaValida = $request->file('valida_nota')?->store('notas_credito/validaciones','public');
        $rutaDetalle = $request->file('detalles_nota')?->store('notas_credito/detalles','public');

        $nota = NotaCredito::create([
            'serie' => $request->serie,
            'correlativo' => $request->correlativo,
            'fecha_registro' => now(),
            'monto_nota' =>$request->monto_nota,
            'archivo_nota' => $rutaNota,
            'valida_nota' => $rutaValida,
            'detalles_nota' => $rutaDetalle,
        ]);   

        $ots = OrdenTrabajo::whereIn('id', $request->ots)->get();

        if ($ots->count() !== count($request->ots)) {
            throw new \Exception('Una o más OTs no existen');
        }    

        foreach ($ots as $ot) {

            // 🔥 obtener monto desde orden de servicio
            $monto = optional($ot->ordenServicio)->monto ?? 0;

            // 🔥 cálculo rebate (5% sin IGV)
            $sinIGV = $monto / 1.18;
            $rebate = $sinIGV * 0.05;

            // 🔥 asignar datos
            $ot->nota_credito_id = $nota->id;
            $ot->rebate = $rebate * 1.18;
            $ot->save();

            OrdenTrabajoEstadoService::cambiarEstado($ot, 12);
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Nota registrada correctamente'
        ]);

    } catch (\Throwable $e) {

        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Error al guardar',
            'error' => $e->getMessage()
        ], 500);
    }
}


    /**
     * Display the specified resource.
     */
    public function show($id)
{
    $nota = NotaCredito::with([
        'ordenesTrabajo.ordenServicio', // 🔥 trae OS
        'ordenesTrabajo.cliente'
    ])->findOrFail($id);

    return response()->json([
        'id' => $nota->id,
        'serie' => $nota->serie,
        'correlativo' => $nota->correlativo,
        'monto_nota' => $nota->monto_nota,

        'ordenes_trabajo' => $nota->ordenesTrabajo->map(function ($ot) {
            return [
                'id' => $ot->id,
                'codigo_ot_cliente' => $ot->codigo_ot_cliente,
                'descripcion' => $ot->descripcion,

                // 🔥 VIENE DE ORDEN SERVICIO
                'codigo_orden_servicio' => optional($ot->ordenServicio)->codigo_orden_servicio,
                'monto' => optional($ot->ordenServicio)->monto,

                'cliente' => $ot->cliente
            ];
        })
    ]);
}

    /**
     * Update the specified resource in storage.
     */

public function update(Request $request, $id)
{
    DB::beginTransaction();

    try {

        $nota = NotaCredito::findOrFail($id);

        // ✅ VALIDACIÓN
        $request->validate([
            'serie'          => 'required',
            'correlativo'    => 'required',
            'monto_nota'     => 'nullable|numeric',
            // 🔥 NUEVO
            'ots'            => 'required|array|min:1',
            'ots.*'          => 'exists:ordenes_trabajo,id',
            'archivo_nota'   => 'nullable|mimes:pdf|max:5120',
            'valida_nota'    => 'nullable|mimes:pdf|max:5120',
            'detalles_nota'  => 'nullable|mimes:pdf|max:5120',
        ]);

        // ✅ DATOS BÁSICOS
        $nota->update([
            'serie'        => $request->serie,
            'correlativo'  => $request->correlativo,
            'monto_nota'   => $request->monto_nota,
        ]);

        /* ================= ARCHIVO NOTA ================= */
        if ($request->hasFile('archivo_nota')) {

            if ($nota->archivo_nota) {
                Storage::disk('public')->delete($nota->archivo_nota);
            }

            $ruta = $request->file('archivo_nota')
                ->store('notas_credito', 'public');

            $nota->archivo_nota = $ruta;
        }

        /* ================= VALIDACIÓN ================= */
        if ($request->hasFile('valida_nota')) {

            if ($nota->valida_nota) {
                Storage::disk('public')->delete($nota->valida_nota);
            }

            $ruta = $request->file('valida_nota')
                ->store('notas_credito/validaciones', 'public');

            $nota->valida_nota = $ruta;
        }

        /* ================= DETALLE ================= */
        if ($request->hasFile('detalles_nota')) {

            if ($nota->detalles_nota) {
                Storage::disk('public')->delete($nota->detalles_nota);
            }

            $ruta = $request->file('detalles_nota')
                ->store('notas_credito/detalles', 'public');

            $nota->detalles_nota = $ruta;
        }

        // 🔥 GUARDAR CAMBIOS
        $nota->save();

        // ================= OTs =================

        // 🔹 obtener OTs seleccionadas
        $otsSeleccionadas = $request->input('ots', []);

        // 🔹 1. quitar OTs que ya no están seleccionadas
        OrdenTrabajo::where('nota_credito_id', $nota->id)
            ->whereNotIn('id', $otsSeleccionadas)
            ->update([
                'nota_credito_id' => null,
                'estado' => 11 // 🔥 vuelve a disponible
            ]);

        // 🔹 2. asignar nuevas OTs
        if (!empty($otsSeleccionadas)) {

            OrdenTrabajo::whereIn('id', $otsSeleccionadas)
                ->update([
                    'nota_credito_id' => $nota->id,
                    'estado' => 12 // 🔥 asignada
                ]);
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Nota actualizada correctamente'
        ]);

    } catch (\Throwable $e) {

        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Error al actualizar',
            'error'   => $e->getMessage()
        ], 500);
    }
}

    /**
     * Remove the specified resource from storage.
     */

    public function destroy($id)
{
    DB::beginTransaction();

    try {

        $nota = NotaCredito::with('ordenesTrabajo')->findOrFail($id);

        // 🔥 1. Cambiar estado OT (12 → 11)
        foreach ($nota->ordenesTrabajo as $ot) {
            if ($ot->estado == 12) {
                OrdenTrabajoEstadoService::cambiarEstado($ot, 11);
            }
        }

        // 🔥 2. FORZAR liberación en BD (clave para evitar el error)
        OrdenTrabajo::where('nota_credito_id', $nota->id)
            ->update(['nota_credito_id' => null]);

        // 🔥 3. Eliminar archivos
        if ($nota->archivo_nota) {
            Storage::disk('public')->delete($nota->archivo_nota);
        }

        if ($nota->valida_nota) {
            Storage::disk('public')->delete($nota->valida_nota);
        }

        if ($nota->detalles_nota) {
            Storage::disk('public')->delete($nota->detalles_nota);
        }

        // 🔥 4. Eliminar nota
        $nota->delete();

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Nota eliminada y OTs liberadas correctamente'
        ]);

    } catch (\Throwable $e) {

        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Error al eliminar',
            'error'   => $e->getMessage()
        ], 500);
    }
}

}
