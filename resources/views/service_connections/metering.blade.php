<div class="card card-primary card-outline">
    <div class="card-header border-0">
        <h3 class="card-title">Meter Details</h3>
        <div class="card-tools">
            @if($serviceConnectionMeter == null)
                <a href="{{ route('serviceConnectionMtrTrnsfrmrs.create-step-three', [$serviceConnections->id]) }}" class="btn btn-sm" title="Add Metering Details"><i class="fas fa-plus-square"></i></a>
            @else
                <a href="{{ route('serviceConnectionMtrTrnsfrmrs.edit', [$serviceConnectionMeter->id]) }}" class="btn btn-sm" title="Update Metering Details"><i class="fas fa-pen"></i></a>
                <button type="button" class="btn btn-sm" data-card-widget="collapse" title="Collapse"><i class="fas fa-minus"></i></button>
            @endif           
        </div>
    </div>

    <div class="card-body table-responsive p-0">
        @if($serviceConnectionMeter == null)
            <p class="text-center"><i>No metering data found!</i></p>
        @else
            <table class="table table-valign-middle">
                <tr>
                    <th>Metering Type</th>
                    <td>{{ $serviceConnectionMeter->TypeOfMetering }}</td>
                </tr>
                <tr>
                    <th>Brand</th>
                    <td>{{ $serviceConnectionMeter->MeterBrand }}</td>
                </tr>
                <tr>
                    <th>Serial Number</th>
                    <td>{{ $serviceConnectionMeter->MeterSerialNumber }}</td>
                </tr>
                <tr>
                    <th>Seal Number</th>
                    <td>{{ $serviceConnectionMeter->MeterSealNumber }}</td>
                </tr>
                <tr>
                    <th>Enclosure Type</th>
                    <td>{{ $serviceConnectionMeter->MeterEnclosureType }}</td>
                </tr>
                <tr>
                    <th>Height</th>
                    <td>{{ $serviceConnectionMeter->MeterHeight==null ? '' :  $serviceConnectionMeter->MeterHeight . ' meter'}}</td>
                </tr>
                <tr>
                    <th>kWh Start</th>
                    <td>{{ $serviceConnectionMeter->MeterKwhStart }}</td>
                </tr>               
                @if ($serviceConnectionMeter->TypeOfMetering == 'DIRECT')
                    <tr>
                        <th>Metering Equipment Capacity</th>
                        <td>{{ $serviceConnectionMeter->DirectRatedCapacity }}</td>
                    </tr>  
                    <tr>
                        <th>Phase</th>
                        <td>{{ $serviceConnectionMeter->Phase }}</td>
                    </tr> 
                @elseif ($serviceConnectionMeter->TypeOfMetering == 'INSTRUMENT RATED')
                    <tr>
                        <th>Metering Equipment Capacity</th>
                        <td>{{ $serviceConnectionMeter->InstrumentRatedCapacity }}</td>
                    </tr> 
                    <tr>
                        <th>Phase</th>
                        <td>{{ $serviceConnectionMeter->Phase }}</td>
                    </tr>
                    <tr>
                        <th>Line Type</th>
                        <td>{{ $serviceConnectionMeter->InstrumentRatedLineType }}</td>
                    </tr>
                @else

                @endif
                <tr>
                    <th>Notes and Remarks</th>
                    <td>{{ $serviceConnectionMeter->MeterNotes }}</td>
                </tr>
            </table>
        @endif        
    </div>
</div>

<div class="card card-primary card-outline">
    <div class="card-header border-0">
        <h3 class="card-title">Transformer Details</h3>
        <div class="card-tools">
            @if($serviceConnectionMeter == null)
                <a href="{{ route('serviceConnectionMtrTrnsfrmrs.create-step-three', [$serviceConnections->id]) }}" class="btn btn-sm" title="Add Transformer Details"><i class="fas fa-plus-square"></i></a>
            @else
                <a href="{{ route('serviceConnectionMtrTrnsfrmrs.edit', [$serviceConnectionMeter->id]) }}" class="btn btn-sm" title="Update Transformer Details"><i class="fas fa-pen"></i></a>
                <button type="button" class="btn btn-sm" data-card-widget="collapse" title="Collapse"><i class="fas fa-minus"></i></button>
            @endif  
        </div>
    </div>

    <div class="card-body table-responsive p-0">
        @if($serviceConnectionMeter == null)
            <p class="text-center"><i>No transformer data found!</i></p>
        @else
            <table class="table table-valign-middle">
                <tr>
                    <th>Brand</th>
                    <td>{{ $serviceConnectionMeter->TransformerBrand }}</td>
                </tr>
                <tr>
                    <th>Rating</th>
                    <td>{{ $serviceConnectionMeter->TransformerRating }}</td>
                </tr>
                <tr>
                    <th>Quantity</th>
                    <td>{{ $serviceConnectionMeter->TransformerQuantity }}</td>
                </tr>
                <tr>
                    <th>Ownership</th>
                    <td>{{ $serviceConnectionMeter->TransformerOwnership }}</td>
                </tr>
                <tr>
                    <th>Ownership Type</th>
                    <td>{{ $serviceConnectionMeter->TransformerOwnershipType }}</td>
                </tr>
            </table>
        @endif        
    </div>
</div>

@if ($serviceConnectionMeter != null)
    @if ($serviceConnectionMeter->TypeOfMetering == 'INSTRUMENT RATED')
    <div class="card card-primary card-outline">
        <div class="card-header border-0">
            <h3 class="card-title">Equipment and Others</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-sm" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>

        <div class="card-body table-responsive p-0">
            @if($serviceConnectionMeter == null)
                <p><i>No data found!</i></p>
            @else
                <table class="table table-valign-middle">
                    <thead>
                        <th></th>
                        <th>Phase A</th>
                        <th>Phase B</th>
                        <th>Phase C</th>
                    </thead>
                    <tbody>
                        <tr>
                            <th>CT Rated Capacity</th>
                            <td>{{ $serviceConnectionMeter->CTPhaseA }}</td>
                            <td>{{ $serviceConnectionMeter->CTPhaseB }}</td>
                            <td>{{ $serviceConnectionMeter->CTPhaseC }}</td>
                        </tr>
                        <tr>
                            <th>PT Rated Capacity</th>
                            <td>{{ $serviceConnectionMeter->PTPhaseA }}</td>
                            <td>{{ $serviceConnectionMeter->PTPhaseB }}</td>
                            <td>{{ $serviceConnectionMeter->PTPhaseC }}</td>
                        </tr>
                        <tr>
                            <th>Brand</th>
                            <td>{{ $serviceConnectionMeter->BrandPhaseA }}</td>
                            <td>{{ $serviceConnectionMeter->BrandPhaseB }}</td>
                            <td>{{ $serviceConnectionMeter->BrandPhaseC }}</td>
                        </tr>
                        <tr>
                            <th>Serial Number</th>
                            <td>{{ $serviceConnectionMeter->SNPhaseA }}</td>
                            <td>{{ $serviceConnectionMeter->SNPhaseB }}</td>
                            <td>{{ $serviceConnectionMeter->SNPhaseC }}</td>
                        </tr>
                        <tr>
                            <th>Security Seal Number</th>
                            <td>{{ $serviceConnectionMeter->SecuritySealPhaseA }}</td>
                            <td>{{ $serviceConnectionMeter->SecuritySealPhaseB }}</td>
                            <td>{{ $serviceConnectionMeter->SecuritySealPhaseC }}</td>
                        </tr>
                    </tbody>
                </table>
            @endif        
        </div>
    </div>
    @endif
@endif

