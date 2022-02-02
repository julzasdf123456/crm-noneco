<div class="content">
    @if ($bills == null)
        <p class="center-text"><i>No billing history recorded</i></p>
    @else
        <table class="table table-sm table-hover">
            <thead>
                <th>Bill Number</th>
                <th>Billing Month</th>
                <th class="right-text">Kwh Used</th>
                <th class="right-text">Rate</th>
                <th class="right-text">Net Amount</th>
                <th width="10%"></th>
            </thead>
            <tbody>
                @foreach ($bills as $item)
                    <tr>
                        <td>{{ $item->BillNumber }}</td>
                        <td>{{ date('F Y', strtotime($item->ServicePeriod)) }}</td>
                        <td class="right-text">{{ $item->KwhUsed != null ? number_format($item->KwhUsed, 2) : '0' }}</td>
                        <td class="right-text">{{ $item->EffectiveRate != null ? number_format($item->EffectiveRate, 4) : '0' }}</td>
                        <td class="right-text">{{ $item->NetAmount != null ? number_format($item->NetAmount, 2) : '0' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>