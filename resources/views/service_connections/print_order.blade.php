
@php
    use App\Models\ServiceConnections;
    use App\Models\Users;

    if ($serviceConnectionInspections != null) {
        $inspector = Users::find($serviceConnectionInspections->Inspector);
    } else {
        $inspector = null;
    }
@endphp

<style>
    @media print {
        @page {
            margin: 10px;
        }

        header {
            display: none;
        }

        .divider {
            width: 100%;
            margin: 10px auto;
            height: 1px;
            background-color: #dedede;
        }

        .map {
            width: 90% auto;
            height: 400px;
        }
    }  

    .divider {
        width: 100%;
        margin: 10px auto;
        height: 1px;
        background-color: #dedede;
    } 

    .map {
        width: 90% auto;
        height: 400px;
    }
</style>

<script src='https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.js'></script>
<link href='https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.css' rel='stylesheet' />

<!-- AdminLTE -->
<link rel="stylesheet" href="https://adminlte.io/themes/v3/dist/css/adminlte.min.css"/>

<div id="print-area">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="invoice p-3 mb-3">
                <div class="row">
                    <div class="col-12">
                        <h4>
                            <i class="fas fa-globe"></i>Name of The Company, Co.
                            <small class="float-right">Date: {{ date('F d, Y') }}</small>
                        </h4>
                        <p><strong>Energization Order</strong></p>
                    </div>
                </div>

                <hr>

                <p><i>Application Basic Details</i></p>

                <div class="row invoice-info">
                    <div class="col-sm-4 invoice-col">
                        Issued From
                        <address>
                            <strong>The Company</strong><br>
                            Street, Barangay, Town, Province<br>
                            Tin No Here: #########<br>
                            Phone Here: ########<br>
                            Email Here: ##########
                        </address>
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-4 invoice-col">
                        Issued To
                        <address>
                            <strong>{{ $serviceConnection->ServiceAccountName }}</strong><br>
                            {{ ServiceConnections::getAddress($serviceConnection) }}<br>
                            Order No: <strong>{{ $serviceConnection->id }}</strong><br>
                            Phone: {{ $serviceConnection->ContactNumber }}<br>
                            Application Type: {{ $serviceConnection->AccountType }}<br>
                        </address>
                    </div>
                    <!-- /.col -->

                    <div class="col-sm-4 invoice-col">
                        Date/Time of:
                        <address>
                            <strong>Application:</strong> {{ date('F d, Y', strtotime($serviceConnection->DateOfApplication)) }}<br>
                            <strong>Inspection:</strong> {{ date('F d, Y', strtotime($serviceConnectionInspections->DateOfVerification)) }}<br>
                            <strong>Payment:</strong> {{ date('F d, Y', strtotime($serviceConnection->ORDate)) }}<br>
                            <strong>Crew Arrival:</strong> _______________________<br>
                            <strong>Energized:</strong> __________________________<br>
                        </address>
                    </div>
                    <!-- /.col -->
                </div>

                <hr>

                <p><i>Technical</i></p>

                <div class="row invoice-info">
                    <div class="col-sm-4 invoice-col">
                        <address>
                            <strong>Inspector:</strong> {{ $inspector->name }}<br>
                            <strong>Main Breaker: </strong> {{ $serviceConnectionInspections->SEMainCircuitBreakerAsInstalled }} amps<br>
                            <strong>Branches: </strong> {{ $serviceConnectionInspections->SENoOfBranchesAsInstalled }}<br>
                            <strong>SDW Size: </strong> {{ $serviceConnectionInspections->SDWSizeAsInstalled }}<br>
                            <strong>SDW Length: </strong> {{ $serviceConnectionInspections->SDWLengthAsInstalled }} meters<br>
                        </address>
                    </div>

                    <div class="col-sm-4 invoice-col">
                        <address>
                            <strong>Meter Brand:</strong> {{ $serviceConnectionMeter->MeterBrand }}<br>
                            <strong>Serial No: </strong> {{ $serviceConnectionMeter->MeterSerialNumber }}<br>
                            <strong>Transformer Brand: </strong> {{ $serviceConnectionMeter->TransformerBrand }}<br>
                            <strong>Ownership: </strong> {{ $serviceConnectionMeter->TransformerOwnership }} ({{ $serviceConnectionMeter->TransformerOwnershipType }})<br>
                            <strong>Rating: </strong> {{ $serviceConnectionMeter->TransformerRating }}<br>
                        </address>
                    </div>

                    <div class="col-sm-4 invoice-col">
                        Geo Location Data
                        <address>
                            <strong>Building: </strong> <span id="building-latlng">{{ $serviceConnectionInspections->GeoBuilding }}</span><br>
                            <strong>Tapping Pole: </strong> <span id="tapping-latlng">{{ $serviceConnectionInspections->GeoTappingPole }}</span><br>
                            <strong>Metering Pole: </strong> <span id="metering-latlng">{{ $serviceConnectionInspections->GeoMeteringPole }}</span><br>
                            <strong>SE Pole: </strong> <span id="se-latlng">{{ $serviceConnectionInspections->GeoSEPole }}</span><br>
                        </address>
                    </div>
                </div>

                <hr>

                <p><i>Map</i></p>

                <div id='map' class="map"></div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">   

    function getLocData() {
        var centerLoc = "";

        if (document.getElementById("building-latlng").innerText === "") {
            if (document.getElementById("metering-latlng").innerText === "") {
                centerLoc = document.getElementById("tapping-latlng").innerText;
            } else {
                centerLoc = document.getElementById("metering-latlng").innerText;
            }            
        } else {
            centerLoc = document.getElementById("building-latlng").innerText;
        }

        return centerLoc;
    }

    // MAPBOX
    mapboxgl.accessToken = 'pk.eyJ1IjoianVsemxvcGV6IiwiYSI6ImNqZzJ5cWdsMjJid3Ayd2xsaHcwdGhheW8ifQ.BcTcaOXmXNLxdO3wfXaf5A';

    var centerLoc = getLocData();

    var map = new mapboxgl.Map({
        container: 'map',
        zoom: 15,
        center: [centerLoc.split(",")[1], centerLoc.split(",")[0]],
        style: 'mapbox://styles/mapbox/streets-v11'
    });

    const marker = new mapboxgl.Marker()
        .setLngLat([centerLoc.split(",")[1], centerLoc.split(",")[0]])
        .addTo(map);

    map.once('idle',function(){
        window.print();

        window.setTimeout(function(){
            window.location.href = "{{ route('serviceConnections.energization') }}";
        }, 1000);
    });    
</script>
