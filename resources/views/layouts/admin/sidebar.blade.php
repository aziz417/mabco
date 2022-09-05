<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ url('/') }}" class="brand-link">
        <img src="{{ asset('assets/admin/dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">{{ config('app.name') }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset('assets/admin/dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2" alt="User Image">
            </div>

            <div class="info">
                <a href="#" class="d-block">{{ \Illuminate\Support\Facades\Auth::user()->name }}</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ Request::routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('category') }}" class="nav-link {{ Request::routeIs('category','category.create','category.edit') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-list-alt"></i>
                        <p>Category</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('brand') }}" class="nav-link {{ Request::routeIs('brand','brand.create','brand.edit') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-list-alt"></i>
                        <p>Brand</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('product') }}" class="nav-link {{ Request::routeIs('product','product.create','product.edit') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>Products</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('stock') }}" class="nav-link {{ Request::routeIs('stock','stock.edit') ? 'active' : '' }}">
                        <i class="nav-icon fa fa-suitcase"></i>
                        <p>Stock</p>
                    </a>
                </li>

                <li class="nav-item has-treeview {{ Request::routeIs('inventory') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Request::routeIs('inventory') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-plus"></i>
                        <p>
                            Inventory
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">

                            <a href="{{ route('inventory', 'in') }}" class="nav-link {{ Request()->getPathInfo() == '/stock/in' ?  'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Stock-In</p>
                            </a>
                            <a href="{{ route('inventory', 'out') }}" class="nav-link {{ Request()->getPathInfo() == '/stock/out' ? 'active' : ''}}">

                                <i class="far fa-circle nav-icon"></i>
                                <p>Stock-Out</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item has-treeview {{ Request::routeIs('order','order.create','order.edit', 'order.approve.list','order.approve','order.cancel.list', 'transaction', 'transaction.create', 'transaction.edit') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Request::routeIs('order','order.create','order.edit', 'order.approve.list', 'order.approve','order.cancel.list', 'transaction', 'transaction.create', 'transaction.edit') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-plus"></i>
                        <p>
                            Order Manage
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">

                            <a href="{{ route('order') }}" class="nav-link {{ Request::routeIs('order','order.create','order.edit','order.edit') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Orders</p>
                            </a>


                            <a href="{{ route('order.approve.list') }}" class="nav-link {{ Request::routeIs('order.approve.list','order.approve') ? 'active' : ''}}">

                                <i class="far fa-circle nav-icon"></i>
                                <p>Order Approve</p>
                            </a>

                            <a href="{{ route('order.cancel.list') }}" class="nav-link {{ Request::routeIs('order.cancel.list') ? 'active' : ''}}">

                                <i class="far fa-circle nav-icon"></i>
                                <p>Order Cancel</p>
                            </a>
                            <a href="{{ route('transaction') }}" class="nav-link {{ Request::routeIs('transaction', 'transaction.create', 'transaction.edit') ? 'active' : ''}} ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Order Transaction</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="{{ route('order_distribute') }}" class="nav-link {{ Request::routeIs('order_distribute') ? 'active' : '' }}">
                        <i class="nav-icon fa fa-suitcase"></i>
                        <p>Order Distribution</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('overview') }}" class="nav-link {{ Request::routeIs('overview','overview.details', 'overview.history') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Overview</p>
                    </a>
                </li>

{{--                <li class="nav-item has-treeview {{ Request::routeIs('order_distribute','order_distribute.create','order_distribute.edit', 'sales','sales.details', 'order_distribute.get_distribute_list') ? 'menu-open' : '' }}">--}}
{{--                    <a href="#" class="nav-link {{ Request::routeIs('order_distribute','order_distribute.create','order_distribute.edit', 'sales','sales.details', 'order_distribute.get_distribute_list') ? 'active' : '' }}">--}}
{{--                        <i class="nav-icon fas fa-plus"></i>--}}
{{--                        <p>--}}
{{--                            Order Distributes--}}
{{--                            <i class="fas fa-angle-left right"></i>--}}
{{--                        </p>--}}
{{--                    </a>--}}
{{--                    <ul class="nav nav-treeview">--}}
{{--                        <li class="nav-item">--}}

{{--                            <a href="{{ route('order_distribute') }}" class="nav-link {{ Request::routeIs('order_distribute','order_distribute.create','order.edit','order_distribute.edit', 'order_distribute.get_distribute_list') ? 'active' : '' }}">--}}
{{--                                <i class="far fa-circle nav-icon"></i>--}}
{{--                                <p>Distributes</p>--}}
{{--                            </a>--}}

{{--                            <a href="{{ route('sales') }}" class="nav-link {{ Request::routeIs('sales','sales.details') ? 'active' : '' }}">--}}
{{--                                <i class="far fa-circle nav-icon"></i>--}}
{{--                                <p>Sales Person</p>--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                    </ul>--}}
{{--                </li>--}}

                <li class="nav-item">
                    <a href="{{ route('return') }}" class="nav-link {{ Request::routeIs('return', 'return.create','return.edit', 'return.show', 'retailer_wise.products') ? 'active' : '' }}">
                        <i class="nav-icon fa fa-suitcase"></i>
                        <p>Return & Damage</p>
                    </a>
                </li>

                <li class="nav-item has-treeview {{ Request::routeIs('permission','permission.create','permission.edit','role','role.create','role.edit','user','user.create','user.edit') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Request::routeIs('permission','permission.create','permission.edit','role','role.create','role.edit','user','user.create','user.edit') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            User Management
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">

                            <a href="{{ route('user') }}" class="nav-link {{ Request::routeIs('user','user.create','user.edit') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>User</p>
                            </a>

                            <a href="{{ route('role') }}" class="nav-link {{ Request::routeIs('role','role.create','role.edit') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Role</p>
                            </a>

                            <a href="{{ route('permission') }}" class="nav-link {{ Request::routeIs('permission','permission.create','permission.edit') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Permission</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="{{ route('adjustment') }}" class="nav-link {{ Request::routeIs('adjustment','adjustment.create', 'adjustment.edit') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Adjustment</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('largeTransaction') }}" class="nav-link {{ Request::routeIs('largeTransaction') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Large Transaction</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('expanse') }}" class="nav-link {{ Request::routeIs('expanse','expanse.create', 'expanse.edit') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Expanse</p>
                    </a>
                </li>

                <li class="nav-item has-treeview {{ Request::routeIs('unit','unit.create','unit.edit', 'bank', 'bank.create', 'bank.edit' ,'return_reason','return_reason.create','return_reason.edit', 'expanse_reason','expanse_reason.create','expanse_reason.edit') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Request::routeIs('unit','unit.create','unit.edit','bank','bank.create','bank.edit' ,'return_reason','return_reason.create','return_reason.edit', 'expanse_reason','expanse_reason.create','expanse_reason.edit') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>
                            Settings
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('unit') }}" class="nav-link {{ Request::routeIs('unit','unit.create','unit.edit') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Unit</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('bank') }}" class="nav-link {{ Request::routeIs('bank','bank.create','bank.edit') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Bank</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('return_reason') }}" class="nav-link {{ Request::routeIs('return_reason','return_reason.create','return_reason.edit') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Product Return Reason</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('expanse_reason') }}" class="nav-link {{ Request::routeIs('expanse_reason','expanse_reason.create','expanse_reason.edit') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Expanse Reason</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="{{ route('report') }}" class="nav-link {{ Request::routeIs('report') ? 'active' : '' }}">
                        <i class="nav-icon fa fa-suitcase"></i>
                        <p>Report</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
