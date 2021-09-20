@php
    use App\Models\ServiceConnections;
@endphp

@extends('layouts.app')

@section('content')
<div class="content">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4 class="m-0">Bill of Materials Assigning</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Bill of Materials Assigning</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row invoice-info">
        <div class="col-sm-4 invoice-col">
            <address>
                <strong>{{ $serviceConnection->ServiceAccountName }}</strong><br>
                {{ ServiceConnections::getAddress($serviceConnection) }}<br>
            </address>
        </div>
        <!-- /.col -->
        <div class="col-sm-4 invoice-col">
            
        </div>
        <div class="col-sm-4 invoice-col">
            <br>
            <b >Account: </b><span id="scId">{{ $serviceConnection->id }}</span>
        </div>
    </div>

    <div class="divider"></div>

    <div class="row">
        <div class="col-md-4 col-lg-4">
            <div class="card">
                <div class="card-header">
                    <span class="card-title">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="hidden" name="_token" id="csrf" value="{{Session::token()}}">
                                <input class="form-control" id="autosuggest" placeholder="Type a structure">
                            </div>
                            <div class="col-md-3">
                                <input class="form-control" type="number" id="qty"  placeholder="Quantity">
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-info" id="add-structure"><i class="fas fa-check-circle"></i></button>
                            </div>
                        </div>
                    </span>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table" id="strucures-table">
                        <thead>
                            <th>Structure</th>
                            <th width="30%">Project Requirement Quantity</th>
                            <th width="10%"></th>
                        </thead>
                        <tbody>
                            @foreach ($structuresAssigned as $item)
                                <tr id="{{ $item->id }}">
                                    <td>{{ $item->StructureId }}</td>
                                    <td>{{ $item->Quantity }}</td>
                                    <td>
                                        <button onclick="deleteStructure('{{ $item->id }}')" class="btn btn-sm text-danger"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{-- <div class="card-footer">
                    <a href="{{ route('billOfMaterialsMatrices.view', [$serviceConnection->id]) }}" class="btn btn-primary">Submit</a>
                </div> --}}
            </div>
        </div>

        <div class="col-md-8 col-lg-8">
            <div class="card card-primary card-outline">
                <div class="card-header border-0">
                    <span class="card-title">Materials Assigned</span>

                    <div class="card-tools">
                        <a href="{{ route('billOfMaterialsMatrices.download-bill-of-materials', [$serviceConnection->id]) }}" class="btn btn-tool text-success" title="Download Excel File"><i class="fas fa-download"></i></a>
                        <button class="btn btn-tool" title="Print"><i class="fas fa-print"></i></button>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table" id="materials-table">
                        <thead>
                            <th>NEA Code</th>
                            <th>Description</th>
                            <th class="text-right">Unit Cost</th>
                            <th class="text-right">Project Requirements</th>
                            <th class="text-right">Extended Cost</th>
                        </thead>
                        <tbody>
                            @php
                                $total = 0.0;
                            @endphp
                            @foreach ($billOfMaterials as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->Description }}</td>
                                    <td class="text-right">{{ number_format($item->Amount, 2) }}</td>
                                    <td class="text-right">{{ $item->ProjectRequirements }}</td>
                                    <td class="text-right">{{ number_format($item->ExtendedCost, 2) }}</td>
                                </tr>
                                @php
                                    $total += doubleval($item->ExtendedCost);
                                @endphp
                            @endforeach
                            <tr>
                                <td><strong>Total</strong></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="text-right"><strong>{{ number_format($total, 2) }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="card-footer">
                    <a href="" class="btn btn-primary">Submit and Forward to Transformer and Pole Assigning <i class="fas fa-arrow-alt-circle-right" style="margin-left: 5px;"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('page_scripts')
    <script type="text/javascript">

    var structures = [];
    $.ajax({
        url : '/structures/get-structures-json',
        type: "GET",
        dataType : "json",
        success : function(response) {
            $.each(response, function(index, element) {
                structures.push({ label : response[index]['Data'], value : response[index]['Data'] });
            });
            
        },
        error : function(error) {
            alert("Error adding structures to row! Contact support immediately.");
        }
    });

    $(document).ready(function() {        
        $('#autosuggest').autocomplete({
            source: structures
        })

        $('#add-structure').on('click', function() {
            if (jQuery.isEmptyObject($('#autosuggest').val()) | jQuery.isEmptyObject($('#qty').val())) {
                alert('Please fill in the fields to continue!');
            } else {
                $.ajax({
                    url : '/structure_assignments/insert-structure-assignment',
                    type: "POST",
                    data : {
                        _token : $('#csrf').val(),
                        ServiceConnectionId : $('#scId').text(),
                        Structure : $('#autosuggest').val(),
                        Quantity : $('#qty').val(),
                    },
                    success : function(response) {
                        var data = JSON.parse(response);
                        $('#strucures-table tbody').append('<tr id="' + data['id'] + '">'+
                                                                '<td>' + data['StructureId'] + '</td>'+
                                                                '<td>' + data['Quantity'] + '</td>'+
                                                                '<td>' +
                                                                    '<button onclick="deleteStructure(' + data["id"] + ')" class="btn btn-sm text-danger"><i class="fas fa-trash"></i></button>' +
                                                                '</td>' +
                                                            '</tr>'); 

                        fetchAllMaterials($('#scId').text());
                                 
                    },
                    error : function(error) {
                        console.log(error);
                    }
                });  
            }
                      
        });
    });  

    function fetchAllMaterials(scId) {
        // SHOW TO MATERIALS TABLE
        $.ajax({
            url : '/bill_of_materials_matrices/get-bill-of-materials-json/',
            type : 'GET',
            data : {
                scId : scId,
            },
            success : function(response) {

                $('#materials-table tbody tr').remove();

                var data = JSON.parse(response);
                var total = 0.0;

                $.each(data, function(index, element) {
                    $('#materials-table tbody').append(
                        '<tr>' + 
                            '<td>' + data[index]['id'] + '</td>' + 
                            '<td>' + data[index]['Description'] + '</td>' +
                            '<td class="text-right">' + Number(parseFloat(data[index]['Amount']).toFixed(2)).toLocaleString() + '</td>' +
                            '<td class="text-right">' + data[index]['ProjectRequirements'] + '</td>' + 
                            '<td class="text-right">' + Number(parseFloat(data[index]['ExtendedCost']).toFixed(2)).toLocaleString() + '</td>' +
                        '</tr>'
                    );
                    total += parseFloat(data[index]['ExtendedCost']);
                });

                $('#materials-table tbody').append(
                    '<tr>' +
                        '<td><strong>Total</strong></td>' +
                        '<td></td>' +
                        '<td></td>' +
                        '<td></td>' +
                        '<td class="text-right"><strong>' + Number(total.toFixed(2)).toLocaleString() + '</strong></td>' +
                    '</tr>'
                );
            },
            error : function(error) {
                console.log(console.error());
            }
        }); 
    }
 
    function deleteStructure(id) {
        if (confirm('Are you sure you want to delete this structure?')) {
            $.ajax({
                url : '/structureAssignments/' + id,
                type: "DELETE",
                data: {
                    _token:'{{ csrf_token() }}',
                    id: id,
                },
                success : function(data) {
                    fetchAllMaterials($('#scId').text());
                },
                error : function(error) {
                    console.log(error);
                    // alert("Error deleting structure " + error);
                }
            });  
            $('#' + id).remove();
        }
    } 
    </script>
@endpush