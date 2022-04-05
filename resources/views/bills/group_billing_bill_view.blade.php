@php
    use App\Models\ServiceAccounts;
    use App\Models\MemberConsumers;
@endphp

@extends('layouts.app')

@section('content')

<div class="row">
    <div class="col-lg-12">
        <br>
        <div class="card">
            <div class="card-body">
                <span>
                    <a href="" class="float-right"><i class="fas fa-print text-warning"></i></a>
                    <h4 style="display: inline; margin-right: 15px;">{{ $memberConsumer != null ? (MemberConsumers::serializeMemberName($memberConsumer)) : '' }}</h4>
                    <p class="text-muted">Billing Month: {{ date('F, Y', strtotime($servicePeriod)) }}</p>
                </span>

                <div class="divider"></div>
                
                <table class="table table-hover">
                    <thead>
                        <th>Account ID:</th>
                        <th>Account No:</th>
                        <th>Consumer Name</th>
                        <th class="text-right">Kwh Used</th>
                        <th class="text-right">2% Discount</th>
                        <th class="text-right">5% Discount</th>
                        <th class="text-right">Net Amount</th>
                    </thead>
                    <tbody>
                        @php
                            $total = 0;
                            $has2Percent = false;
                            $has5Percent = false;
                        @endphp
                        @foreach ($ledgers as $item)
                            <tr>
                                <td>{{ $item->AccountNumber }}</td>
                                <td>{{ $item->OldAccountNo }}</td>
                                <td>{{ $item->ServiceAccountName }}</td>
                                <td class="text-right">{{ $item->KwhUsed }}</td>
                                <td class="text-right">{{ number_format($item->Evat2Percent, 2) }}</td>
                                <td class="text-right">{{ number_format($item->Evat5Percent, 2) }}</td>
                                <td class="text-right">{{ number_format($item->NetAmount, 2) }}</td>
                            </tr>
                            @php
                                $total += floatval($item->NetAmount);

                                if ($item->Evat2Percent != null) {
                                    $has2Percent = true;
                                }

                                if ($item->Evat5Percent != null) {
                                    $has5Percent = true;
                                }
                            @endphp
                        @endforeach
                    </tbody>
                </table>

                <div class="divider"></div>
                <br>

                <div class="row">
                    {{-- EVAT CONFIG --}}
                    <div class="col-lg-8 col-md-7">
                        <address class="text-muted"><i>Settings</i></address>
                        
                        <table class="table table-borderless">
                            <tr>
                                <td>
                                    <div class="row">
                                        <div class="col-lg-1 col-md-3">
                                            {{ Form::checkbox('Evat2Percent', 'Evat2Percent', $has2Percent ? true : false, ['class' => 'custom-checkbox', 'id' => 'Evat2Percent']) }}
                                        </div>
                                        <div class="col-lg-10 col-md-7">
                                            {!! Form::label('Evat2Percent', '2% Discount') !!}
                                        </div>     
                                    </div>                                   
                                </td>
                                <td>
                                    <div class="row">
                                        <div class="col-lg-1 col-md-3">
                                            {{ Form::checkbox('Evat5Percent', 'Evat5Percent', $has5Percent ? true : false, ['class' => 'custom-checkbox', 'id' => 'Evat5Percent']) }}
                                        </div>
                                        <div class="col-lg-10 col-md-7">
                                            {!! Form::label('Evat5Percent', '5% Discount') !!}
                                        </div>  
                                    </div>                                  
                                </td>
                            </tr>
                        </table>
                    </div>

                    {{-- TOTAL --}}
                    <div class="col-lg-4 col-md-5">
                        <span>
                            <address class="text-muted text-right">Total: </address>
                            <h2 class="text-right">P {{ number_format($total, 2) }}</h2>
                        </span>
                    </div>                    
                </div>
            </div>            
        </div>
    </div>
</div>

@endsection

@push('page_scripts')
    <script>
        $(document).ready(function() {
            $('#Evat2Percent').change(function() {
                check2Percent()
            })

            $('#Evat5Percent').change(function() {
                check5Percent()
            })
        })

        function check2Percent() {
            if ($('#Evat2Percent').is(':checked')) {
                // ADD 2% DISCOUNT
                $.ajax({
                    url : '/bills/add-two-percent',
                    type : 'GET',
                    data : {
                        MemberConsumerId : "{{ $memberConsumer->ConsumerId }}",
                        Period : "{{ $servicePeriod }}"
                    },
                    success : function(res) {
                        location.reload()
                    },
                    error : function(err) {
                        alert('An error occurred while adding 2% discount')
                    }
                })
            } else {
                // REMOVE 2% DISCOUNT
                $.ajax({
                    url : '/bills/remove-two-percent',
                    type : 'GET',
                    data : {
                        MemberConsumerId : "{{ $memberConsumer->ConsumerId }}",
                        Period : "{{ $servicePeriod }}"
                    },
                    success : function(res) {
                        location.reload()
                    },
                    error : function(err) {
                        alert('An error occurred while adding 2% discount')
                    }
                })
            }
        }

        function check5Percent() {
            if ($('#Evat5Percent').is(':checked')) {
                // ADD 5% DISCOUNT
                $.ajax({
                    url : '/bills/add-five-percent',
                    type : 'GET',
                    data : {
                        MemberConsumerId : "{{ $memberConsumer->ConsumerId }}",
                        Period : "{{ $servicePeriod }}"
                    },
                    success : function(res) {
                        location.reload()
                    },
                    error : function(err) {
                        alert('An error occurred while adding 5% discount')
                    }
                })
            } else {
                // REMOVE 5% DISCOUNT
                $.ajax({
                    url : '/bills/remove-five-percent',
                    type : 'GET',
                    data : {
                        MemberConsumerId : "{{ $memberConsumer->ConsumerId }}",
                        Period : "{{ $servicePeriod }}"
                    },
                    success : function(res) {
                        location.reload()
                    },
                    error : function(err) {
                        alert('An error occurred while adding 5% discount')
                    }
                })
            }
        }
    </script>
@endpush