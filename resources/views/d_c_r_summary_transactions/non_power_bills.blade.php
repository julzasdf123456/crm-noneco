<div class="card shadow-none" style="height: 70vh">
    <div class="card-body table-responsive px-0">
        <table class="table table-hover table-sm">
            <thead>
                <th>OR Number</th>
                <th>Account Number</th>
                <th>Account Name</th>
                <th>GL Code</th>
                <th class="text-right">Amount</th>
                <th>Check No.</th>
                <th>Bank</th>
            </thead>
            <tbody>
                @if ($nonPowerBills != null)
                    @php
                        $prevHolder = null;
                        $prev = null;
                        $total = 0;
                        $i=0;
                    @endphp
                    @foreach ($nonPowerBills as $item)
                        @php
                            $prevHolder = $item->ORNumber;
                        @endphp
                        <tr>
                            <td>{{ $prevHolder==$prev ? '' : $item->ORNumber }}</td>
                            <td>{{ $prevHolder==$prev ? '' : $item->AccountNumber }}</td>
                            <td>{{ $prevHolder==$prev ? '' : $item->PayeeName }}</td>
                            <td>{{ $item->AccountCode }}</td>
                            <td class="text-right">{{ $item->Total != null ? number_format($item->Total, 2) : '0.0' }}</td>
                            <td>{{ $item->CheckNo }}</td>
                            <td>{{ $item->Bank }}</td>
                        </tr>   
                        @php
                            $prev = $prevHolder;    
                            $total = $total + floatval($item->Total);
                            $i++;                
                        @endphp   
                    @endforeach    
                    <tr>
                        <th>Total ({{ $i }} payments)</th> 
                        <th></th>   
                        <th></th>
                        <th></th>
                        <th class="text-right">{{ number_format($total, 2) }}</th>
                        <th></th>
                        <th></th>
                    </tr>          
                @endif        
            </tbody>
        </table>
    </div>
</div>

