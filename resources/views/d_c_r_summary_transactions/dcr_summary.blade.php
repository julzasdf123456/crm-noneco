<table class="table table-hover table-sm table-borderless">
    <thead>
        <th>GL Code</th>
        <th>Description</th>
        <th class="text-right">Amount</th>
    </thead>
    <tbody>
        @php
            $total = 0.0;
        @endphp
        @foreach ($data as $item)
            @if (floatval($item->Amount) == 0)
                
            @else
                <tr>
                    <td>{{ $item->GLCode }}</td>
                    <td>{{ $item->Description }}</td>
                    <td class="text-right">{{ number_format($item->Amount, 2) }}</td>
                </tr>
                @php
                    $total += floatval($item->Amount);
                @endphp
            @endif
            
        @endforeach
        <tr>
            <th>Total</th>
            <td></td>
            <th class="text-right">{{ number_format($total, 2) }}</th>
        </tr>
    </tbody>
</table>