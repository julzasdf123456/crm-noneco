@php
    use App\Models\Users;

    if ($serviceConnectionInspections != null) {
        $inspector = Users::find($serviceConnectionInspections->Inspector);
    } else {
        $inspector = null;
    }
    
@endphp

@if ($serviceConnectionInspections != null)

<!-- Inspection Details -->
<div class="card card-primary card-outline">
    <div class="card-header border-0">
        <h3 class="card-title">Inspection Details</h3>
        <div class="card-tools">
            @if($serviceConnectionInspections == null)
                <a href="" class="btn btn-sm" title="Add Verification Details"><i class="fas fa-plus-square"></i></a>
            @else
                <a href="{{ route('serviceConnectionInspections.edit', [$serviceConnectionInspections->id]) }}" class="btn btn-sm" title="Update Verification Details"><i class="fas fa-pen"></i></a>
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
                    <td>{{ $inspector->name }}</td>
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
                <a href="" class="btn btn-sm" title="Add Verification Details"><i class="fas fa-plus-square"></i></a>
            @else
                <a href="{{ route('serviceConnectionInspections.edit', [$serviceConnectionInspections->id]) }}" class="btn btn-sm" title="Update Verification Details"><i class="fas fa-pen"></i></a>
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
        <h3 class="card-title">Pole Data</h3>
        <div class="card-tools">
            @if($serviceConnectionInspections == null)
                <a href="" class="btn btn-sm" title="Add Verification Details"><i class="fas fa-plus-square"></i></a>
            @else
                <a href="{{ route('serviceConnectionInspections.edit', [$serviceConnectionInspections->id]) }}" class="btn btn-sm" title="Update Verification Details"><i class="fas fa-pen"></i></a>
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
                <a href="" class="btn btn-sm" title="Add Verification Details"><i class="fas fa-plus-square"></i></a>
            @else
                <a href="{{ route('serviceConnectionInspections.edit', [$serviceConnectionInspections->id]) }}" class="btn btn-sm" title="Update Verification Details"><i class="fas fa-pen"></i></a>
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
                    <td>{{ $serviceConnectionInspections->GeoBuilding }}</td>
                    <td width="10"><a href="#" title="View in map"><i class="fas fa-directions"></i></a></td>
                </tr>
                <tr>
                    <td>Tapping Pole</td>
                    <td>{{ $serviceConnectionInspections->GeoTappingPole }}</td>
                    <td width="10"><a href="#" title="View in map"><i class="fas fa-directions"></i></a></td>
                </tr>
                <tr>
                    <td>Metering Pole</td>
                    <td>{{ $serviceConnectionInspections->GeoMeteringPole }}</td>
                    <td width="10"><a href="#" title="View in map"><i class="fas fa-directions"></i></a></td>
                </tr>
                <tr>
                    <td>Service Entrance Pole</td>
                    <td>{{ $serviceConnectionInspections->GeoSEPole }}</td>
                    <td width="10"><a href="#" title="View in map"><i class="fas fa-directions"></i></a></td>
                </tr>

                <tr>
                    <td>Nearest Neighbor 1</td>
                    <td>{{ $serviceConnectionInspections->FirstNeighborName }}</td>
                    <td width="10"></td>
                </tr>
                <tr>
                    <td>Nearest Neighbor 1 Meter #</td>
                    <td>{{ $serviceConnectionInspections->FirstNeighborMeterSerial }}</td>
                    <td width="10"></td>
                </tr>
                <tr>
                    <td>Nearest Neighbor 2</td>
                    <td>{{ $serviceConnectionInspections->SecondNeighborName }}</td>
                    <td width="10"></td>
                </tr>
                <tr>
                    <td>Nearest Neighbor 2 Meter #</td>
                    <td>{{ $serviceConnectionInspections->SecondNeighborMeterSerial }}</td>
                    <td width="10"></td>
                </tr>
            </tbody>
            
        </table>
    </div>
</div>
@else 
<p class="text-center"><i>No inspection data found!</i></p>
<a href="{{ route('serviceConnectionInspections.create-step-two', [$serviceConnections->id]) }}" class="btn btn-primary btn-sm" title="Add Verification Details"><i class="fas fa-pen ico-tab"></i>Create Verification</a>
@endif

