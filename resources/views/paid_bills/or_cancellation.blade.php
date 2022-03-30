@php
    use Illuminate\Support\Facades\Auth; 
    use App\Models\IDGenerator;
    use App\Models\ORAssigning;
@endphp

@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Bills Payment OR Cancellation Console</h4>
                </div>
            </div>
        </div>
    </section>

    <div class="row">
        <div class="col-lg-5 col-md-6">
            <div class="card" style="height: 70vh;">
                <div class="card-header border-0">
                    <div class="row">
                        <div class="col-lg-8">
                            <input type="text" class="form-control" id="search-field" placeholder="Search OR Number or Consumer" autofocus>
                        </div>
                        <div class="col-lg-4">
                            <div class="card-tools">
                                <button class="btn btn-primary">Search</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive px-0">
                    <table class="table table-hover table-sm" id="res-table">
                        <thead>
                            <th>OR Number</th>
                            <th>Account No</th>
                            <th>Consumer Name</th>
                            <th>Amount</th>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    <script>
        $(document).ready(function() {
            $('#search-field').keyup(function() {
                performSearchOR(this.value)
            })
        })

        function performSearchOR(value) {
            $('#res-table tbody tr').remove()
            $.ajax({
                url : '/paid_bills/search-or',
                type : 'GET',
                data : {
                    query : value
                },
                success : function(res) {
                    $('#res-table tbody').append(res)
                },
                error : function(err) {
                    $('#res-table tbody tr').remove()
                    alert('An error occurred during the search')
                }
            })
        }
    </script>
@endpush