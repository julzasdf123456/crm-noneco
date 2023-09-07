@if($totalTransactions == null)
    <p class="text-center"><i>No payment transactions recorded!</i></p>
    @if ($serviceConnectionInspections != null)
        @if ($serviceConnectionInspections->Status != "Approved")
            <p class="text-danger"><i class="fas fa-info-circle ico-tab"></i> <i>NOTE that you can't create payment invoice if the inspection isn't approved or successful.</i></p>
        @else
            @if (Auth::user()->hasAnyRole(['Administrator', 'Heads and Managers', 'Service Connection Assessor'])) 
                <a href="{{ route('serviceConnectionPayTransactions.create-step-four', [$serviceConnections->id]) }}" class="btn btn-primary btn-sm" title="Add Payment Transaction">
                    <i class="fas fa-plus ico-tab"></i>
                    Create Payment Invoice</a>
            @endif
        @endif
    @endif
@else
    @if ($totalTransactions->Notes != null) 
        <p class="badge bg-success" style="padding: 10px;"><i class="fas fa-check-circle ico-tab-mini"></i>Paid</p>
    @endif
    <div class="row">
        {{-- <div class="col-md-12">
            <div class="callout callout-info">
                <p>Material Transactions</p>
            </div>
          
            <table id="materials_table" class="table">
                <thead>
                    <th>Materials</th>
                    <th>Rate</th>
                    <th>Qty</th>
                    <th class="text-right">Sub Ttl</th>
                    <th class="text-right">VAT</th>
                    <th class="text-right">Total</th>
                </thead>
                <tbody>
                    @if ($materialPayments != null)
                        @foreach ($materialPayments as $item)
                            <tr id="{{ $item->id }}">
                                <td>{{ $item->Material }}</td>
                                <td>{{ $item->Rate }}</td>
                                <td>{{ $item->Quantity }}</td>
                                <td class="text-right">{{ number_format($item->Rate * $item->Quantity, 2) }}</td>
                                <td class="text-right">{{ $item->Vat }}</td>  
                                <td class="text-right">{{ number_format($item->Total, 2) }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        <div class="divider"></div> --}}

        <div class="col-md-12">
            {{-- <div class="callout callout-info">
                <p>Particular Payments</p>
            </div> --}}

            <table id="particulars_table" class="table table-hover table-sm table-bordered">
                <thead>
                    <th>Particulars</th>
                    <th class="text-right">Amnt</th>
                    <th class="text-right">OR Number</th>
                    <th class="text-right">Total</th>
                </thead>
                <tbody>
                    @if ($particularPayments != null)
                        @foreach ($particularPayments as $item)
                            <tr id="{{ $item->id }}">
                                <td>{{ $item->Particular }}</td>  
                                <td class="text-right">{{ number_format($item->Amount, 2) }}</td>
                                <td class="text-right">{{ $item->Vat }}</td>  
                                <th class="text-right">{{ number_format($item->Total, 2) }}</th>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    
    @if ($totalTransactions != null)
        <div class="col-md-12">
            <p>Sub Total: <strong>{{ number_format($totalTransactions->SubTotal, 2) }}</strong></p>
            <p>Total VAT: <strong>{{ number_format($totalTransactions->TotalVat, 2) }}</strong></p>
            <h4>Overall Total: <strong>{{ number_format($totalTransactions->Total, 2) }}</strong></h4>

            <br>
            @if (Auth::user()->hasAnyRole(['Administrator', 'Heads and Managers', 'Service Connection Assessor'])) 
                <a href="{{ route('serviceConnectionPayTransactions.create-step-four', [$serviceConnections->id]) }}" class="btn btn-sm btn-warning">
                    <i class="fas fa-pen ico-tab"></i>
                    Update Payment</a>
                <a href="" class="btn btn-sm btn-success">
                   <i class="fas fa-print ico-tab"></i>
                    Print Invoice</a>
            @endif
        </div>
    @else
        <p class="text-center"><i>No total transactions recorded!</i></p>
        @if (Auth::user()->hasAnyRole(['Administrator', 'Heads and Managers', 'Service Connection Assessor'])) 
            <a href="{{ route('serviceConnectionPayTransactions.create-step-four', [$serviceConnections->id]) }}" class="btn btn-sm btn-warning">
                <i class="fas fa-pen ico-tab"></i>
                Update Payment</a>
        @endif
    @endif
@endif