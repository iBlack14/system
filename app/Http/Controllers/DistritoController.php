<?php

namespace App\Http\Controllers;

use App\Models\Distrito;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;

class DistritoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
       public function select(Request $request)
    {        
        $distritos = Distrito::where('province_id', $province_id)
        ->select('id', 'name')
        ->orderBy('name')        
        ->get();
        return response()->json($distritos);
    }
    
 public function porProvincia($provinceId)
{
    // Normalizamos el ID con ceros a la izquierda
    $provinceId = str_pad($provinceId, 4, '0', STR_PAD_LEFT);

    \Log::info('🔹 provinceId normalizado:', ['provinceId' => $provinceId]);

    $distritos = Distrito::where('province_id', $provinceId)->get(['id', 'name']);

    return response()->json($distritos);
}



}
