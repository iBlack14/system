<?php

namespace App\Http\Controllers;
use App\Models\Responsables;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;

class ResponsablesController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function __construct(){
        $this->middleware('can:responsables_list')->only(['index']);
        $this->middleware('can:responsables_create')->only(['store']);
        $this->middleware('can:responsables_edit')->only(['show', 'update']);
        $this->middleware('can:responsables_delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        //

        if ($request->ajax()) {

        $data = Responsables::join('clientes', 'responsables.cliente_id', '=', 'clientes.id')
            ->select([
                'responsables.id',
                'responsables.nombre',
                'responsables.cargo',
                'responsables.telefono',
                'responsables.email',
                'responsables.activo',
                'clientes.razon_social as cliente'
            ]);

        return DataTables::of($data)
            ->filterColumn('cliente', function ($query, $keyword) {
                $query->where('clientes.razon_social', 'LIKE', "%{$keyword}%");
            })
            ->addColumn('action', function ($row) {
                $editButton = auth()->user()->can('responsables_edit')
                    ? view('components.button-edit', ['id' => $row->id])->render()
                    : '';

                $deleteButton = auth()->user()->can('responsables_delete')
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

    return view('responsables.index');
    }

    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //

        try {
        $validated = $this->validateData($request);

        $responsables = Responsables::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Registro creado satisfactoriamente',
            'responsables' => $responsables
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'errors' => $e->errors(),
            'message' => 'Errores de validación'
        ], 422);
    } catch (\Exception $e) {
        \Log::error('Error al crear el responsable: '.$e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error al crear el responsable'
        ], 500);
    }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //

         try {
        $registro = Responsables::with(['cliente', 'user'])->findOrFail($id);

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
        $registro = Responsables::findOrFail($id);
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
            $registro = Responsables::findOrFail($id);
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
            'cargo' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefono'  => $id ? 'nullable|min:6' : 'required|min:6',
            'activo' => 'required|boolean'  
                   
        ]);
    }

    public function porCliente($clienteId)
{
    return Responsables::where('cliente_id', $clienteId)
        ->select('id', 'nombre', 'cargo')
        ->orderBy('nombre')
        ->get();
}

}
