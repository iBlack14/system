<?php

namespace App\Http\Controllers;
use App\Models\Transporte;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;

class TransporteController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct(){
    $this->middleware('can:transporte_list')->only(['index']);
    $this->middleware('can:transporte_create')->only(['store']);
    $this->middleware('can:transporte_edit')->only(['show', 'update']);
    $this->middleware('can:transporte_delete')->only(['destroy']);
    }

  public function index(Request $request)
{
    if ($request->ajax()) {
    $data = Transporte::select([
        'transporte.id',
        'ubigeo_peru_departments.name as department_name',
        'ubigeo_peru_provinces.name as province_name',
        'ubigeo_peru_districts.name as district_name','transporte.unidad_codigo',
        'transporte.precio'
    ])
    ->leftJoin('ubigeo_peru_departments', 'transporte.department_id', '=', 'ubigeo_peru_departments.id')
    ->leftJoin('ubigeo_peru_provinces', 'transporte.province_id', '=', 'ubigeo_peru_provinces.id')
    ->leftJoin('ubigeo_peru_districts', 'transporte.districts_id', '=', 'ubigeo_peru_districts.id');

    return DataTables::of($data)
        ->filter(function ($query) use ($request) {
            if ($search = $request->get('search')['value']) {
                $search = strtolower($search);
                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(ubigeo_peru_departments.name) LIKE ?', ["%{$search}%"])
                      ->orWhereRaw('LOWER(ubigeo_peru_provinces.name) LIKE ?', ["%{$search}%"])
                      ->orWhereRaw('LOWER(ubigeo_peru_districts.name) LIKE ?', ["%{$search}%"])
                      ->orWhereRaw('CAST(transporte.precio AS CHAR) LIKE ?', ["%{$search}%"])
                      ->orWhereRaw('CAST(transporte.id AS CHAR) LIKE ?', ["%{$search}%"]);
                });
            }
        })
        ->addColumn('action', function ($row) {
            $editButton = '';
            if (auth()->user()->can('transporte_edit')) {
                $editButton = view('components.button-edit', ['id' => $row->id])->render();
            }
            $deleteButton = '';
            if (auth()->user()->can('transporte_delete')) {
                $deleteButton = view('components.button-delete', ['id' => $row->id])->render();
            }
            return $editButton . $deleteButton;
        })
        ->rawColumns(['action'])
        ->make(true);
}


    return view('transporte.index');
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
        $registro = Transporte::create($data);
        
        return response()->json([
            'success'=> true,
            'message'=>'Registro creado satisfactoriamente',
            'transporte'=> $registro
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
          try {
            $registro = Transporte::findOrFail($id);
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
        $registro = Transporte::findOrFail($id);
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
            $registro = Transporte::findOrFail($id);
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
            'department_id' => 'required|string|max:2',
            'province_id' => 'required|string|max:4',
            'districts_id' => 'required|string|max:6',
            'unidad_codigo' => 'required|string|max:6',
            'precio' => 'required|numeric|between:0,9999.99'
        ]);
    }

    /**
 * Buscar transportes por texto (para autocompletar o selección rápida)
 */
public function buscar(Request $request)
{
    // Detectamos el término sin importar el nombre del parámetro
    $term = strtolower($request->get('term') ?? $request->get('q') ?? $request->get('search') ?? '');

    $query = Transporte::select(
            'transporte.id',
            'ubigeo_peru_departments.name as department_name',
            'ubigeo_peru_provinces.name as province_name',
            'ubigeo_peru_districts.name as district_name',
            'transporte.precio'
        )
        ->leftJoin('ubigeo_peru_departments', 'transporte.department_id', '=', 'ubigeo_peru_departments.id')
        ->leftJoin('ubigeo_peru_provinces', 'transporte.province_id', '=', 'ubigeo_peru_provinces.id')
        ->leftJoin('ubigeo_peru_districts', 'transporte.districts_id', '=', 'ubigeo_peru_districts.id');

    if ($term !== '') {
        $query->where(function ($q) use ($term) {
            $q->whereRaw('LOWER(ubigeo_peru_departments.name) LIKE ?', ["%{$term}%"])
              ->orWhereRaw('LOWER(ubigeo_peru_provinces.name) LIKE ?', ["%{$term}%"])
              ->orWhereRaw('LOWER(ubigeo_peru_districts.name) LIKE ?', ["%{$term}%"])
              ->orWhereRaw('CAST(transporte.precio AS CHAR) LIKE ?', ["%{$term}%"])
              ->orWhereRaw('CAST(transporte.id AS CHAR) LIKE ?', ["%{$term}%"]);
        });
    }

    $resultados = $query->limit(15)->get();

    $response = $resultados->map(function ($item) {
        return [
            'id' => $item->id,
            'label' => "{$item->department_name} / {$item->province_name} / {$item->district_name} - S/ {$item->precio}",
            'precio' => $item->precio,
        ];
    });

    return response()->json($response);
}




}
