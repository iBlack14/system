<?php

namespace App\Http\Controllers;
use App\Models\Sedes;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;

class SedesController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct(){
        $this->middleware('can:sedes_list')->only(['index']);
        $this->middleware('can:sedes_create')->only(['store']);
        $this->middleware('can:sedes_edit')->only(['show', 'update']);
        $this->middleware('can:sedes_delete')->only(['destroy']);
    }

    public function index(Request $request)
{
    if ($request->ajax()) {

        $data = Sedes::join('clientes', 'sedes.cliente_id', '=', 'clientes.id')
            ->select([
                'sedes.id',
                'sedes.nombre',
                'sedes.direccion',
                'sedes.telefono',
                'sedes.activo',
                'clientes.razon_social as cliente'
            ]);

        return DataTables::of($data)
            ->filterColumn('cliente', function ($query, $keyword) {
                $query->where('clientes.razon_social', 'LIKE', "%{$keyword}%");
            })
            ->addColumn('action', function ($row) {
                $editButton = auth()->user()->can('sedes_edit')
                    ? view('components.button-edit', ['id' => $row->id])->render()
                    : '';

                $deleteButton = auth()->user()->can('sedes_delete')
                    ? view('components.button-delete', ['id' => $row->id])->render()
                    : '';

                return $editButton . $deleteButton;
            })

            ->addColumn('activo', function ($row) {
                    return $row->activo
                        ? '<span class="badge bg-success">Activo</span>'
                        : '<span class="badge bg-danger">Inactivo</span>';
                })
                //->rawColumns(['action'])
                ->rawColumns(['action', 'activo'])
                ->make(true);
    }

    return view('sedes.index');
}
 

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
            try {
        $validated = $this->validateData($request);

        $sedes = Sedes::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Registro creado satisfactoriamente',
            'sedes' => $sedes
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'errors' => $e->errors(),
            'message' => 'Errores de validación'
        ], 422);
    } catch (\Exception $e) {
        \Log::error('Error al crear técnico: '.$e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error al crear la Sede'
        ], 500);
    }



    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        //
        try {
        $registro = Sedes::with(['cliente', 'user'])->findOrFail($id);

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
        //

         $data = $this->validateData($request, $id);
        $registro = Sedes::findOrFail($id);
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
            $registro = Sedes::findOrFail($id);
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
        return $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'nombre' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'telefono' => 'nullable|min:6',
            'activo' => 'required|boolean'        
        ]);
    }

    public function porCliente($clienteId)
    {
    return Sedes::where('cliente_id', $clienteId)
        ->select('id', 'nombre')
        ->orderBy('nombre')
        ->get();
    }

    // SedesController.php
    public function buscarPorCliente(Request $request)
    {
    $q = $request->input('q');
    $clienteId = $request->input('cliente_id');

    return Sedes::where('cliente_id', $clienteId)
        ->where('nombre', 'like', "%{$q}%")
        ->select('id', 'nombre', 'direccion')
        ->limit(10)
        ->get();
    }



}
