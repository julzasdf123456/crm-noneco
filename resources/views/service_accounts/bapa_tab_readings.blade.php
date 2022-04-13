<table class="table table-hover table-borderless">
    <thead>
        <th>Billing Month</th>
        <th class="text-right">Total No. Readings</th>
        <th></th>
    </thead>
    <tbody>
        @foreach ($readings as $item)
            <tr>
                <td><a href="{{ route('bills.bapa-view-readings', [$item->ServicePeriod, urlencode($bapaName)]) }}">{{ date('F Y', strtotime($item->ServicePeriod)) }}</a></td>
                <td class="text-right">{{ number_format($item->NoOfReadings) }}</td>
                <td class="text-right">
                    <a href="{{ route('bills.bapa-view-readings', [$item->ServicePeriod, urlencode($bapaName)]) }}"><i class="fas fa-eye"></i></a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>