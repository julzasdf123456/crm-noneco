<div class="card shadow-none" style="height: 70vh">
    <div class="card-body table-responsive p-0">
        <table class="table table-hover table-head-fixed text-nowrap table-sm">
            <thead>
                <th>OR Number</th>
                <th>Account Number</th>
                <th>Account Name</th>
                <th>Payment Source</th>
                <th class="text-right">Amount</th>
                <th>Check No</th>
                <th>Bank</th>
            </thead>
            <tbody>
                @php
                    $i=0;
                    $total = 0;
                @endphp
                @if ($checkPayments != null)
                    @foreach ($checkPayments as $item)
                        <tr>
                            <td>{{ $item->ORNumber }}</td>
                            <td>{{ $item->AccountNumber }}</td>
                            <td>{{ $item->PayeeName }}</td>
                            <td>{{ $item->Source }}</td>
                            <td class="text-right text-info">{{ $item->Amount != null ? number_format($item->Amount, 2) : '0.0' }}</td>
                            <td>{{ $item->CheckNo }}</td>
                            <td>{{ $item->Bank }}</td>
                        </tr>    
                        @php
                            $i++;
                            $total += floatval($item->Amount);
                        @endphp
                    @endforeach     
                @endif        
            </tbody>
            <tfoot style="position: sticky; inset-block-end: 0; background-color: white;">
                <th colspan="2">Total ({{ $i }} payments)</th> 
                <th></th>
                <th></th>
                <th class="text-right text-primary">{{ number_format($total, 2) }}</th>
                <th></th>
                <th></th>
            </tfoot>
        </table>
    </div>
</div>

