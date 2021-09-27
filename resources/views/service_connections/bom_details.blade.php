<div class="row">
    <div class="col-lg-12 col-sm-12">
        {{-- TOOLBAR --}}
        <span>
            <a href="{{ route('serviceConnections.spanning-assigning', [$serviceConnections->id]) }}" class="btn btn-sm btn-default"><i class="fas fa-network-wired"></i> Edit Spanning</a>
            <a href="{{ route('serviceConnections.bom-assigning', [$serviceConnections->id]) }}" class="btn btn-sm btn-default"><i class="fas fa-file-invoice-dollar"></i> Edit Materials</a>
            <a href="{{ route('serviceConnections.forward-to-transformer-assigning', [$serviceConnections->id]) }}" class="btn btn-sm btn-default"><i class="fas fa-car-battery"></i> Edit Transformer</a>
            <a href="{{ route('serviceConnections.pole-assigning', [$serviceConnections->id]) }}" class="btn btn-sm btn-default"><i class="fas fa-cross"></i> Edit Pole</a>
        </span>
    </div>
</div>