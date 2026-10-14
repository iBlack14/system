<?php

namespace App\Http\Controllers;

use App\Models\Bancos;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class BancosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        if ($request->ajax()) {
            $data = Bancos::select(['id', 'descripcion']);

            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    $editButton ='';
                    if(auth()->user()->can('bancos_edit')){
                        $editButton = view('components.button-edit', ['id' => $row->id])->render();
                    }
                    $deleteButton = '';
                    if(auth()->user()->can('bancos_delete')){
                        $deleteButton = view('components.button-delete', ['id' => $row->id])->render();
                    }
                    // Combinar ambos botones en una cadena y devolverla
                    return $editButton . $deleteButton;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('bancos.index');
    }

    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
          $data = $this->validateData($request);
        $registro = Bancos::create($data);
        
        return response()->json([
            'success'=> true,
            'message'=>'Registro creado satisfactoriamente',
            'bancos'=> $registro
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
         try {
            $registro = Bancos::findOrFail($id);
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
        $registro = Bancos::findOrFail($id);
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
            $registro = Bancos::findOrFail($id);
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

    public function getBancos()
{
    return response()->json(
        \App\Models\Bancos::select('id', 'descripcion')->get()
    );
}

 protected function validateData(Request $request, $id = null)
    {
        return $request->validate([
            'descripcion' => 'required|string|max:250'
            
        ]);
    }

     public function buscar(Request $request)
    {
        $q = $request->input('q');
        return ManoObra::where('descripcion', 'like', "%{$q}%")                    
                    ->select('id', 'descripcion')
                    ->limit(10)
                    ->get();
    }
}
