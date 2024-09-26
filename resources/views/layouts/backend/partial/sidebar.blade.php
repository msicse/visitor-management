<aside id="leftsidebar" class="sidebar">
    <!-- User Info -->
    <div class="user-info">
        <div class="image">
            <img src=" {{ asset('images/users/' . Auth::user()->image) }}" width="50" height="50" alt="User" />
        </div>
        <div class="info-container">
            <div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{ Auth::user()->name }}</div>
            <div class="email">{{ Auth::user()->email }}</div>
            <div class="btn-group user-helper-dropdown">
                <i class="material-icons" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="true">keyboard_arrow_down</i>
                <ul class="dropdown-menu pull-right">
                    <li><a href=""><i class="material-icons">person</i>Profile</a></li>
                    <li>
                        <a href="">
                            <i class="material-icons">enhanced_encryption</i>
                            Change Pass
                        </a>
                    </li>


                    <li role="seperator" class="divider"></li>
                    <li><a href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();"><i
                                class="material-icons">input</i>Sign Out</a></li>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>

                </ul>
            </div>
        </div>
    </div>
    <!-- #User Info -->

    <!-- Menu -->
    <div class="menu">
        <ul class="list">
            <li class="header">MAIN NAVIGATION</li>

            <li class="{{ request()->is('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}">
                    <i class="material-icons">dashboard</i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="{{ Request::is('departments*') ? 'active' : '' }}">
                <a href="{{ route('departments.index') }}">
                    <i class="material-icons">corporate_fare</i>
                    <span>Departments</span>
                </a>
            </li>
            <li class="{{ request()->is('employees*') ? 'active' : '' }}">
                <a href="{{ route('employees.index') }}">
                    <i class="material-icons">wc</i>
                    <span>Employees</span>
                </a>
            </li>

            <li class="{{ request()->is('visitors*') ? 'active' : '' }}">
                <a href="{{ route('visitors.index') }}">
                    <i class="material-icons">wc</i>
                    <span>Visitors</span>
                </a>
            </li>
            <li class="{{ request()->is('pending-visitors*') ? 'active' : '' }}">
                <a href="{{ route('visitors.pending') }}">
                    <i class="material-icons">wc</i>
                    <span>Pending Visitors</span>
                </a>
            </li>

        </ul>
    </div>
    <!-- #Menu -->
    <!-- Footer -->
    <div class="legal">
        <div class="copyright">
            &copy;
            <script>
                document.write(new Date().getFullYear());
            </script>
            All rights reserved | by
            <a href="/">RSC</a>
        </div>
        <div class="version">

        </div>
    </div>
    <!-- #Footer -->
</aside>

Copyright ©
