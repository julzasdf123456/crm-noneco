<!-- MEMBERSHIP MENU -->
@canany(['Super Admin', 'view membership'])
    <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
            <i class="nav-icon fas fa-users"></i>
            <p>
                Membership
                <i class="fas fa-angle-left right"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="{{ route('memberConsumers.index') }}"
                class="nav-link {{ Request::is('memberConsumers*') ? 'active' : '' }}">
                <i class="fas fa-street-view nav-icon"></i><p>Member Consumers</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('memberConsumers.create') }}"
                class="nav-link {{ Request::is('memberConsumers*') ? 'active' : '' }}">
                <i class="fas fa-user-plus nav-icon"></i><p>Register New MCO</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('memberConsumerTypes.index') }}"
                class="nav-link {{ Request::is('memberConsumerTypes*') ? 'active' : '' }}">
                <i class="fas fa-code-branch nav-icon"></i><p>Consumer Types</p>
                </a>
            </li>
        </ul>
    </li>
    
@endcanany

<!-- SERVICE CONNECTION MENU -->
@canany(['Super Admin', 'sc view'])
    <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
            <i class="nav-icon fas fa-plug"></i>
            <p>
                Service Connections
                <i class="fas fa-angle-left right"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="{{ route('serviceConnections.index') }}"
                class="nav-link {{ Request::is('serviceConnections*') ? 'active' : '' }}">
                    <i class="fas fa-bolt nav-icon"></i>
                    <p>All Applications</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('serviceConnections.selectmembership') }}"
                class="nav-link {{ Request::is('serviceConnections.selectmembership') ? 'active' : '' }}">
                    <i class="fas fa-plus nav-icon"></i>
                    <p>New Application</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-cogs nav-icon"></i>
                    <p>
                        Settings
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview" style="display: none;">
                    <li class="nav-item">
                        <a href="{{ route('serviceConnectionAccountTypes.index') }}"
                        class="nav-link {{ Request::is('serviceConnectionAccountTypes*') ? 'active' : '' }}">
                        <i class="fas fa-code-branch nav-icon"></i><p>Account Types</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('serviceConnectionMatPayables.index') }}"
                        class="nav-link {{ Request::is('serviceConnectionMatPayables*') ? 'active' : '' }}">
                        <i class="fas fa-hammer nav-icon"></i><p>Material Payables</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('serviceConnectionPayParticulars.index') }}"
                        class="nav-link {{ Request::is('serviceConnectionPayParticulars*') ? 'active' : '' }}">
                        <i class="fas fa-shopping-cart nav-icon"></i><p>Payment Particulars</p>
                        </a>
                    </li>
                </ul>
              </li>
        </ul>
    </li>
@endcanany

<!-- EXTRAS MENU -->
@canany(['Super Admin', 'view membership', 'view complains', 'view service connections'])
    <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
            <i class="nav-icon fas fa-ellipsis-h"></i>
            <p>
                Extras
                <i class="fas fa-angle-left right"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="{{ route('towns.index') }}"
                class="nav-link {{ Request::is('towns*') ? 'active' : '' }}">
                <i class="fas fa-map-marker-alt nav-icon"></i><p>Towns</p>
                </a>
            </li>


            <li class="nav-item">
                <a href="{{ route('barangays.index') }}"
                class="nav-link {{ Request::is('barangays*') ? 'active' : '' }}">
                <i class="fas fa-map-marked-alt nav-icon"></i><p>Barangays</p>
                </a>
            </li>
        </ul>
    </li>
@endcanany

<!-- ADMIN MENU -->
@can('Super Admin')
    <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
            <i class="nav-icon fas fa-shield-alt"></i>
            <p>
                Administrator
                <i class="fas fa-angle-left right"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="{{ route('users.index') }}"
                class="nav-link {{ Request::is('users*') ? 'active' : '' }}">
                    <p><i class="fas fa-user-lock nav-icon"></i> Users</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('roles.index') }}"
                class="nav-link {{ Request::is('roles*') ? 'active' : '' }}">
                <i class="fas fa-unlock-alt nav-icon"></i><p>Roles</p>
                </a>
            </li>


            <li class="nav-item">
                <a href="{{ route('permissions.index') }}"
                class="nav-link {{ Request::is('permissions*') ? 'active' : '' }}">
                <i class="fas fa-key nav-icon"></i><p>Permissions</p>
                </a>
            </li>
        </ul>
    </li>
@endcan



