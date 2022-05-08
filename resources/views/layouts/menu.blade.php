<!-- need to remove -->
<li class="nav-item">
    <a href="{{ route('home') }}" class="nav-link {{ Request::is('home') ? 'active' : '' }}">
        <i class="nav-icon fas fa-home"></i>
        <p>Home</p>
    </a>
</li>

<li class="nav-item">
    @can('CRUD_city_managers')
        <a href="{{ route('city_managers.index') }}" class="nav-link {{ Request::is('city_managers') ? 'active' : '' }}">
            <i class="nav-icon fas fa-dashboard"></i>
            <p>City Managers</p>
        </a>
    @endcan
</li>

<li class="nav-item">
    @can('CRUD_gym_managers')
        <a href="{{ route('gym_managers.index') }}" class="nav-link {{ Request::is('gym_managers') ? 'active' : '' }}">
            <i class="nav-icon fas fa-dashboard"></i>
            <p>Gym Managers</p>
        </a>
    @endcan
</li>

<li class="nav-item">
    @can('CRUD_coaches')
        <a href="{{ route('coaches.index') }}" class="nav-link {{ Request::is('coaches') ? 'active' : '' }}">
            <i class="nav-icon fas fa-dashboard"></i>
            <p>Coaches</p>
        </a>
    @endcan
</li>

<li class="nav-item">
    @can('CRUD_members')
        <a href="{{ route('members.index') }}" class="nav-link {{ Request::is('members') ? 'active' : '' }}">
            <i class="nav-icon fas fa-dashboard"></i>
            <p>Members</p>
        </a>
    @endcan
</li>

<li class="nav-item">
    @can('CRUD_cities')
        <a href="{{ route('cities.index') }}" class="nav-link {{ Request::is('cities') ? 'active' : '' }}">
            <i class="nav-icon fas fa-dashboard"></i>
            <p>Cities</p>
        </a>
    @endcan
</li>

<li class="nav-item">
    @can('CRUD_gyms')
        <a href="{{ route('gyms.index') }}" class="nav-link {{ Request::is('gyms') ? 'active' : '' }}">
            <i class="nav-icon fas fa-dashboard"></i>
            <p>Gyms</p>
        </a>
    @endcan
</li>

<li class="nav-item">
    @can('CRUD_sessions')
        <a href="{{ route('sessions.index') }}" class="nav-link {{ Request::is('sessions') ? 'active' : '' }}">
            <i class="nav-icon fas fa-dashboard"></i>
            <p>Sessions</p>
        </a>
    @endcan
</li>

<li class="nav-item">
    @can('Read_attendance')
        <a href="{{ route('attendance.index') }}" class="nav-link {{ Request::is('attendance') ? 'active' : '' }}">
            <i class="nav-icon fas fa-dashboard"></i>
            <p>Attendance</p>
        </a>
    @endcan
</li>

<li class="nav-item">
    @if (auth()->user()->hasAnyPermission(['CRUD_packages', 'Read_packages']))
        <a href="{{ route('packages.index') }}" class="nav-link {{ Request::is('packages') ? 'active' : '' }}">
            <i class="nav-icon fas fa-dashboard"></i>
            <p>Packages</p>
        </a>
    @endif
</li>

<li class="nav-item">
    @can('Read_revenue')
        <a href="{{ route('revenue.index') }}" class="nav-link {{ Request::is('revenue') ? 'active' : '' }}">
            <i class="nav-icon fas fa-dashboard"></i>
            <p>Revenue</p>
        </a>
    @endcan
</li>
