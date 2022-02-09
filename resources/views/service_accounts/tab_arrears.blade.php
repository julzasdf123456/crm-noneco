@php
    use App\Models\IDGenerator;
@endphp
<div class="content">
    <div class="row">
        <div class="col-lg-6">
            {{-- COLLECTIBLES --}}
            <div class="card" style="height: 60vh;">
                <div class="card-header border-0">
                    <span class="card-title">Uncollected Arrears</span>
                    <div class="card-tools">
                        <button class="btn btn-tool" title="Update Figure" data-toggle="modal" data-target="#modal-update-collectible"><i class="fas fa-pen"></i></button>
                        @if ($collectibles != null && count($arrearsLedger) < 1)
                            <button class="btn btn-tool text-warning" title="Split into multiple months (termed payment)" data-toggle="modal" data-target="#modal-ledgerize"><i class="fas fa-clipboard-list"></i></button>
                        @endif                        
                    </div>
                </div>
                <div class="card-body table-responsive">
                    @if ($collectibles != null)
                        <h3 class="text-danger">₱ {{ number_format($collectibles->Balance, 2) }}</h3>

                        @if ($arrearsLedger != null && count($arrearsLedger) > 0)
                            <div class="divider"></div>
                            <p><i>Arrears | Termed Ledger ({{ count($arrearsLedger) }} months to pay)</i></p>

                            <table class="table table-sm table-hover">
                                <thead>
                                    <th>Period</th>
                                    <th class="text-right">Amount to Pay</th>
                                    <th></th>
                                </thead>
                                <tbody>
                                    @foreach ($arrearsLedger as $item)
                                        <tr>
                                            <td>{{ date('F Y', strtotime($item->ServicePeriod)) }}</td>
                                            <td class="text-right">₱ {{ number_format($item->Amount, 2) }}</td>
                                            <td>
                                                {{-- INSERT STATUS HERE --}}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            {!! Form::open(['route' => ['collectibles.clear-ledger', $serviceAccounts->id], 'method' => 'post']) !!}
                            {!! Form::button('<i class="fas fa-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-sm btn-link text-danger float-right', 'onclick' => "return confirm('Are you sure you want to clear this ledger?')"]) !!}
                            {!! Form::close() !!}
                        @else
                            <p>Arrears not ledgerized</p>
                        @endif                    
                    @else
                        <h3 class="text-success">₱ 0.00</h3>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card" style="height: 60vh;">
                <div class="card-header border-0">
                    <span class="card-title">Monthly Bill Arrears</span>
                </div>
                <div class="card-body table-responsive px-0">

                </div>
            </div>
        </div>
    </div>
</div>

{{-- UPDATE COLLECTIBLES MODAL --}}
<div class="modal fade" id="modal-update-collectible" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            @if ($collectibles != null)
                {!! Form::model($collectibles, ['route' => ['collectibles.update', $collectibles->id], 'method' => 'patch']) !!}
            @else
                {!! Form::open(['route' => 'collectibles.store']) !!}
            @endif
            <div class="modal-header">
                <h4 class="modal-title">Update Uncollected Arrears</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                @if ($collectibles == null)
                    <input type="hidden" name="id" value="{{ IDGenerator::generateIDandRandString() }}">
                @endif

                <input type="hidden" name="AccountNumber" value="{{ $serviceAccounts->id }}">

                <!-- Balance Field -->
                <div class="form-group col-sm-12">
                    {!! Form::label('Balance', 'Balance') !!}
                    {!! Form::number('Balance', null, ['class' => 'form-control', 'step' => 'any', 'maxlength' => 60,'maxlength' => 60]) !!}
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                {!! Form::submit('Update', ['class' => 'btn btn-primary']) !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

{{-- LEDGERIZE MODAL --}}
<div class="modal fade" id="modal-ledgerize" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Split Payment Into Terms</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="Term">Term (in months)</label>
                    <input type="number" id="Term" placeholder="Enter number of months to pay" class="form-control"/>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="term-btn">Proceed</button>
            </div>
        </div>
    </div>
</div>

@push('page_scripts')
    <script>
        $(document).ready(function() {
            $('#term-btn').on('click', function() {
                $.ajax({
                    url : '/collectibles/ledgerize',
                    type : 'GET',
                    data : {
                        CollectibleId : "{{ $collectibles != null ? $collectibles->id : 0 }}",
                        Term : $('#Term').val(),
                    },
                    success : function(res) {
                        location.reload();
                    }, 
                    error : function(err) {
                        alert('An error occurred while ledgerizing this arrear')
                        console.log(err)
                    }
                })
            })
        })
    </script>
@endpush