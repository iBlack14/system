<?php

namespace App\Http\Controllers;
use App\Models\Condiciones;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;

class CondicionesController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct(){
        $this->middleware('can:condiciones_list')->only(['index']);
        $this->middleware('can:condiciones_create')->only(['store']);
        $this->middleware('can:condiciones_edit')->only(['show', 'update']);
        $this->middleware('can:condiciones_delete')->only(['destroy']);
    }


    public function index(Request $request)
    {
        //
         if ($request->ajax()) {
            $data = Condiciones::select(['id', 'descripcion']);

            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    $editButton ='';
                    if(auth()->user()->can('condiciones_edit')){
                        $editButton = view('components.button-edit', ['id' => $row->id])->render();
                    }
                    $deleteButton = '';
                    if(auth()->user()->can('condiciones_delete')){
                        $deleteButton = view('components.button-delete', ['id' => $row->id])->render();
                    }
                    // Combinar ambos botones en una cadena y devolverla
                    return $editButton . $deleteButton;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('condiciones.index');
    }   

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
         $data = $this->validateData($request);
        $registro = Condiciones::create($data);
        
        return response()->json([
            'success'=> true,
            'message'=>'Registro creado satisfactoriamente',
            'manoobra'=> $registro
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
          try {
            $registro = Condiciones::findOrFail($id);
            return response()->json($registro);
        } catch (\Exception $e) {
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
        $registro = Condiciones::findOrFail($id);
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
            $registro = Condiciones::findOrFail($id);
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

     public function buscar(Request $request)
    {
        $q = $request->input('q');
        return Condiciones::where('descripcion', 'like', "%{$q}%")                    
                    ->select('id', 'descripcion')
                    ->limit(10)
                    ->get();
    }

     protected function validateData(Request $request, $id = null)
    {
        return $request->validate([
            'descripcion' => 'required|string|max:255',           
        ]);
    }
}
