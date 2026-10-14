<?php

namespace App\Http\Controllers;
use App\Models\ManoObra;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;

class ManoObraController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct(){
        $this->middleware('can:manoobra_list')->only(['index']);
        $this->middleware('can:manoobra_create')->only(['store']);
        $this->middleware('can:manoobra_edit')->only(['show', 'update']);
        $this->middleware('can:manoobra_delete')->only(['destroy']);
    }
    public function index(Request $request)
    {
        //
        
         if ($request->ajax()) {
            $data = ManoObra::select(['id', 'descripcion', 'precio']);

            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    $editButton ='';
                    if(auth()->user()->can('manoobra_edit')){
                        $editButton = view('components.button-edit', ['id' => $row->id])->render();
                    }
                    $deleteButton = '';
                    if(auth()->user()->can('manoobra_delete')){
                        $deleteButton = view('components.button-delete', ['id' => $row->id])->render();
                    }
                    // Combinar ambos botones en una cadena y devolverla
                    return $editButton . $deleteButton;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('manoobra.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
          $data = $this->validateData($request);
        $registro = ManoObra::create($data);
        
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
            $registro = ManoObra::findOrFail($id);
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
         $data = $this->validateData($request, $id);
        $registro = ManoObra::findOrFail($id);
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
            $registro = ManoObra::findOrFail($id);
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
            'descripcion' => 'required|string|max:250',
            'precio' => 'required|numeric|between:0,9999.99'
        ]);
    }

     public function buscar(Request $request)
    {
        $q = $request->input('q');
        return ManoObra::where('descripcion', 'like', "%{$q}%")
                    ->orWhere('precio', 'like', "%{$q}%")
                    ->select('id', 'descripcion','precio')
                    ->limit(10)
                    ->get();
    }
  
}
