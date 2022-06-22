<div class="card shadow-none" style="height: 70vh">
    <div class="card-body table-responsive px-0">
        <table class="table table-hover table-sm">
            <thead>
                <th>OR Number</th>
                <th>OR Date</th>
                <th>Account Number</th>
                <th>Account Name</th>
                <th class="text-right">Amount Paid</th>
            </thead>
            <tbody>
                @if ($powerBills != null)
                    @php
                        $total = 0;
                        $i=0;
                    @endphp
                    @foreach ($powerBills as $item)
                        <tr>
                            <td>{{ $item->ORNumber }}</td>
                            <td>{{ $item->ORDate }}</td>
                            <td>{{ $item->AccountNumber }}</td>
                            <td>{{ $item->ServiceAccountName }}</td>
                            <td class="text-right">{{ $item->NetAmount }}</td>
                        </tr>   
                        @php
                            $total = $total + floatval($item->NetAmount);
                            $i++;
                        @endphp   
                    @endforeach   
                    <tr>
                        <th>Total ({{ $i }} payments)</th> 
                        <th></th>   
                        <th></th>
                        <th></th>
                        <th class="text-right">{{ number_format($total, 2) }}</th>
                    </tr>         
                @endif        
            </tbody>
        </table>
    </div>
</div>