<?php

namespace App\Http\Controllers;

use App\Models\OrdenTrabajo;
use App\Models\OrdenTrabajoEstadoCatalogo;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * 
     * 
     */

    public function __construct()
    {
        $this->middleware('auth');
    }
/* public function index()
{
    // 🔹 Estados del catálogo
    $estados = OrdenTrabajoEstadoCatalogo::orderBy('orden')->get();

    // 🔹 Conteo de OT por estado
    $ordenesPorEstado = OrdenTrabajo::select('estado', DB::raw('COUNT(*) as total'))
        ->groupBy('estado')
        ->pluck('total', 'estado');

    return view('dashboard.index', compact(
        'estados',
        'ordenesPorEstado'
    ));
} */

public function index()
{
    $user = auth()->user();

    if ($user->can('dashboard_index')) {

        $estados = OrdenTrabajoEstadoCatalogo::orderBy('orden')->get();

        $ordenesPorEstado = OrdenTrabajo::selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        return view('dashboard.index', compact('estados', 'ordenesPorEstado'));
    }

    return view('dashboard.bienvenida');
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
}
