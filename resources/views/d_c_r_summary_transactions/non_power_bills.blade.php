<table class="table table-responsive-lg table-hover table-sm">
    <thead>
        <th>OR Number</th>
        <th>Account Number</th>
        <th>Account Name</th>
        <th>GL Code</th>
        <th>Amount</th>
        <th>Check No.</th>
        <th>Bank</th>
    </thead>
    <tbody>
        @if ($nonPowerBills != null)
            @php
                $prevHolder = null;
                $prev = null;
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
                    <td>{{ $item->Total != null ? number_format($item->Total, 2) : '0.0' }}</td>
                    <td>{{ $item->CheckNo }}</td>
                    <td>{{ $item->Bank }}</td>
                </tr>   
                @php
                    $prev = $prevHolder;                    
                @endphp   
            @endforeach            
        @endif        
    </tbody>
</table>