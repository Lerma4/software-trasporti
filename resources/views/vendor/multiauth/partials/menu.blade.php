<ul class="c-sidebar-nav">
    <li class="c-sidebar-nav-item">
        <a class="c-sidebar-nav-link" href="{{ route('admin.home') }}">
            <i class="c-sidebar-nav-icon fas fa-chart-line"></i> @lang('Dashboard')
        </a>
    </li>
    <li class="c-sidebar-nav-item">
        <a class="c-sidebar-nav-link" href="{{ route('admin.groups') }}">
            <i class="c-sidebar-nav-icon fas fa-users-cog"></i> @lang('Groups')
        </a>
    </li>
    <li class="c-sidebar-nav-item">
        <a class="c-sidebar-nav-link" href="{{ route('admin.users') }}">
            <i class="c-sidebar-nav-icon fas fa-users"></i> @lang('Users')
        </a>
    </li>
    <li class="c-sidebar-nav-dropdown">
        <a class="c-sidebar-nav-dropdown-toggle" href="">
            <i class="c-sidebar-nav-icon fas fa-truck-moving"></i> @lang('Vehicle management')
        </a>
        <ul class="c-sidebar-nav-dropdown-items">
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link" href="{{ route('admin.trucks') }}">
                    @lang('Trucks')
                </a>
            </li>
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link" href="{{ route('admin.maint') }}">
                    @lang('Maintenance')
                </a>
            </li>
        </ul>
    </li>
    <li class="c-sidebar-nav-item">
        <a class="c-sidebar-nav-link" href="{{ route('admin.settings') }}">
            <i class="c-sidebar-nav-icon fas fa-cogs"></i> @lang('Settings')
        </a>
    </li>
</ul>