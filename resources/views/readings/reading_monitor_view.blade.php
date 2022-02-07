@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Reading Monitoring Console</h4>
                </div>
            </div>
        </div>
    </section>

    <div class="content">
        <div class="row">
            <div class="col-lg-3 col-md-4">
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">Config</span>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="MeterReader">Select Meter Reader</label>
                            <select name="MeterReader" id="MeterReader" class="form-control">
                                @if (count($meterReaders) > 0)
                                    @foreach ($meterReaders as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                @else
                                    <option value="">No Meter Reader Found</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-primary" id="view-btn"><i class="fas fa-eye ico-tab-mini"></i>View</button>
                    </div>
                </div>

                <div class="card" style="height: 60vh;">
                    <div class="card-header border-0">
                        <span class="card-title">Read Accounts</span>
                    </div>
                    <div class="card-body table-responsive px-0">
                        <table class="table table-sm table-hover" id="res-table">
                            <thead>
                                <th>Account No.</th>
                                <th>Reading</th>
                                <th>Timestamp</th>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-9 col-md-8">
                <div id="map" style="width: 100%; height: 90vh;"></div>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.5.1/mapbox-gl.js"></script>
    <link href="https://api.mapbox.com/mapbox-gl-js/v2.5.1/mapbox-gl.css" rel="stylesheet">
    <script>
        mapboxgl.accessToken = 'pk.eyJ1IjoianVsemxvcGV6IiwiYSI6ImNqZzJ5cWdsMjJid3Ayd2xsaHcwdGhheW8ifQ.BcTcaOXmXNLxdO3wfXaf5A';
            const map = new mapboxgl.Map({
            container: 'map', // container ID
            style: 'mapbox://styles/mapbox/satellite-v9',
            center: [123.242197, 10.844679], // starting position [lng, lat], 
            zoom: 10 // starting zoom
        });

        function loadMapAndAccounts() {
            $.ajax({
                url : '/readings/get-readings-from-meter-reader',
                type : 'GET',
                data : {
                    ServicePeriod : "{{ $servicePeriod }}",
                    MeterReader : $("#MeterReader").val(),
                },
                success : function(result) {
                    $('#res-table tbody tr').remove();
                    if (jQuery.isEmptyObject(result)) {
                        console.log("No data found")
                    } else {
                        $.each(result, function(index, element) {
                            // ADD TO TABLE
                            $('#res-table tbody').append(addRowToTable(result[index]['AccountNumber'], result[index]['KwhUsed'], result[index]['ReadingTimestamp']))

                            // ADD TO MAP
                            if (jQuery.isEmptyObject(result[index]['Longitude']) | jQuery.isEmptyObject(result[index]['Latitude'])) {

                            } else {
                                if (index == 0) {
                                    map.flyTo({
                                        center: [parseFloat(result[index]['Longitude']), parseFloat(result[index]['Latitude'])],
                                        zoom: 18,
                                        bearing: 0,
                                        speed: 1, // make the flying slow
                                        curve: 1, // change the speed at which it zooms out
                                        easing: (t) => t,
                                        essential: true
                                    });
                                }

                                new mapboxgl.Marker({ color: 'red'})
                                    .setLngLat([parseFloat(result[index]['Longitude']), parseFloat(result[index]['Latitude'])])
                                    .addTo(map);
                            }
                            
                        })
                    }
                },
                error : function(error) {
                    alert("An error occurred while fetching data")
                    console.log(error)
                }
            })
        }

        function addRowToTable(acctNo, kwhUsed, timestamp) {
            return "<tr>" + 
                    "<td>" + acctNo + "</td>" +
                    "<td>" + kwhUsed + "</td>" +
                    "<td>" + moment(timestamp).format('MMMM DD, Y | h:mm:ss a') + "</td>" +
                "</tr>"
        }

        map.on('load', () => {
            loadMapAndAccounts()
            // $.ajax({
            //     url : '/meter_reader_tracks/get-tracks-by-tracknameid',
            //     type : 'GET',
            //     data : {
            //         TrackNameId : ""
            //     }, 
            //     success : function(res) {
            //         if (jQuery.isEmptyObject(res)) {
            //             alert('No tracks recorded in this track set')
            //         } else {
            //             var coordinates = [];

            //             $.each(res, function(index, element) {
            //                 coordinates.push([res[index]['Longitude'], res[index]['Latitude']])
            //             })

            //             map.addSource('route', {
            //                 'type': 'geojson',
            //                 'lineMetrics': true,
            //                 'data': {
            //                     'type': 'Feature',
            //                     'properties': {},
            //                     'geometry': {
            //                         'type': 'LineString',
            //                         'coordinates': coordinates
            //                     }
            //                 }
            //             });

            //             map.addLayer({
            //                 'id': 'route',
            //                 'type': 'line',
            //                 'source': 'route',
            //                 'layout': {
            //                     'line-join': 'round',
            //                     'line-cap': 'round'
            //                 },
            //                 'paint': {
            //                     'line-color': 'red',
            //                     'line-width': 8,
            //                     'line-gradient': [
            //                         'interpolate',
            //                         ['linear'],
            //                         ['line-progress'],
            //                         0,
            //                         'blue',
            //                         0.1,
            //                         'royalblue',
            //                         0.3,
            //                         'cyan',
            //                         0.5,
            //                         'lime',
            //                         0.7,
            //                         'yellow',
            //                         1,
            //                         'red'
            //                     ]
            //                 }
            //             });

            //             map.flyTo({
            //                 center: coordinates[0],
            //                 zoom: 15,
            //                 bearing: 0,
            //                 speed: 1, // make the flying slow
            //                 curve: 1, // change the speed at which it zooms out
            //                 easing: (t) => t,
            //                 essential: true
            //             });

            //             new mapboxgl.Marker({ color: 'blue', rotation: 45 })
            //                 .setLngLat(coordinates[0])
            //                 .addTo(map);
                        
            //             new mapboxgl.Marker({ color: 'red', rotation: -45 })
            //                 .setLngLat(coordinates[coordinates.length-1])
            //                 .addTo(map);
            //         }
            //     },
            //     error : function(err) {
            //         alert('Error fetching tracks! Contact support for more.')
            //         console.log(err)
            //     }
            // })
        })

        $('#view-btn').on('click', function() {
            loadMapAndAccounts()
        })
    </script>
@endpush

