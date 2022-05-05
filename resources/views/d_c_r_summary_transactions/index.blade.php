@php    
    use Illuminate\Support\Facades\Auth;
@endphp

@extends('layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h4>DCR Summary Report</h4>
            </div>
        </div>
    </div>
</section>

<div class="content px-3">

    @include('flash::message')

    <div class="clearfix"></div>

    <div class="row">
        {{-- FORM --}}
        <div class="col-lg-3">
            <div class="card">
                {!! Form::open(['route' => 'dCRSummaryTransactions.index', 'method' => 'GET']) !!}
                <div class="card-body">
                    <input type="hidden" value="{{ Auth::id() }}" name="Teller">
                    <!-- Day Field -->
                    <div class="form-group col-sm-12">
                        {!! Form::label('Day', 'Choose Day:') !!}
                        {!! Form::text('Day', $day, ['class' => 'form-control','id'=>'Day']) !!}
                    </div>

                    @push('page_scripts')
                        <script type="text/javascript">
                            $('#Day').datetimepicker({
                                format: 'YYYY-MM-DD',
                                useCurrent: true,
                                sideBySide: true
                            })
                        </script>
                    @endpush
                </div>
                <div class="card-footer">
                    {!! Form::submit('Proceed', ['class' => 'btn btn-primary']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>

        {{-- RESULTS --}}
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header border-0">
                    <span class="card-title">Daily Collection Summary</span>
                </div>
                <div class="card-body table-responsive px-0">
                    <table class="table table-hover table-sm table-borderless">
                        <thead>
                            <th>GL Code</th>
                            <th>Description</th>
                            <th class="text-right">Amount</th>
                        </thead>
                        <tbody>
                            @foreach ($data as $item)
                                @if (intval($item->Amount) == 0)
                                    
                                @else
                                    <tr>
                                        <td>{{ $item->GLCode }}</td>
                                        <td>{{ $item->Description }}</td>
                                        <td class="text-right">{{ number_format($item->Amount, 2) }}</td>
                                    </tr>
                                @endif
                                
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

