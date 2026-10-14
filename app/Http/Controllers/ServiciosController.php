<?php

namespace App\Http\Controllers;
use App\Models\Servicios;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;

class ServiciosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $this->middleware('can:servicios_list')->only(['index']);
        $this->middleware('can:servicios_create')->only(['store']);
        $this->middleware('can:servicios_edit')->only(['show', 'update']);
        $this->middleware('can:servicios_delete')->only(['destroy']);
    }


    public function index(Request $request)
    {
        //
        if ($request->ajax()) {
            $data = Servicios::select(['id', 'descripcion', 'unidad_codigo','precio']);

            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    $editButton ='';
                    if(auth()->user()->can('servicios_edit')){
                        $editButton = view('components.button-edit', ['id' => $row->id])->render();
                    }
                    $deleteButton = '';
                    if(auth()->user()->can('servicios_delete')){
                        $deleteButton = view('components.button-delete', ['id' => $row->id])->render();
                    }
                    // Combinar ambos botones en una cadena y devolverla
                    return $editButton . $deleteButton;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('servicios.index');
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
        $registro = Servicios::create($data);
        
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
            $registro = Servicios::findOrFail($id);
            return response()->json($registro);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Registro no encontrado'], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
         $data = $this->validateData($request, $id);
        $registro = Servicios::findOrFail($id);
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
            $registro = Servicios::findOrFail($id);
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
        return Servicios::where('descripcion', 'like', "%{$q}%")
                    ->orWhere('precio', 'like', "%{$q}%")
                    ->select('id', 'descripcion','precio')
                    ->limit(10)
                    ->get();
    }

     protected function validateData(Request $request, $id = null)
    {
        return $request->validate([
            'descripcion' => 'required|string|max:250',
            'unidad_codigo' =>'required|string|max:3',
            'precio' => 'required|numeric|between:0,9999.99'
        ]);
    }
}
