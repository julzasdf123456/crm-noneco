@extends('layouts.app')

@section('content')
    <div class="card m-3">
        <div class="card-header">
            <div class="card-title" style="width: 50%;">
                <div class="row mb-2">
                    <div class="col-md-6">
                        <input autofocus type="text" class="form-control" placeholder="Search account" id="params" value="{{ old('params') }}">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-info"><i class="fas fa-search"></i></button>                   
                    </div>
                </div>
            </div>

            <div class="card-tools">
                <a href="{{ route('tickets.create-new', [0]) }}" class="btn btn-tool text-danger"><i class="fas fa-forward"></i> Walk-in tickets</a>
            </div>
        </div>
        <div class="card-body">
            <table id="search-table" class="table table-hover">
                <thead>
                    <th>Account Number</th>
                    <th>Service Account Name</th>
                    <th>Address</th>
                    <th></th>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@push('page_scripts')
    <script>
        function delay(callback, ms) {
            var timer = 0;
            return function() {
                var context = this, args = arguments;
                clearTimeout(timer);
                timer = setTimeout(function () {
                callback.apply(context, args);
                }, ms || 0);
            };
        }

        $(document).ready(function() {
            $('#params').keyup(delay(function(e) {
                $.ajax({
                    url : '/tickets/get-create-ajax',
                    type : 'GET',
                    data : {
                        params : this.value,
                    },
                    success : function(response) {
                        $('#search-table tbody tr').remove();

                        $('#search-table tbody').append(response);
                    },
                    error : function(error) {
                        alert(error)
                        console.log(error)
                    }
                });
            }, 200));
        });
    </script>
@endpush