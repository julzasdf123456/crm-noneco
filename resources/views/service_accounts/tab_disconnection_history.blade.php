@php
    use App\Models\User;
@endphp

<table class="table table-hover table-sm table-bordered">
    <thead>
        <th>Status</th>
        <th>Billing Month</th>
        <th>Disco/Reco Date</th>
        <th>Disco/Reco Time</th>
        <th>Disconnection Personnel</th>
        <th class="text-right">Last Reading</th>
        <th>Remarks/Notes</th>
    </thead>
    <tbody>
        @if ($disconnectionHistory != null)
            @foreach ($disconnectionHistory as $item)
                @php
                    $user = User::find($item->UserId);
                @endphp
                <tr>
                    <td><i class="fas ico-tab {{ $item->Status=='RECONNECTED' ? 'fa-check-circle text-success' : 'fa-exclamation-circle text-danger' }}"></i>{{ $item->Status }}</td>
                    <td>{{ $item->ServicePeriod==null ? '-' : date('F Y', strtotime($item->ServicePeriod)) }}</td>
                    <td>{{ date('F d, Y', strtotime($item->DateDisconnected)) }}</td>
                    <td>{{ date('h:i:s A', strtotime($item->TimeDisconnected)) }}</td>
                    <td>{{ $user != null ? $user->name : 'n/a' }}</td>
                    <td class="text-right">{{ $item->BillId }}</td>
                    <td>{{ $item->Notes }}</td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>