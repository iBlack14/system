<?php

namespace App\Http\Controllers;

use App\Models\Tecnicos;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;

class TecnicosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $this->middleware('can:tecnicos_list')->only(['index']);
        $this->middleware('can:tecnicos_create')->only(['store']);
        $this->middleware('can:tecnicos_edit')->only(['show', 'update']);
        $this->middleware('can:tecnicos_delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        //

        if ($request->ajax()) {
             $data = Tecnicos::with('departamento','provincia','distrito')
            ->select([
            'id',
            'dni',
            'nombre',
            'department_id',
            'province_id',
            'districts_id',
            'email',
            'telefono',
            'profesion',
            'puesto',
            'activo'
        ]);

            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    $editButton ='';
                    if(auth()->user()->can('tecnicos_edit')){
                        $editButton = view('components.button-edit', ['id' => $row->id])->render();
                    }
                    $deleteButton = '';
                    if(auth()->user()->can('tecnicos_delete')){
                        $deleteButton = view('components.button-delete', ['id' => $row->id])->render();
                    }
                    // Combinar ambos botones en una cadena y devolverla
                    return $editButton . $deleteButton;
                })
                //->addColumn('profesion', function ($row) {
                 //   return $row->profesion;
                        //? '<span class="badge bg-success">Activo</span>'
                        //: '<span class="badge bg-danger">Inactivo</span>';
               // })
                 ->addColumn('activo', function ($row) {
                    return $row->activo
                        ? '<span class="badge bg-success">Activo</span>'
                        : '<span class="badge bg-danger">Inactivo</span>';
                })
                //->rawColumns(['action'])
                ->rawColumns(['action', 'activo'])
                ->make(true);
        }

        return view('tecnicos.index');
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
    try {
        $validated = $this->validateData($request);

        $tecnico = Tecnicos::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Registro creado satisfactoriamente',
            'tecnico' => $tecnico
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
            'message' => 'Error al crear el técnico'
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
            $registro = Tecnicos::findOrFail($id);
            return response()->json($registro);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Registro no encontrado'], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tecnicos $tecnicos)
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
        $registro = Tecnicos::findOrFail($id);
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
            $registro = Tecnicos::findOrFail($id);
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
        'dni' => [
            'required',
            'digits:8',
            Rule::unique('tecnicos')->ignore($id)
        ],
        'nombre' => 'required|string|max:255',
        'department_id' => 'required|string|max:2',
        'province_id' => 'required|string|max:4',
        'districts_id' => 'required|string|max:6',
        'email' => [
            'nullable', // puedes poner 'required' si lo haces obligatorio
            'email',
            'max:255',
            Rule::unique('tecnicos')->ignore($id)
        ],
        'telefono' => $id ? 'nullable|min:6' : 'required|min:6',
        'profesion' => 'required|string|max:255',
        'puesto' => 'required|string|max:50',
        'activo' => 'required|boolean',
    ]);
}
     public function buscar(Request $request)
    {
        $q = $request->input('q');
        return Tecnicos::where('nombre', 'like', "%{$q}%")
                    ->orWhere('dni', 'like', "%{$q}%")
                    ->select('id', 'dni', 'nombre')
                    ->limit(10)
                    ->get();
    }
}
