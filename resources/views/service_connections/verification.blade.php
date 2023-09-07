@php
    use App\Models\Users;

    if ($serviceConnectionInspections != null) {
        $inspector = Users::find($serviceConnectionInspections->Inspector);
    } else {
        $inspector = null;
    }
    
@endphp

@if ($serviceConnectionInspections != null)

@if ($serviceConnectionInspections->Status == 'FOR INSPECTION') 
    <a href="{{ route('serviceConnections.bypass-approve-inspection', [$serviceConnectionInspections->id]) }}" class="btn btn-sm btn-warning" style="margin-bottom: 5px;"><i class="fas fa-check ico-tab"></i>Approve This Application</a>    
@endif

<!-- Inspection Details -->
<div class="card card-primary card-outline">
    <div class="card-header border-0">
        <h3 class="card-title">Inspection Details</h3>
        <div class="card-tools">
            @if($serviceConnectionInspections == null)
                @if (Auth::user()->hasAnyRole(['Administrator', 'Heads and Managers', 'Inspector', 'Service Connection Assessor'])) 
                    <a href="" class="btn btn-sm" title="Add Verification Details"><i class="fas fa-plus-square"></i></a>
                @endif
            @else
                @if (Auth::user()->hasAnyRole(['Administrator', 'Heads and Managers', 'Inspector', 'Service Connection Assessor'])) 
                    <a href="{{ route('serviceConnectionInspections.edit', [$serviceConnectionInspections->id]) }}" class="btn btn-sm" title="Update Verification Details">
                        <i class="fas fa-pen"></i>
                    </a>
                @endif

                <button type="button" class="btn btn-sm" data-card-widget="collapse" title="Collapse"><i class="fas fa-minus"></i></button>
            @endif
        </div>
    </div>

    <div class="card-body table-responsive p-0">
        <table class="table table-valign-middle">
            <tbody>
                <tr>
                    <td>Status</td>
                    <td>{{ $serviceConnectionInspections->Status }}</td>
                </tr>
                <tr>
                    <td>Inspector</td>
                    <td>{{ $inspector != null ? $inspector->name : '-' }}</td>
                </tr>
                <tr>
                    <td>Inspection Date</td>
                    <td>{{ $serviceConnectionInspections->DateOfVerification != null ? date('F d, Y', strtotime($serviceConnectionInspections->DateOfVerification)) : ''}}</td>
                </tr>
                <tr>
                    <td>Notes and Remarks</td>
                    <td>{{ $serviceConnectionInspections->Notes }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>


<!-- Connection -->
<div class="card card-primary card-outline">
    <div class="card-header border-0">
        <h3 class="card-title">Breaker and Service Drop Wire</h3>
        <div class="card-tools">
            @if($serviceConnectionInspections == null)
                @if (Auth::user()->hasAnyRole(['Administrator', 'Heads and Managers', 'Inspector', 'Service Connection Assessor'])) 
                    <a href="" class="btn btn-sm" title="Add Verification Details"><i class="fas fa-plus-square"></i></a>
                @endif
            @else
                @if (Auth::user()->hasAnyRole(['Administrator', 'Heads and Managers', 'Inspector', 'Service Connection Assessor'])) 
                    <a href="{{ route('serviceConnectionInspections.edit', [$serviceConnectionInspections->id]) }}" class="btn btn-sm" title="Update Verification Details">
                        <i class="fas fa-pen"></i>
                    </a>
                @endif
                <button type="button" class="btn btn-sm" data-card-widget="collapse" title="Collapse"><i class="fas fa-minus"></i></button>
            @endif
        </div>
    </div>

    <div class="card-body table-responsive p-0">
        <table class="table table-valign-middle">
            <thead>
                <tr>
                    <th></th>
                    <th>Planned</th>
                    <th>Installed</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Service Entrance Main Breaker</td>
                    <td>{{ $serviceConnectionInspections->SEMainCircuitBreakerAsPlan==null ? '-' : $serviceConnectionInspections->SEMainCircuitBreakerAsPlan . ' amps' }}</td>
                    <td>{{ $serviceConnectionInspections->SEMainCircuitBreakerAsInstalled==null ? '-' : $serviceConnectionInspections->SEMainCircuitBreakerAsInstalled . ' amps' }}</td>
                </tr>
                <tr>
                    <td>Number of Breaker Branches</td>
                    <td>{{ $serviceConnectionInspections->SENoOfBranchesAsPlan==null ? '-' : $serviceConnectionInspections->SENoOfBranchesAsPlan }}</td>
                    <td>{{ $serviceConnectionInspections->SENoOfBranchesAsInstalled==null ? '-' : $serviceConnectionInspections->SENoOfBranchesAsInstalled }}</td>
                </tr>
                <tr>
                    <td>Service Drop Wire Size</td>
                    <td>{{ $serviceConnectionInspections->SDWSizeAsPlan==null ? '-' : $serviceConnectionInspections->SDWSizeAsPlan . ' mm' }}</td>
                    <td>{{ $serviceConnectionInspections->SDWSizeAsInstalled==null ? '-' : $serviceConnectionInspections->SDWSizeAsInstalled . ' mm' }}</td>
                </tr>
                <tr>
                    <td>Service Drop Wire Length</td>
                    <td>{{ $serviceConnectionInspections->SDWLengthAsPlan==null ? '-' : $serviceConnectionInspections->SDWLengthAsPlan . ' m' }}</td>
                    <td>{{ $serviceConnectionInspections->SDWLengthAsInstalled==null ? '-' : $serviceConnectionInspections->SDWLengthAsInstalled . ' m' }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Pole -->
<div class="card card-primary card-outline">
    <div class="card-header border-0">
        <h3 class="card-title">Pole Data (Pole Number: {{ $serviceConnectionInspections->PoleNumber==null ? 'not specified' : $serviceConnectionInspections->PoleNumber }})</h3>
        <div class="card-tools">
            @if($serviceConnectionInspections == null)
                @if (Auth::user()->hasAnyRole(['Administrator', 'Heads and Managers', 'Inspector', 'Service Connection Assessor'])) 
                    <a href="" class="btn btn-sm" title="Add Verification Details"><i class="fas fa-plus-square"></i></a>
                @endif
            @else
                @if (Auth::user()->hasAnyRole(['Administrator', 'Heads and Managers', 'Inspector', 'Service Connection Assessor'])) 
                    <a href="{{ route('serviceConnectionInspections.edit', [$serviceConnectionInspections->id]) }}" class="btn btn-sm" title="Update Verification Details">
                        <i class="fas fa-pen"></i>
                    </a>
                @endif
                <button type="button" class="btn btn-sm" data-card-widget="collapse" title="Collapse"><i class="fas fa-minus"></i></button>
            @endif
        </div>
    </div>

    <div class="card-body table-responsive p-0">
        <table class="table table-valign-middle">
            <thead>
                <tr>
                    <th></th>
                    <th>Wood</th>
                    <th>Concrete</th>
                    <th>GI</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Quantity</td>
                    <td>{{ $serviceConnectionInspections->PoleHardwoodNoOfLiftPoles }}</td>
                    <td>{{ $serviceConnectionInspections->PoleConcreteNoOfLiftPoles }}</td>
                    <td>{{ $serviceConnectionInspections->PoleGINoOfLiftPoles }}</td>
                </tr>
                <tr>
                    <td>Diameter</td>
                    <td>{{ $serviceConnectionInspections->PoleHardwoodEstimatedDiameter }}</td>
                    <td>{{ $serviceConnectionInspections->PoleConcreteEstimatedDiameter }}</td>
                    <td>{{ $serviceConnectionInspections->PoleGIEstimatedDiameter }}</td>
                </tr>
                <tr>
                    <td>Height</td>
                    <td>{{ $serviceConnectionInspections->PoleHardwoodHeight }}</td>
                    <td>{{ $serviceConnectionInspections->PoleConcreteHeight }}</td>
                    <td>{{ $serviceConnectionInspections->PoleGIHeight }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Geo Tagging -->
<div class="card card-primary card-outline">
    <div class="card-header border-0">
        <h3 class="card-title">Geo Tagging and Neighboring</h3>
        <div class="card-tools">
            @if($serviceConnectionInspections == null)
                @if (Auth::user()->hasAnyRole(['Administrator', 'Heads and Managers', 'Inspector', 'Service Connection Assessor'])) 
                    <a href="" class="btn btn-sm" title="Add Verification Details"><i class="fas fa-plus-square"></i></a>
                @endif
            @else
                @if (Auth::user()->hasAnyRole(['Administrator', 'Heads and Managers', 'Inspector', 'Service Connection Assessor'])) 
                    <a href="{{ route('serviceConnectionInspections.edit', [$serviceConnectionInspections->id]) }}" class="btn btn-sm" title="Update Verification Details">
                        <i class="fas fa-pen"></i>
                    </a>
                @endif
                <button type="button" class="btn btn-sm" data-card-widget="collapse" title="Collapse"><i class="fas fa-minus"></i></button>
            @endif
        </div>
    </div>

    <div class="card-body table-responsive p-0">
        <table class="table table-valign-middle">
            <thead>
                <th>Structure</th>
                <th>Location</th>
                <td></td>
            </thead>
            <tbody>
                <tr>
                    <td>Building</td>
                    <td id="geobuilding">{{ $serviceConnectionInspections->GeoBuilding }}</td>
                </tr>
                <tr>
                    <td>Tapping Pole</td>
                    <td id="geotappingpole">{{ $serviceConnectionInspections->GeoTappingPole }}</td>
                </tr>
                <tr>
                    <td>Metering Pole</td>
                    <td id="geometering">{{ $serviceConnectionInspections->GeoMeteringPole }}</td>
                </tr>
                <tr>
                    <td>Service Entrance Pole</td>
                    <td id="geosepole">{{ $serviceConnectionInspections->GeoSEPole }}</td>
                </tr>

                <tr>
                    <td>Nearest Neighbor 1</td>
                    <td>{{ $serviceConnectionInspections->FirstNeighborName }}</td>
                </tr>
                <tr>
                    <td>Nearest Neighbor 1 Meter #</td>
                    <td>{{ $serviceConnectionInspections->FirstNeighborMeterSerial }}</td>
                </tr>
                <tr>
                    <td>Nearest Neighbor 2</td>
                    <td>{{ $serviceConnectionInspections->SecondNeighborName }}</td>
                </tr>
                <tr>
                    <td>Nearest Neighbor 2 Meter #</td>
                    <td>{{ $serviceConnectionInspections->SecondNeighborMeterSerial }}</td>
                </tr>
            </tbody>
            
        </table>

        <div id='map' style="width: 100%; height: 400px;"></div>
    </div>
</div>
@else 
<p class="text-center"><i>No inspection data found!</i></p>
    @if (Auth::user()->hasAnyRole(['Administrator', 'Heads and Managers', 'Inspector', 'Service Connection Assessor'])) 
        <a href="{{ route('serviceConnectionInspections.create-step-two', [$serviceConnections->id]) }}" class="btn btn-primary btn-sm" title="Add Verification Details"><i class="fas fa-pen ico-tab"></i>Create Verification</a>
    @endif
@endif

@push('page_scripts')
    <script src='https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.js'></script>

    <link href='https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.css' rel='stylesheet' />

    <script type="text/javascript">
        function getLocData() {
            var centerLoc = "";

            if ($("#geobuilding").text() === "") {
                if ($('#geometering').text() === "") {
                    centerLoc = $('#geotappingpole').text();
                } else {
                    centerLoc = $('#geometering').text();
                }            
            } else {
                centerLoc = $("#geobuilding").text();
            }

            return centerLoc;
        }

        $(document).ready(function() {
            // MAPBOX
            mapboxgl.accessToken = 'pk.eyJ1IjoianVsemxvcGV6IiwiYSI6ImNqZzJ5cWdsMjJid3Ayd2xsaHcwdGhheW8ifQ.BcTcaOXmXNLxdO3wfXaf5A';

            var centerLoc = getLocData();

            var map = new mapboxgl.Map({
                container: 'map',
                zoom: 15,
                center: [centerLoc.split(",")[1], centerLoc.split(",")[0]],
                style: 'mapbox://styles/mapbox/satellite-v9'
            });

            map.once('idle',function(){
                if (!jQuery.isEmptyObject($('#geobuilding').text())) {
                    const markerBldg = new mapboxgl.Marker()
                        .setLngLat([$('#geobuilding').text().split(",")[1], $('#geobuilding').text().split(",")[0]])
                        .addTo(map);
                }

                if (!jQuery.isEmptyObject($('#geometering').text())) {
                    const markerMetering = new mapboxgl.Marker()
                        .setLngLat([$('#geometering').text().split(",")[1], $('#geometering').text().split(",")[0]])
                        .addTo(map);
                }

                if (!jQuery.isEmptyObject($('#geotappingpole').text())) {
                    const markerTapping = new mapboxgl.Marker()
                        .setLngLat([$('#geotappingpole').text().split(",")[1], $('#geotappingpole').text().split(",")[0]])
                        .addTo(map);
                }

                if (!jQuery.isEmptyObject($('#geosepole').text())) {
                    const markerSe = new mapboxgl.Marker()
                        .setLngLat([$('#geosepole').text().split(",")[1], $('#geosepole').text().split(",")[0]])
                        .addTo(map);
                }
            });    

            
        });
    </script>
@endpush

