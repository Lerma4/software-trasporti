<ul class="c-sidebar-nav">
    <li class="c-sidebar-nav-item">
        <a class="c-sidebar-nav-link" href="{{ route('admin.home') }}">
            <i class="c-sidebar-nav-icon cil-speedometer"></i> @lang('Dashboard')
        </a>
    </li>
    <li class="c-sidebar-nav-item">
        <a class="c-sidebar-nav-link" href="{{ route('admin.users') }}">
            <i class="c-sidebar-nav-icon cil-people"></i> @lang('Users')
        </a>
    </li>
    <li class="c-sidebar-nav-dropdown">
        <a class="c-sidebar-nav-dropdown-toggle" href="#">
            <i class="c-sidebar-nav-icon cil-truck"></i> @lang('Vehicle management')
        </a>
        <ul class="c-sidebar-nav-dropdown-items">
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link" href="#">
                    @lang('Trucks')
                </a>
            </li>
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link" href="#">
                    @lang('Maintenance')
                </a>
            </li>
        </ul>
    </li>
</ul>