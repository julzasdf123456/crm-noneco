@php
    
use Illuminate\Support\Facades\Auth;

@endphp

<li class="nav-header">CRM</li>
<!-- MEMBERSHIP MENU -->
@canany(['Super Admin', 'view membership'])
    <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
            <i class="fas fa-users nav-icon text-success"></i>
            <p>
                Membership
                <i class="fas fa-angle-left right"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            @canany(['Super Admin', 'view membership'])
                <li class="nav-item">
                    <a href="{{ route('memberConsumers.index') }}"
                    class="nav-link {{ Request::is('memberConsumers.index*') ? 'active' : '' }}">
                    <i class="fas fa-street-view nav-icon text-success"></i><p>Member Consumers</p>
                    </a>
                </li>
            @endcanany

            @canany(['Super Admin', 'create membership'])
                <li class="nav-item">
                    <a href="{{ route('memberConsumers.create') }}"
                    class="nav-link {{ Request::is('memberConsumers.create*') ? 'active' : '' }}">
                    <i class="fas fa-user-plus nav-icon text-success"></i><p>Register New MCO</p>
                    </a>
                </li>
            @endcanany

            @canany(['Super Admin', 'update membership'])
                <li class="nav-header">                
                    Settings
                </li>

                <li class="nav-item">
                    <a href="{{ route('memberConsumerTypes.index') }}"
                    class="nav-link {{ Request::is('memberConsumerTypes*') ? 'active' : '' }}">
                    <i class="fas fa-code-branch nav-icon text-success"></i><p>Consumer Types</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="{{ route('memberConsumerChecklistsReps.index') }}"
                    class="nav-link {{ Request::is('memberConsumerChecklistsReps*') ? 'active' : '' }}">
                    <i class="fas fa-check nav-icon text-success"></i><p>Checklists</p>
                    </a>
                </li>
            @endcanany
        </ul>
    </li>    
@endcanany

<!-- SERVICE CONNECTION MENU -->
@canany(['Super Admin', 'sc view'])
    <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
            <i class="fas fa-plug nav-icon text-warning"></i>
            <p>
                Service Connections
                <i class="fas fa-angle-left right"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            @canany(['Super Admin', 'sc view'])
                <li class="nav-item">
                    <a href="{{ route('serviceConnections.dashboard') }}"
                    class="nav-link {{ Request::is('serviceConnections.dashboard*') ? 'active' : '' }}">
                        <i class="fas fa-chart-line nav-icon text-warning"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
            @endcanany

            @canany(['Super Admin', 'sc view'])
                <li class="nav-item">
                    <a href="{{ route('serviceConnections.index') }}"
                    class="nav-link {{ Request::is('serviceConnections*') ? 'active' : '' }}">
                        <i class="fas fa-bolt nav-icon text-warning"></i>
                        <p>All Applications</p>
                    </a>
                </li>
            @endcanany

            @canany(['Super Admin', 'sc create'])
                <li class="nav-item">
                    <a href="{{ route('serviceConnections.selectmembership') }}"
                    class="nav-link {{ Request::is('serviceConnections.selectmembership') ? 'active' : '' }}">
                        <i class="fas fa-plus nav-icon text-warning"></i>
                        <p>New Application</p>
                    </a>
                </li>
            @endcanany

            @if (Auth::user()->hasRole('Metering Personnel'))
            <li class="nav-item">
                <a href="{{ route('serviceConnectionMtrTrnsfrmrs.assigning') }}"
                class="nav-link {{ Request::is('serviceConnectionMtrTrnsfrmrs.assigning') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt nav-icon text-warning"></i>
                    <p>Assign Meters
                        <span id="assign-badge-count" class="right badge badge-danger">0</span>
                    </p>
                </a>
            </li>
            @endif

            @canany(['Super Admin', 'sc view'])
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <p>
                        Monitoring
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview" style="display: none;">
                    <li class="nav-item">
                        <a href="{{ route('serviceConnections.daily-monitor') }}"
                        class="nav-link {{ Request::is('serviceConnections.daily-monitor*') ? 'active' : '' }}">
                        <i class="fas fa-clipboard-check nav-icon text-warning"></i><p>Daily Monitor</p>
                        </a>
                    </li>

                </ul>
            </li>
            @endcanany

            @canany(['Super Admin', 'sc view'])
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <p>
                        Reports
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview" style="display: none;">
                    <li class="nav-item">
                        <a href="{{ route('serviceConnections.applications-report') }}"
                        class="nav-link {{ Request::is('serviceConnections.applications-report*') ? 'active' : '' }}">
                        <i class="fas fa-file-import nav-icon text-warning"></i><p>Applications</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('serviceConnections.energization-report') }}"
                        class="nav-link {{ Request::is('serviceConnections.energization-report*') ? 'active' : '' }}">
                        <i class="fas fa-charging-station nav-icon text-warning"></i><p>Energization</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('serviceConnections.daily-monitor') }}"
                        class="nav-link {{ Request::is('serviceConnections.daily-monitor*') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt nav-icon text-warning"></i><p>Meter Installation</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('serviceConnections.daily-monitor') }}"
                        class="nav-link {{ Request::is('serviceConnections.daily-monitor*') ? 'active' : '' }}">
                        <i class="fas fa-clipboard-list nav-icon text-warning"></i><p>Summary</p>
                        </a>
                    </li>
                </ul>
            </li>
            @endcanany

            @canany(['Super Admin', 'sc settings'])
            <li class="nav-item">
                <a href="#" class="nav-link">
                    {{-- <i class="fas fa-cogs"></i> --}}
                    <p>
                        Settings
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview" style="display: none;">
                    <li class="nav-item">
                        <a href="{{ route('serviceConnectionAccountTypes.index') }}"
                        class="nav-link {{ Request::is('serviceConnectionAccountTypes*') ? 'active' : '' }}">
                        <i class="fas fa-code-branch nav-icon text-warning"></i><p>Account Types</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('serviceConnectionMatPayables.index') }}"
                        class="nav-link {{ Request::is('serviceConnectionMatPayables*') ? 'active' : '' }}">
                        <i class="fas fa-hammer nav-icon text-warning"></i><p>Material Payables</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('serviceConnectionPayParticulars.index') }}"
                        class="nav-link {{ Request::is('serviceConnectionPayParticulars*') ? 'active' : '' }}">
                        <i class="fas fa-shopping-cart nav-icon text-warning"></i><p>Payment Particulars</p>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="{{ route('serviceConnectionChecklistsReps.index') }}"
                        class="nav-link {{ Request::is('serviceConnectionChecklistsReps*') ? 'active' : '' }}">
                        <i class="fas fa-check nav-icon text-warning"></i><p>Checklists</p>
                        </a>
                    </li>
                </ul>
            </li>
            @endcanany

            {{-- MATERIALS AND STRUCTURES --}}
            @canany(['Super Admin'])
            <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    {{-- <i class="fas fa-toolbox nav-icon"></i> --}}
                    <p>
                        Mat. & Structures
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('structures.index') }}"
                        class="nav-link {{ Request::is('structures*') ? 'active' : '' }}">
                        <i class="fas fa-draw-polygon nav-icon text-warning"></i><p>Structures</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('materialAssets.index') }}"
                        class="nav-link {{ Request::is('materialAssets*') ? 'active' : '' }}">
                        <i class="fas fa-plug nav-icon text-warning"></i><p>Material Assets</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('transformerIndices.index') }}"
                        class="nav-link {{ Request::is('transformerIndices*') ? 'active' : '' }}">
                        <i class="fas fa-car-battery nav-icon text-warning"></i><p>Transformer Index</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('poleIndices.index') }}"
                        class="nav-link {{ Request::is('poleIndices*') ? 'active' : '' }}">
                        <i class="fas fa-cross nav-icon text-warning"></i><p>Pole Index</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('spanningIndices.index') }}"
                        class="nav-link {{ Request::is('spanningIndices*') ? 'active' : '' }}">
                        <i class="fas fa-network-wired nav-icon text-warning"></i>
                        <p>Spanning Index</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('specialEquipmentMaterials.index') }}"
                        class="nav-link {{ Request::is('specialEquipmentMaterials*') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt nav-icon text-warning"></i><p>Special Eq. Materials</p>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="{{ route('preDefinedMaterials.index') }}"
                        class="nav-link {{ Request::is('preDefinedMaterials*') ? 'active' : '' }}">
                            <i class="fas fa-plug nav-icon text-warning"></i><p>Pre-Defined Materials</p>
                        </a>
                    </li>
                </ul>
            </li>
            @endcanany

            @canany(['Super Admin', 'sc view'])
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <p>
                        Others
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview" style="display: none;">
                    <li class="nav-item">
                        <a href="{{ route('serviceConnections.fleet-monitor') }}"
                        class="nav-link {{ Request::is('serviceConnections.fleet-monitor*') ? 'active' : '' }}">
                        <i class="fas fa-street-view nav-icon text-warning"></i><p>Fleet Monitoring</p>
                        </a>
                    </li>

                </ul>
            </li>
            @endcanany
            
            @canany(['Super Admin', 'sc delete'])
            <li class="nav-item">
                <a href="{{ route('serviceConnections.trash') }}"
                class="nav-link {{ Request::is('serviceConnections.trash*') ? 'active' : '' }}">
                <i class="fas fa-trash nav-icon text-warning"></i><p>Trash</p>
                </a>
            </li>
            @endcanany
        </ul>
    </li>
@endcanany

{{-- TICKETS --}}
@canany(['Super Admin'])
    <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
            <i class="fas fa-ambulance nav-icon text-danger"></i>
            <p>
                Tickets
                <i class="fas fa-angle-left right"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="{{ route('tickets.index') }}"
                class="nav-link {{ Request::is('tickets.index*') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-list nav-icon text-danger"></i><p>All Tickets</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('tickets.create-select') }}"
                class="nav-link {{ Request::is('tickets.create-select*') ? 'active' : '' }}">
                    <i class="fas fa-plus-circle nav-icon text-danger"></i><p>New Ticket</p>
                </a>
            </li>
            <li class="nav-header">                
                Settings and Others 
            </li>
            <li class="nav-item">
                <a href="{{ route('ticketsRepositories.index') }}"
                   class="nav-link {{ Request::is('ticketsRepositories*') ? 'active' : '' }}">
                   <i class="fas fa-check-circle nav-icon text-danger"></i><p>Ticket Types</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('tickets.trash') }}"
                   class="nav-link {{ Request::is('tickets.trash*') ? 'active' : '' }}">
                   <i class="fas fa-trash nav-icon text-danger"></i><p>Trash</p>
                </a>
            </li>
        </ul>
    </li>
    
@endcanany

{{-- DAMAGE ASSESSMENT --}}
@canany(['Super Admin', 'view damage monitor'])
    <li class="nav-item">
        <a href="{{ route('damageAssessments.index') }}"
        class="nav-link {{ Request::is('damageAssessments.index*') ? 'active' : '' }}">
            <i class="fas fa-house-damage nav-icon text-default"></i><p>Damage Assessment</p>
        </a>
    </li>    
@endcanany

<li class="nav-header">BILLING</li>
{{-- SERVICE ACCOUNTS --}}
@canany(['Super Admin'])
    <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
            <i class="fas fa-file-invoice-dollar nav-icon text-primary"></i>
            <p>
                Service Accounts
                <i class="fas fa-angle-left right"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="{{ route('serviceAccounts.index') }}"
                   class="nav-link {{ Request::is('serviceAccounts*') ? 'active' : '' }}">                   
                   <i class="fas fa-user-circle nav-icon text-primary"></i><p>Active Accounts</p>
                </a>
            </li>
            <li class="nav-header">                
                Meter Reading 
            </li>
            <li class="nav-item">
                <a href="{{ route('readingSchedules.index') }}"
                   class="nav-link {{ Request::is('readingSchedules.index*') ? 'active' : '' }}">                   
                   <i class="fas fa-calendar-week nav-icon text-primary"></i><p>Reading Scheduler</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('meterReaderTrackNames.index') }}"
                   class="nav-link {{ Request::is('meterReaderTrackNames.index*') ? 'active' : '' }}">                   
                   <i class="fas fa-map-marked-alt nav-icon text-primary"></i><p>M. Reader Tracks</p>
                </a>
            </li>
            <li class="nav-header">                
                Others 
            </li>
            <li class="nav-item">
                <a href="{{ route('serviceAccounts.pending-accounts') }}"
                   class="nav-link {{ Request::is('serviceAccounts.pending-accounts*') ? 'active' : '' }}">                   
                   <i class="fas fa-user-alt-slash nav-icon text-primary"></i><p>Pending Accounts</p>
                </a>
            </li>
            
        </ul>
    </li>
@endcanany

<li class="nav-header">MISCELLANEOUS</li>
<!-- EXTRAS MENU -->
@canany(['Super Admin', 'create membership', 'sc create'])
    <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
            <i class="fas fa-ellipsis-v nav-icon text-info"></i>
            <p>
                Extras
                <i class="fas fa-angle-left right"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="{{ route('towns.index') }}"
                class="nav-link {{ Request::is('towns*') ? 'active' : '' }}">
                <i class="fas fa-map-marker-alt nav-icon text-info"></i><p>Towns</p>
                </a>
            </li>


            <li class="nav-item">
                <a href="{{ route('barangays.index') }}"
                class="nav-link {{ Request::is('barangays*') ? 'active' : '' }}">
                <i class="fas fa-map-marked-alt nav-icon text-info"></i><p>Barangays</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('serviceConnectionCrews.index') }}"
                   class="nav-link {{ Request::is('serviceConnectionCrews*') ? 'active' : '' }}">
                   <i class="fas fa-map-marked-alt nav-icon text-info"></i><p>Station Crews</p>
                </a>
            </li>
        </ul>
    </li>
@endcanany

<li class="nav-header">ADMINISTRATIVE</li>
<!-- ADMIN MENU -->
@can('Super Admin')
    <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
            <i class="fas fa-shield-alt nav-icon"></i>
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



