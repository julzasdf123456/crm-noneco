@php
    use App\Models\User;
@endphp

<table class="table table-hover">
    <thead>
        <th>Billing Month</th>
        <th>Date Disconnected</th>
        <th>Time Disconnected</th>
        <th>Meter Reader</th>
    </thead>
    <tbody>
        @if ($disconnectionHistory != null)
            @foreach ($disconnectionHistory as $item)
                @php
                    $user = User::find($item->UserId);
                @endphp
                <tr>
                    <td>{{ date('F Y', strtotime($item->ServicePeriod)) }}</td>
                    <td>{{ date('F d, Y', strtotime($item->DateDisconnected)) }}</td>
                    <td>{{ date('h:i:s A', strtotime($item->TimeDisconnected)) }}</td>
                    <td>{{ $user != null ? $user->name : 'n/a' }}</td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>