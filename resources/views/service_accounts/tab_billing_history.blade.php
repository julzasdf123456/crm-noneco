<div class="content">
    @if ($bills == null)
        <p class="center-text"><i>No billing history recorded</i></p>
    @else
        <table class="table table-sm table-hover">
            <thead>
                <th>Bill Number</th>
                <th>Billing Month</th>
                <th class="text-right">Kwh Used</th>
                <th class="text-right">Rate</th>
                <th class="text-right">Net Amount</th>
                <th class="text-right">OR Number</th>
                <th class="text-right">Payment Date</th>
                <th></th>
            </thead>
            <tbody>
                @foreach ($bills as $item)
                    <tr>
                        <td><i class="fas {{ $item->ORDate != null ? 'fa-check-circle text-success' : 'fa-exclamation-circle text-danger' }} ico-tab"></i><a href="{{ route('bills.show', [$item->id]) }}">{{ $item->BillNumber }}</a></td>
                        <td>{{ date('F Y', strtotime($item->ServicePeriod)) }}</td>
                        <td class="text-right">{{ $item->KwhUsed != null ? number_format($item->KwhUsed, 2) : '0' }}</td>
                        <td class="text-right">{{ $item->EffectiveRate != null ? number_format($item->EffectiveRate, 4) : '0' }}</td>
                        <td class="text-right">{{ $item->NetAmount != null ? number_format($item->NetAmount, 2) : '0' }}</td>
                        <td class="text-right"><a href="{{ $item->PaidBillId != null ? (route('transactionIndices.browse-ors-view', [$item->PaidBillId, 'BILLS PAYMENT'])) : '' }}">{{ $item->ORNumber != null ? $item->ORNumber : '-' }}</a></td>
                        <td class="text-right">{{ $item->ORDate != null ? date('F d, Y', strtotime($item->ORDate)) : '-' }}</td>
                        <td class="text-right">
                            @if ($item->ORDate == null)
                                <a href="{{ route('bills.adjust-bill', [$item->id]) }}" class="btn btn-link btn-sm text-warning" title="Adjust Reading"><i class="fas fa-pen"></i></a>
                                <button class="btn btn-link text-danger" title="Cancel this Bill" onclick="requestCancel('{{ $item->id }}')"><i class="fas fa-ban"></i></button>
                            @endif
                            <a href="{{ route('bills.print-single-bill-new-format', [$item->id]) }}" class="btn btn-link" title="Print New Formatted Bill"><i class="fas fa-print"></i></a>
                            <a href="{{ route('bills.print-single-bill-old', [$item->id]) }}" class="btn btn-link text-warning" title="Print Pre-Formatted Bill (Old)"><i class="fas fa-print"></i></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

@push('page_scripts')
    <script>
        function requestCancel(id) {
            (async () => {
                const { value: text } = await Swal.fire({
                    input: 'textarea',
                    inputLabel: 'Remarks',
                    inputPlaceholder: 'Type your remarks here...',
                    inputAttributes: {
                        'aria-label': 'Type your remarks here'
                    },
                    title: 'Cancel this bill?',
                    showCancelButton: true
                })

                if (text) {
                    $.ajax({
                        url : '{{ route("bills.request-cancel-bill") }}',
                        type : 'GET',
                        data : {
                            id : id,
                            Remarks : text
                        },
                        success : function(res) {
                            Swal.fire('Cancel Request Successful', 'Your cancellation request has been forwarded to your Billing Head and is waiting for confirmation', 'success')
                        },
                        error : function(err) {
                            Swal.fire('Cancel Request Error', 'Contact support immediately', 'error')
                        }
                    })
                }
            })()
        }
    </script>
@endpush