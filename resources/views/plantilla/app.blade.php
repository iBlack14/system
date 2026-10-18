<!doctype html>
<html lang="es">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>@yield('titulo','Sistema - Acotec E.I.R.L.')</title>
    <!--begin::Primary Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="title" content="Sistema - Acotec" />
    <meta name="author" content="Acotec" />
    <meta
      name="description"
      content="Sistema de Ventas en Laravell."
    />
    <meta
      name="keywords"
      content="Sistema en laravel, Sistema de Ventas"
    />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!--end::Primary Meta Tags-->
    <link rel="icon" href="{{ asset('assets/branding/acotec-mark.png') }}" type="image/png">
    <!--begin::Fonts-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!--end::Fonts-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link
      rel="stylesheet"
      href="{{asset('css/overlayscrollbars.min.css')}}"
    />
    <!--end::Third Party Plugin(OverlayScrollbars)-->
    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link
      rel="stylesheet"
      href="{{asset('bootstrap-icons-1.13.1/bootstrap-icons.min.css')}}"
    />
    <!--end::Third Party Plugin(Bootstrap Icons)-->
    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="{{asset('css/adminlte.css')}}" />
    <!--end::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="{{asset('datatables/dataTables.bootstrap5.css')}}">
    @stack('estilos')
    <link rel="stylesheet" href="{{ asset('css/acotec.css') }}?v={{ filemtime(public_path('css/acotec.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/acotec-mobile.css') }}?v={{ filemtime(public_path('css/acotec-mobile.css')) }}">
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg sidebar-mini bg-body-tertiary app-loaded acotec-app">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      @include('plantilla.header')
      <!--end::Header-->
      <!--begin::Sidebar-->
      @include('plantilla.menu')
      <!--end::Sidebar-->
      <!--begin::App Main-->
      <main class="app-main" id="contenido-principal" tabindex="-1">
        <!--begin::App Content Header-->
        <div class="app-content-header">
          <!--begin::Container-->
          <div class="container-fluid">
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content Header-->
        <!--begin::App Content-->
        <div class="app-content">
          <!--begin::Container-->
          @yield('contenido')
          <!--end::Container-->
        </div>
        <!--end::App Content-->
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      @include('plantilla.footer')
      <!--end::Footer-->
      <nav class="mobile-bottom-nav" aria-label="Accesos rápidos móvil">
        <a href="{{ route('dashboard') }}"><i class="bi bi-grid-1x2-fill"></i><span>Inicio</span></a>
        @can('ordenestrabajo_list')<a href="{{ route('ordenestrabajo.index') }}"><i class="bi bi-clipboard-check"></i><span>OT</span></a>@endcan
        @can('ventas_list')<a href="{{ route('ventas.index') }}"><i class="bi bi-receipt"></i><span>Ventas</span></a>@endcan
        @can('clientes_list')<a href="{{ route('clientes.index') }}"><i class="bi bi-people"></i><span>Clientes</span></a>@endcan
        <button type="button" data-lte-toggle="sidebar"><i class="bi bi-three-dots"></i><span>Más</span></button>
      </nav>
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <script
      src="{{asset('js/overlayscrollbars.browser.es6.min.js')}}"
    ></script>
    <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script
      src="{{asset('js/popper.min.js')}}"
    ></script>
    <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
    <script
      src="{{asset('js/bootstrap.min.js')}}"
    ></script>
    <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
    <script src="{{asset('js/adminlte.js')}}"></script>
    <script src="{{asset('datatables/jquery-3.7.1.js')}}"></script>
    <script src="{{asset('datatables/dataTables.js')}}"></script>
    <script src="{{asset('datatables/dataTables.bootstrap5.js')}}"></script>
    <script src="{{asset('js/sweetalert2.js')}}"></script>
    <script src="{{asset('js/crud.js')}}"></script>
    @stack('scripts')
    <script src="{{ asset('js/acotec.js') }}?v={{ filemtime(public_path('js/acotec.js')) }}" defer></script>
    <!--end::Required Plugin(AdminLTE)--><!--begin::OverlayScrollbars Configure-->
    <script>
      const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
      const Default = {
        scrollbarTheme: 'os-theme-light',
        scrollbarAutoHide: 'leave',
        scrollbarClickScroll: true,
      };
      document.addEventListener('DOMContentLoaded', function () {
        const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
        if (sidebarWrapper && typeof OverlayScrollbarsGlobal?.OverlayScrollbars !== 'undefined') {
          OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
            scrollbars: {
              theme: Default.scrollbarTheme,
              autoHide: Default.scrollbarAutoHide,
              clickScroll: Default.scrollbarClickScroll,
            },
          });
        }
      });
    </script>
    <!--end::OverlayScrollbars Configure-->
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
