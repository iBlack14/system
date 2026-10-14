<nav class="app-header navbar navbar-expand bg-white" aria-label="Barra de herramientas">
    <div class="container-fluid">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-header-toggle" data-lte-toggle="sidebar" type="button" aria-label="Alternar menú lateral">
                <i class="bi bi-list"></i>
            </button>
            <div class="header-breadcrumbs d-none d-sm-flex align-items-center gap-2">
                <span class="badge-status-dot"></span>
                <a class="breadcrumb-root" href="{{ route('dashboard') }}">Sistema</a>
                <span class="breadcrumb-separator"><i class="bi bi-chevron-right"></i></span>
                <strong class="breadcrumb-current">Acotec E.I.R.L.</strong>
            </div>
        </div>
        @auth
        <div class="d-flex align-items-center gap-2 ms-auto">
            <div class="dropdown">
                <button class="profile-trigger" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Menú de usuario">
                    <span class="profile-avatar">{{ mb_substr(Auth::user()->name, 0, 1) }}</span>
                    <span class="d-none d-md-block text-start">
                        <strong>{{ Auth::user()->name }}</strong>
                        <small class="text-muted">{{ Auth::user()->getRoleNames()->first() ?? 'Usuario' }}</small>
                    </span>
                    <i class="bi bi-chevron-down d-none d-sm-inline ms-1"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end shadow-sm">
                    <div class="dropdown-header px-3 py-2 border-bottom mb-1">
                        <div class="fw-semibold text-dark">{{ Auth::user()->name }}</div>
                        <div class="small text-muted text-truncate">{{ Auth::user()->email }}</div>
                    </div>
                    <a href="{{ route('perfil.edit') }}" class="dropdown-item py-2">
                        <i class="bi bi-person me-2 text-primary"></i>Mi perfil
                    </a>
                    <div class="dropdown-divider my-1"></div>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="dropdown-item py-2 text-danger" type="submit">
                            <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endauth
    </div>
</nav>
