<!-- MEMBERSHIP MENU -->
@canany(['Super Admin', 'view membership'])
    <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
            <lord-icon
                src="https://cdn.lordicon.com/imamsnbq.json"
                trigger="loop-on-hover"
                delay="500"
                stroke="100"
                colors="primary:#ffffff,secondary:#ffffff"
                style="width:23px;height:23px">
            </lord-icon>
            <p>
                Membership
                <i class="fas fa-angle-left right"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="{{ route('memberConsumers.index') }}"
                class="nav-link {{ Request::is('memberConsumers.index*') ? 'active' : '' }}">
                <i class="fas fa-street-view nav-icon"></i><p>Member Consumers</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('memberConsumers.create') }}"
                class="nav-link {{ Request::is('memberConsumers.create*') ? 'active' : '' }}">
                <i class="fas fa-user-plus nav-icon"></i><p>Register New MCO</p>
                </a>
            </li>

            <li class="nav-header">                
                <lord-icon
                    src="https://cdn.lordicon.com/sbiheqdr.json"
                    trigger="loop"
                    colors="primary:#ffffff,secondary:#ffffff"
                    stroke="100"
                    delay="1800"
                    style="width:21px;height:21px">
                </lord-icon> Settings
            </li>

            <li class="nav-item">
                <a href="{{ route('memberConsumerTypes.index') }}"
                class="nav-link {{ Request::is('memberConsumerTypes*') ? 'active' : '' }}">
                <i class="fas fa-code-branch nav-icon"></i><p>Consumer Types</p>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('memberConsumerChecklistsReps.index') }}"
                class="nav-link {{ Request::is('memberConsumerChecklistsReps*') ? 'active' : '' }}">
                <i class="fas fa-check nav-icon"></i><p>Checklists</p>
                </a>
            </li>
        </ul>
    </li>
    
@endcanany

<!-- SERVICE CONNECTION MENU -->
@canany(['Super Admin', 'sc view'])
    <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
            <lord-icon
                src="https://cdn.lordicon.com/dbsklakl.json"
                trigger="hover"
                colors="primary:#ffffff,secondary:#ffffff"
                stroke="100"
                style="width:23px;height:23px">
            </lord-icon>
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
                    <lord-icon
                        src="https://cdn.lordicon.com/sbiheqdr.json"
                        trigger="loop"
                        colors="primary:#ffffff,secondary:#ffffff"
                        stroke="100"
                        delay="1800"
                        style="width:22px;height:22px">
                    </lord-icon>
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
                    
                    <li class="nav-item">
                        <a href="{{ route('serviceConnectionChecklistsReps.index') }}"
                        class="nav-link {{ Request::is('serviceConnectionChecklistsReps*') ? 'active' : '' }}">
                        <i class="fas fa-check nav-icon"></i><p>Checklists</p>
                        </a>
                    </li>

                </ul>
            </li>
        
            <li class="nav-item">
                <a href="{{ route('serviceConnections.trash') }}"
                class="nav-link {{ Request::is('serviceConnections.trash*') ? 'active' : '' }}">
                <i class="fas fa-trash nav-icon"></i><p>Trash</p>
                </a>
            </li>
        
        </ul>
    </li>
@endcanany

<!-- EXTRAS MENU -->
@canany(['Super Admin', 'view membership', 'view complains', 'view service connections'])
    <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
            <lord-icon
                src="https://cdn.lordicon.com/jvucoldz.json"
                trigger="hover"
                colors="primary:#ffffff,secondary:#ffffff"
                stroke="100"
                style="width:23px;height:23px">
            </lord-icon>
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
            <lord-icon
                src="https://cdn.lordicon.com/huwchbks.json"
                trigger="hover"
                colors="primary:#ffffff,secondary:#ffffff"
                stroke="100"
                style="width:23px;height:23px">
            </lord-icon>
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

