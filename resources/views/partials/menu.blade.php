<ul class="c-sidebar-nav">
    <li class="c-sidebar-nav-item">
        <a class="c-sidebar-nav-link" href="{{ route('home') }}">
            <i class="c-sidebar-nav-icon fas fa-truck-loading"></i> @lang('Load trip')
        </a>
    </li>
    <li class="c-sidebar-nav-dropdown">
        <a class="c-sidebar-nav-dropdown-toggle" href="">
            <i class="c-sidebar-nav-icon fas fa-file-alt"></i> @lang('Documents')
        </a>
        <ul class="c-sidebar-nav-dropdown-items">
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link" href="{{ route('documents.received') }}">
                    @lang('Received')
                </a>
            </li>
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link" href="{{ route('documents.sent') }}">
                    @lang('Sent')
                </a>
            </li>
        </ul>
    </li>
    <li class="c-sidebar-nav-item">
        <a class="c-sidebar-nav-link" href="{{ route('reports') }}">
            <i class="c-sidebar-nav-icon fas fa-exclamation-circle"></i> @lang('Report problem')
        </a>
    </li>
    <li class="c-sidebar-nav-item">
        <a class="c-sidebar-nav-link" href="{{ route('report.maint') }}">
            <i class="c-sidebar-nav-icon fas fa-tools"></i> @lang('Report maintenances')
        </a>
    </li>
    <li class="c-sidebar-nav-item">
        <a class="c-sidebar-nav-link" href="{{ route('crash') }}">
            <i class="c-sidebar-nav-icon fas fa-car-crash"></i> @lang('Report crash')
        </a>
    </li>
    <li class="c-sidebar-nav-item">
        <a class="c-sidebar-nav-link" href="{{ route('settings') }}">
            <i class="c-sidebar-nav-icon fas fa-cogs"></i> @lang('Settings')
        </a>
    </li>
</ul>