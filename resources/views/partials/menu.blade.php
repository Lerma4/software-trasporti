<ul class="c-sidebar-nav">
    <li class="c-sidebar-nav-item">
        <a class="c-sidebar-nav-link" href="{{ route('home') }}">
            <i class="c-sidebar-nav-icon fas fa-truck-loading"></i> @lang('Load trip')
        </a>
    </li>
    <li class="c-sidebar-nav-item">
        <a class="c-sidebar-nav-link" href="{{ route('crash') }}">
            <i class="c-sidebar-nav-icon fas fa-car-crash"></i> @lang('Report crash')
        </a>
    </li>
    <li class="c-sidebar-nav-item">
        <a class="c-sidebar-nav-link" href="{{ route('documents') }}">
            <i class="c-sidebar-nav-icon fas fa-file-alt"></i> @lang('Documents')
        </a>
    </li>
    <li class="c-sidebar-nav-item">
        <a class="c-sidebar-nav-link" href="{{ route('settings') }}">
            <i class="c-sidebar-nav-icon fas fa-cogs"></i> @lang('Settings')
        </a>
    </li>
</ul>