@extends('plantilla.app')
@section('titulo', 'Vista general · Acotec')
@section('contenido')
<div class="container-fluid">
<div class="page-intro"><div><span class="eyebrow">ESPACIO DE TRABAJO</span><h1>Vista general</h1><p>Todo listo para continuar con tu día.</p></div><span class="date-chip"><i class="bi bi-calendar3 me-2"></i>{{ now()->format('d / m / Y') }}</span></div>
<section class="welcome-panel"><div><span class="eyebrow">BIENVENIDO A ACOTEC</span><h2>Hola, {{ auth()->user()->name }}.</h2><p>Una visión clara. Un equipo conectado.<br>Elige un área y continúa donde lo dejaste.</p></div><div class="welcome-symbol" aria-hidden="true"><i class="bi bi-grid-1x2"></i></div></section>
@include('dashboard.quick-links')
<div class="workspace-note"><i class="bi bi-info-circle"></i><span>Los módulos disponibles corresponden a los permisos de tu cuenta.</span><a href="{{ route('perfil.edit') }}">Ver mi perfil <i class="bi bi-arrow-right"></i></a></div>
</div>
@endsection
