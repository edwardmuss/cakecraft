<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="{{ route('root') }}" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ \App\Support\Brand::logoUrl() }}" alt="" height="30" style="border-radius:50%">
            </span>
            <span class="logo-lg">
                <img src="{{ \App\Support\Brand::logoUrl() }}" alt="" height="44" style="border-radius:50%">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="{{ route('root') }}" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ \App\Support\Brand::logoUrl() }}" alt="" height="30" style="border-radius:50%">
            </span>
            <span class="logo-lg">
                <img src="{{ \App\Support\Brand::logoUrl() }}" alt="" height="44" style="border-radius:50%">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span>@lang('translation.menu')</span></li>
                
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ config('cakecraft.store_domain') ? 'https://' . config('cakecraft.store_domain') : url('/') }}" target="_blank">
                        <i class="ri-global-line"></i> <span>View Website</span>
                    </a>
                </li>
                
                <!-- start Dashboard Menu -->
                @can('view dashboard')
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('root') ? 'active' : '' }}" href="{{ route('root') }}">
                            <i class="ri-dashboard-2-line"></i> <span>@lang('translation.dashboard')</span>
                        </a>
                    </li>
                @endcan
                <!-- end Dashboard Menu -->

                <!-- start branches Menu -->   
                @can('view branches')
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('branches*') ? 'active' : '' }}" href="{{ route('branches.index') }}">
                            <i class="ri-store-2-line"></i> <span>@lang('translation.branches')</span>
                        </a>
                    </li>
                @endcan    
                <!-- end branches Menu -->  

                <!-- start Customers Menu -->   
                @can('view customers')
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('customers*') ? 'active' : '' }}" href="{{ route('customers.index') }}">
                            <i class="ri-group-line"></i> <span>@lang('translation.customers')</span>
                        </a>
                    </li>
                @endcan    
                <!-- end Customers Menu -->      

                <!-- start Catalogue Menu -->
                @can('view products')
                    @php $catActive = request()->routeIs('cakes*') || request()->routeIs('collections*') || request()->routeIs('flavours*') || request()->routeIs('sizes*') || request()->routeIs('zones*'); @endphp
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ $catActive ? 'active' : '' }}" href="#sidebarCatalogue" data-bs-toggle="collapse" role="button" aria-expanded="{{ $catActive ? 'true' : 'false' }}" aria-controls="sidebarCatalogue">
                            <i class="ri-cake-3-line"></i> <span>Cakes &amp; Catalogue</span>
                        </a>
                        <div class="{{ $catActive ? 'collapse show' : 'collapse' }} menu-dropdown" id="sidebarCatalogue">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item"><a href="{{ route('cakes.index') }}" class="nav-link {{ request()->routeIs('cakes*') ? 'active' : '' }}">Cakes</a></li>
                                <li class="nav-item"><a href="{{ route('collections.index') }}" class="nav-link {{ request()->routeIs('collections*') ? 'active' : '' }}">Collections</a></li>
                                <li class="nav-item"><a href="{{ route('flavours.index') }}" class="nav-link {{ request()->routeIs('flavours*') ? 'active' : '' }}">Flavours</a></li>
                                <li class="nav-item"><a href="{{ route('sizes.index') }}" class="nav-link {{ request()->routeIs('sizes*') ? 'active' : '' }}">Sizes</a></li>
                                <li class="nav-item"><a href="{{ route('zones.index') }}" class="nav-link {{ request()->routeIs('zones*') ? 'active' : '' }}">Delivery Zones</a></li>
                            </ul>
                        </div>
                    </li>
                @endcan
                <!-- end Catalogue Menu -->

                <!-- start Web Orders -->
                @can('view invoices')
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('order-requests*') ? 'active' : '' }}" href="{{ route('order-requests.index') }}">
                            <i class="ri-shopping-basket-2-line"></i> <span>Web Orders</span>
                        </a>
                    </li>
                @endcan

                <!-- start Occasion Reminders -->
                @can('view customers')
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('occasions*') ? 'active' : '' }}" href="{{ route('occasions.index') }}">
                            <i class="ri-cake-2-line"></i> <span>Occasion Reminders</span>
                        </a>
                    </li>
                @endcan 

                <!-- start Sales & Billing Menu -->
                @canany(['view invoices', 'view financials', 'create payments'])
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('quotations*') || request()->routeIs('invoices*') || request()->routeIs('payments*') ? 'active' : '' }}" href="#sidebarSales" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('quotations*') || request()->routeIs('invoices*') || request()->routeIs('payments*') ? 'true' : 'false' }}" aria-controls="sidebarSales">
                            <i class="ri-shopping-cart-2-line"></i> <span>Sales & Billing</span>
                        </a>
                        <div class="{{ request()->routeIs('quotations*') || request()->routeIs('invoices*') || request()->routeIs('payments*') ? 'collapse.show' : 'collapse' }} menu-dropdown" id="sidebarSales">
                            <ul class="nav nav-sm flex-column">
                                @can('view financials')
                                <li class="nav-item">
                                    <a href="{{ route('quotations.index') }}" class="nav-link {{ request()->routeIs('quotations*') ? 'active' : '' }}">Quotations</a>
                                </li>
                                @endcan
                                @can('view invoices')
                                <li class="nav-item">
                                    <a href="{{ route('invoices.index') }}" class="nav-link {{ request()->routeIs('invoices*') ? 'active' : '' }}">Invoices</a>
                                </li>
                                @endcan
                                @can('create payments')
                                <li class="nav-item">
                                    <a href="{{ route('payments.index') }}" class="nav-link {{ request()->routeIs('payments*') ? 'active' : '' }}">Payments Received</a>
                                </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                @endcanany
                <!-- end Sales & Billing Menu -->

                <!-- start Financials Menu -->
                @canany(['view financials', 'view suppliers', 'view expense categories', 'view expenses'])
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('expense-categories*') || request()->routeIs('suppliers*') || request()->routeIs('expenses*') || request()->routeIs('finance.dashboard*') ? 'active' : '' }}" href="#sidebarFinance" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('expense-categories*') || request()->routeIs('suppliers*') || request()->routeIs('expenses*') || request()->routeIs('finance.dashboard*') ? 'true' : 'false' }}" aria-controls="sidebarFinance">
                            <i class="ri-funds-box-line"></i> <span>Financials</span>
                        </a>
                        <div class="{{ request()->routeIs('expense-categories*') || request()->routeIs('suppliers*') || request()->routeIs('expenses*') || request()->routeIs('finance.dashboard*') ? 'collapse.show' : 'collapse' }} menu-dropdown" id="sidebarFinance">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('finance.dashboard') }}" class="nav-link {{ request()->routeIs('finance.dashboard*') ? 'active' : '' }}">Finance Dashboard</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('expenses.index') }}" class="nav-link {{ request()->routeIs('expenses*') ? 'active' : '' }}">Expense Ledger</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers*') ? 'active' : '' }}">Suppliers / Vendors</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('expense-categories.index') }}" class="nav-link {{ request()->routeIs('expense-categories*') ? 'active' : '' }}">Expense Categories</a>
                                </li>
                            </ul>
                        </div>
                    </li> 
                @endcanany   
                <!-- end Financials Menu -->

                <!-- start Reports Menu -->
                @can('view financials')
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('reports*') || request()->routeIs('statements*') ? 'active' : '' }}" href="#sidebarReports" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('reports*') || request()->routeIs('statements*') ? 'true' : 'false' }}" aria-controls="sidebarReports">
                            <i class="ri-file-chart-line"></i> <span>@lang('translation.reports')</span>
                        </a>
                        <div class="{{ request()->routeIs('reports*') || request()->routeIs('statements*') ? 'collapse.show' : 'collapse' }} menu-dropdown" id="sidebarReports">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('statements.index') }}" class="nav-link {{ request()->routeIs('statements*') ? 'active' : '' }}">Customer Statements</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('reports.receivables') }}" class="nav-link {{ request()->routeIs('reports.receivables') ? 'active' : '' }}">Receivables</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.index') ? 'active' : '' }}">General Reports</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endcan  
                <!-- end Reports Menu -->

                <!-- start Settings Menu -->
                @canany(['view settings', 'view roles', 'view users'])
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('settings*') || request()->routeIs('roles*') || request()->routeIs('users.permissions*') || request()->routeIs('sms-templates*') || request()->routeIs('branding*') ? 'active' : '' }}" href="#sidebarsettings" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('settings*') || request()->routeIs('roles*') || request()->routeIs('users.permissions*') || request()->routeIs('sms-templates*') || request()->routeIs('branding*') ? 'true' : 'false' }}" aria-controls="sidebarsettings">
                            <i class="ri-settings-4-line"></i> <span>@lang('translation.settings')</span>
                        </a>
                        <div class="{{ request()->routeIs('settings*') || request()->routeIs('roles*') || request()->routeIs('users.permissions*') || request()->routeIs('sms-templates*') || request()->routeIs('branding*') ? 'collapse.show' : 'collapse' }} menu-dropdown" id="sidebarsettings">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.index') ? 'active' : '' }}">
                                        @lang('translation.profile')
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('branding.edit') }}" class="nav-link {{ request()->routeIs('branding*') ? 'active' : '' }}">Branding</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('sms-templates.index') }}" class="nav-link {{ request()->routeIs('sms-templates*') || request()->routeIs('branding*') ? 'active' : '' }}">
                                        @lang('translation.sms-templates')
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('roles.index') }}" class="nav-link {{ request()->routeIs('roles*') || request()->routeIs('users.permissions*') ? 'active' : '' }}">
                                        @lang('translation.roles-permissions')
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li> 
                @endcanany
                <!-- end Settings Menu -->
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>