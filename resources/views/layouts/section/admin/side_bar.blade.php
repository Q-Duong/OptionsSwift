<aside>
    <div id="sidebar" class="nav-collapse">
        <!-- sidebar menu start-->
        <div class="leftside-navigation">
            <ul class="sidebar-menu" id="nav-accordion">
                <li>
                    <a class=""
                        href="">
                        <i class="far fa-chart-bar"></i>
                        <span>Thống kê doanh thu</span>
                    </a>
                </li>
                <li class="sub-menu">
                    <a class="{{ request()->routeIs('admin.html.list') ? 'active' : '' }}"
                        href="{{ route('admin.html.list') }}">
                        <i class="far fa-code"></i>
                        <span>Quản lý Giao diện Client</span>
                    </a>
                </li>
                <li class="sub-menu">
                    <a class="{{ request()->routeIs('admin.clients.pending') ? 'active' : '' }}"
                        href="{{ route('admin.clients.pending') }}">
                        <i class="far fa-code"></i>
                        <span>Quản lý Client</span>
                    </a>
                </li>
            </ul>
            <!-- sidebar menu end-->
        </div>
    </div>
</aside>
