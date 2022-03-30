<div class="row">
    <div class="col-lg-12">
        <p class="text-center text-muted">Balance</p>
        <p class="text-center text-success" style="font-size: 2.5em;">₱ {{ $prepaymentBalance != null ? (number_format($prepaymentBalance->Balance, 2)) : "0.0" }}</p>
    </div>

    <div class="col-lg-12">
        <div class="divider"></div>

        <p>Transaction History</p>
        <table class="table table-hover table-sm table-borderless">
            <thead>
                <th>Transaction ID</th>
                <th>Method</th>
                <th>Amount</th>
                <th>Personnel</th>
                <th>Transaction Date</th>
            </thead>
            <tbody>
                @foreach ($prepaymentHistory as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td class="{{ $item->Method == 'DEPOSIT' ? 'text-success' : 'text-danger' }}">{{ $item->Method }}</td>
                        <td>{{ number_format($item->Amount, 2) }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ date('M d, Y h:i:s A', strtotime($item->created_at)) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>