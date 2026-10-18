<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">

    {{-- BRAND --}}
    <div class="sidebar-brand d-flex align-items-center px-3">
        <a href="{{route('dashboard')}}" class="brand-link d-flex align-items-center justify-content-center text-decoration-none" aria-label="Ir al inicio">
            <img src="{{ asset('assets/branding/acotec-logo.png') }}" class="acotec-logo-sidebar" alt="Acotec">
            <img src="{{ asset('assets/branding/acotec-mark.png') }}" class="acotec-mark-image" alt="" aria-hidden="true">
        </a>
    </div>

    <div class="sidebar-wrapper">
        <nav class="mt-2" aria-label="Navegación principal">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">

                {{-- ================= DASHBOARD ================= --}}
                <li class="nav-item">
                    <a href="{{route('dashboard')}}" class="nav-link" id="itemDashboard">
                        <i class="nav-icon bi bi-speedometer2"></i>
                        <p>Vista general</p>
                    </a>
                </li>


                {{-- ================= ALMACÉN ================= --}}
                @canany(['unidades_list','productos_list','proveedores_list','compras_list'])
                <li class="nav-item" id="mnuAlmacen">
                    <a href="javascript:void(0);" class="nav-link">
                        <i class="nav-icon bi bi-box-seam"></i>
                        <p>Almacén <i class="nav-arrow bi bi-chevron-right"></i></p>
                    </a>

                    <ul class="nav nav-treeview">

                        @can('unidades_list')
                        <li class="nav-item"><a href="{{route('unidades.index')}}" class="nav-link" id="itemUnidades">
                            <i class="nav-icon bi bi-rulers"></i><p>Unidades</p></a>
                        </li>
                        @endcan

                        @can('productos_list')
                        <li class="nav-item"><a href="{{route('productos.index')}}" class="nav-link" id="itemProductos">
                            <i class="nav-icon bi bi-box"></i><p>Productos</p></a>
                        </li>
                        <li class="nav-item"><a href="{{route('woocommerce.index')}}" class="nav-link" id="itemWooCommerce">
                            <i class="nav-icon bi bi-shop"></i><p>WooCommerce</p></a>
                        </li>
                        @endcan

                        @can('proveedores_list')
                        <li class="nav-item"><a href="{{route('proveedores.index')}}" class="nav-link" id="itemProveedores">
                            <i class="nav-icon bi bi-truck"></i><p>Proveedores</p></a>
                        </li>
                        @endcan

                        @can('compras_list')
                        <li class="nav-item"><a href="{{route('compras.index')}}" class="nav-link" id="itemCompras">
                            <i class="nav-icon bi bi-cart"></i><p>Compras</p></a>
                        </li>
                        @endcan

                        @can('salidas_list')
                        <li class="nav-item"><a href="{{route('salidas.index')}}" class="nav-link" id="itemSalidas">
                            <i class="nav-icon bi bi-person-lines-fill"></i><p>Salidas Almacén</p></a>
                        </li>
                        @endcan

                    </ul>
                </li>
                @endcanany


                {{-- ================= VENTAS ================= --}}
                @canany(['cotizaciones_list','ventas_list','notacredito_list'])
                <li class="nav-item" id="mnuVenta">
                    <a href="javascript:void(0);" class="nav-link">
                        <i class="nav-icon bi bi-cash-coin"></i>
                        <p>Ventas <i class="nav-arrow bi bi-chevron-right"></i></p>
                    </a>

                    <ul class="nav nav-treeview">

                        @can('cotizaciones_list')
                        <li class="nav-item"><a href="{{route('cotizaciones.index')}}" class="nav-link" id="itemCotizaciones">
                            <i class="nav-icon bi bi-file-earmark-text"></i><p>Cotizaciones</p></a>
                        </li>
                        @endcan

                        @can('ventas_list')
                        <li class="nav-item"><a href="{{route('ventas.index')}}" class="nav-link" id="itemVentas">
                            <i class="nav-icon bi bi-receipt"></i><p>Ventas</p></a>
                        </li>
                        @endcan

                        @can('notacredito_list')
                        <li class="nav-item"><a href="{{route('notacredito.index')}}" class="nav-link" id="itemNotaCredito">
                            <i class="nav-icon bi bi-arrow-return-left"></i><p>Notas de Crédito</p></a>
                        </li>
                        @endcan

                    </ul>
                </li>
                @endcanany


                {{-- ================= COMERCIAL ================= --}}
                @canany(['clientes_list','servicios_list'])
                <li class="nav-item" id="mnuComercial">
                    <a href="javascript:void(0);" class="nav-link">
                        <i class="nav-icon bi bi-briefcase"></i>
                        <p>Comercial <i class="nav-arrow bi bi-chevron-right"></i></p>
                    </a>

                    <ul class="nav nav-treeview">

                        @can('clientes_list')
                        <li class="nav-item"><a href="{{route('clientes.index')}}" class="nav-link" id="itemClientes">
                            <i class="nav-icon bi bi-people"></i><p>Clientes</p></a>
                        </li>
                        @endcan

                        @can('sedes_list')
                        <li class="nav-item"><a href="{{route('sedes.index')}}" class="nav-link" id="itemSedes">
                            <i class="nav-icon bi bi-building"></i><p>Sedes</p></a>
                        </li>
                        @endcan

                        @can('responsables_list')
                        <li class="nav-item"><a href="{{route('responsables.index')}}" class="nav-link" id="itemResponsables">
                            <i class="nav-icon bi bi-person-badge"></i><p>Solicitantes</p></a>
                        </li>
                        @endcan

                        @can('servicios_list')
                        <li class="nav-item"><a href="{{route('servicios.index')}}" class="nav-link" id="itemServicios">
                            <i class="nav-icon bi bi-tools"></i><p>Servicios</p></a>
                        </li>
                        @endcan

                        @can('tpersonal_list')
                        <li class="nav-item"><a href="{{route('tpersonal.index')}}" class="nav-link" id="itemTPersonal">
                            <i class="nav-icon bi bi-bus-front"></i><p>Transporte Personal</p></a>
                        </li>
                        @endcan

                        @can('transporte_list')
                        <li class="nav-item"><a href="{{route('transporte.index')}}" class="nav-link" id="itemTransporte">
                            <i class="nav-icon bi bi-truck"></i><p>Transporte Herramientas</p></a>
                        </li>
                        @endcan

                        @can('condiciones_list')
                        <li class="nav-item"><a href="{{route('condiciones.index')}}" class="nav-link" id="itemCondiciones">
                            <i class="nav-icon bi bi-card-checklist"></i><p>Condiciones</p></a>
                        </li>
                        @endcan

                    </ul>
                </li>
                @endcanany


                {{-- ================= OPERACIONES ================= --}}
                @canany(['ordenestrabajo_list','ordenesservicio_list'])
                <li class="nav-item" id="mnuMantenimiento">
                    <a href="javascript:void(0);" class="nav-link">
                        <i class="nav-icon bi bi-wrench"></i>
                        <p>Operaciones <i class="nav-arrow bi bi-chevron-right"></i></p>
                    </a>

                    <ul class="nav nav-treeview">

                        @can('ordenestrabajo_list')
                        <li class="nav-item"><a href="{{route('ordenestrabajo.index')}}" class="nav-link" id="itemOrdenesTrabajo">
                            <i class="nav-icon bi bi-clipboard"></i><p>Órdenes de Trabajo</p></a>
                        </li>
                        @endcan

                        @can('ordenesservicio_list')
                        <li class="nav-item"><a href="{{route('ordenesservicio.index')}}" class="nav-link" id="itemOrdenesServicio">
                            <i class="nav-icon bi bi-file-earmark"></i><p>Órdenes de Servicio</p></a>
                        </li>
                        @endcan

                        @can('informes_list')
                        <li class="nav-item"><a href="{{route('informes.index')}}" class="nav-link" id="itemInformes">
                            <i class="nav-icon bi bi-journal-text"></i><p>Informes</p></a>
                        </li>
                        @endcan

                        @can('tecnicos_list')
                        <li class="nav-item"><a href="{{route('tecnicos.index')}}" class="nav-link" id="itemTecnicos">
                            <i class="nav-icon bi bi-person-gear"></i><p>Técnicos</p></a>
                        </li>
                        @endcan

                    </ul>
                </li>
                @endcanany


                {{-- ================= REPORTES ================= --}}
                @canany(['reporteventas_list','reportecompras_list','reportepagos_list','reporteordenes_list'])
                <li class="nav-item {{ request()->routeIs('reportes.*') ? 'menu-open' : '' }}" id="mnuReporte">
                    <a href="javascript:void(0);" class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-bar-chart-line"></i>
                        <p>
                            Reportes
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">

                        @can('reporteventas_list')
                        <li class="nav-item">
                            <a href="{{ route('reportes.ventas') }}" 
                            class="nav-link {{ request()->routeIs('reportes.ventas') ? 'active' : '' }}"
                            id="itemReporteVentas">
                                <i class="nav-icon bi bi-cash-coin"></i>
                                <p>Ventas</p>
                            </a>
                        </li>
                        @endcan

                        @can('reportecompras_list')
                        <li class="nav-item">
                            <a href="{{ route('reportes.compras') }}" class="nav-link {{ request()->routeIs('reportes.compras') ? 'active' : '' }}" id="itemReporteCompras">
                                <i class="nav-icon bi bi-bag"></i>
                                <p>Compras</p>
                            </a>
                        </li>
                        @endcan

                        @can('reportepagos_list')
                        <li class="nav-item">
                            <a href="{{ route('reportes.pagos') }}" class="nav-link {{ request()->routeIs('reportes.pagos') ? 'active' : '' }}" id="itemReportePagos">
                                <i class="nav-icon bi bi-cash"></i>
                                <p>Pagos</p>
                            </a>
                        </li>
                        @endcan

                        @can('reporteordenes_list')
                        <li class="nav-item">
                            <a href="{{ route('reportes.ordenes') }}" 
                            class="nav-link {{ request()->routeIs('reportes.ordenes') ? 'active' : '' }}"
                            id="itemReporteOrdenes">
                                <i class="nav-icon bi bi-list-task"></i>
                                <p>OTs</p>
                            </a>
                        </li>
                        @endcan

                    </ul>
                </li>
                @endcanany


                {{-- ================= CONFIGURACIÓN ================= --}}
                @canany(['comprobante_tipos_list','documento_tipos_list'])
                <li class="nav-item" id="mnuConfiguracion">
                    <a href="javascript:void(0);" class="nav-link">
                        <i class="nav-icon bi bi-gear"></i>
                        <p>Configuración <i class="nav-arrow bi bi-chevron-right"></i></p>
                    </a>

                    <ul class="nav nav-treeview">

                        <li class="nav-item"><a href="{{route('comprobante-tipos.index')}}" class="nav-link">
                            <i class="nav-icon bi bi-receipt-cutoff"></i><p>Tipos Comprobante</p></a>
                        </li>

                        <li class="nav-item"><a href="{{route('documento-tipos.index')}}" class="nav-link">
                            <i class="nav-icon bi bi-file-earmark-text"></i><p>Tipos Documento</p></a>
                        </li>

                        <li class="nav-item"><a href="{{route('afectacion-tipos.index')}}" class="nav-link">
                            <i class="nav-icon bi bi-percent"></i><p>Tipos Afectación</p></a>
                        </li>

                        <li class="nav-item"><a href="{{route('comprobante-series.index')}}" class="nav-link">
                            <i class="nav-icon bi bi-hash"></i><p>Correlativos</p></a>
                        </li>

                        <li class="nav-item"><a href="{{route('bancos.index')}}" class="nav-link">
                            <i class="nav-icon bi bi-bank"></i><p>Bancos</p></a>
                        </li>

                    </ul>
                </li>
                @endcanany


                {{-- ================= SEGURIDAD ================= --}}
                @canany(['users_list','roles_permisos_list'])
                <li class="nav-item" id="mnuSeguridad">
                    <a href="javascript:void(0);" class="nav-link">
                        <i class="nav-icon bi bi-shield-lock"></i>
                        <p>Seguridad <i class="nav-arrow bi bi-chevron-right"></i></p>
                    </a>

                    <ul class="nav nav-treeview">

                        <li class="nav-item"><a href="{{route('roles.index')}}" class="nav-link">
                            <i class="nav-icon bi bi-person-lock"></i><p>Roles</p></a>
                        </li>

                        <li class="nav-item"><a href="{{route('usuarios.index')}}" class="nav-link">
                            <i class="nav-icon bi bi-people"></i><p>Usuarios</p></a>
                        </li>

                    </ul>
                </li>
                @endcanany

            </ul>
        </nav>
    </div>
</aside>
