<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Iniciar sesión · Acotec</title>
<link rel="icon" href="{{ asset('assets/branding/acotec-mark.png') }}" type="image/png">
<link rel="stylesheet" href="{{ asset('css/adminlte.css') }}">
<link rel="stylesheet" href="{{ asset('bootstrap-icons-1.13.1/bootstrap-icons.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/acotec.css') }}?v={{ filemtime(public_path('css/acotec.css')) }}">
<link rel="stylesheet" href="{{ asset('css/acotec-mobile.css') }}?v={{ filemtime(public_path('css/acotec-mobile.css')) }}">
</head>
<body class="acotec-login">
<main class="login-layout">
<section class="login-story" aria-label="Acotec gestión empresarial">
<a class="login-brand" href="{{ url('/') }}"><img src="{{ asset('assets/branding/acotec-logo.png') }}" alt="Acotec · Automatización y control en la tecnología"></a>
<div class="login-story-content"><span class="eyebrow">GESTIÓN QUE CONECTA</span><h1>Todo tu trabajo.<br>En un mismo lugar.</h1><p>Conecta tus operaciones, organiza tu equipo y mantén el control de cada proyecto.</p>
<div class="story-workflow" aria-label="Áreas de gestión"><div><i class="bi bi-briefcase"></i><span>Comercial</span></div><i class="bi bi-arrow-right" aria-hidden="true"></i><div><i class="bi bi-tools"></i><span>Operaciones</span></div><i class="bi bi-arrow-right" aria-hidden="true"></i><div><i class="bi bi-bar-chart"></i><span>Resultados</span></div></div></div>
<div class="login-story-footer">ACOTEC E.I.R.L. <span>Precisión en cada operación.</span></div>
</section>
<section class="login-entry" aria-labelledby="login-title">
<div class="login-form-wrap"><span class="eyebrow">TU ESPACIO DE TRABAJO</span><h2 id="login-title">Bienvenido de nuevo</h2><p class="text-muted mb-4">Ingresa tus credenciales para continuar.</p>
@if(session('error'))<div class="alert alert-danger" role="alert">{{ session('error') }}</div>@endif
<form action="{{ route('login.post', absolute: false) }}" method="post">@csrf
<div class="mb-4"><label for="loginEmail" class="form-label">Correo electrónico</label><input id="loginEmail" type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="nombre@empresa.com" autocomplete="username" required @error('email') aria-describedby="email-error" aria-invalid="true" @enderror>
@error('email')<div class="invalid-feedback" id="email-error">{{ $message }}</div>@enderror</div>
<div class="mb-4"><label for="loginPassword" class="form-label">Contraseña</label><div class="password-field"><input id="loginPassword" type="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="current-password" required @error('password') aria-describedby="password-error" aria-invalid="true" @enderror><button type="button" class="password-toggle" aria-label="Mostrar contraseña" aria-controls="loginPassword" aria-pressed="false"><i class="bi bi-eye" aria-hidden="true"></i></button></div>
@error('password')<div class="text-danger small mt-2" id="password-error">{{ $message }}</div>@enderror</div>
<button type="submit" class="btn btn-primary w-100 login-submit">Iniciar sesión <i class="bi bi-arrow-right ms-2" aria-hidden="true"></i></button></form>
<p class="login-help"><i class="bi bi-shield-check me-2"></i>Acceso exclusivo para usuarios autorizados.</p></div>
<div class="login-entry-footer">© {{ date('Y') }} Acotec E.I.R.L. · Gestión empresarial</div>
</section></main>
<script src="{{ asset('js/acotec.js') }}?v={{ filemtime(public_path('js/acotec.js')) }}" defer></script>
</body></html>
