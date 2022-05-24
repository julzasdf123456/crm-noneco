<table class="table table-responsive-lg table-hover table-sm">
    <thead>
        <th>OR Number</th>
        <th>OR Date</th>
        <th>Account Number</th>
        <th>Account Name</th>
        <th>Amount Paid</th>
    </thead>
    <tbody>
        @if ($powerBills != null)
            @foreach ($powerBills as $item)
                <tr>
                    <td>{{ $item->ORNumber }}</td>
                    <td>{{ $item->ORDate }}</td>
                    <td>{{ $item->AccountNumber }}</td>
                    <td>{{ $item->ServiceAccountName }}</td>
                    <td>{{ $item->NetAmount }}</td>
                </tr>      
            @endforeach            
        @endif        
    </tbody>
</table>