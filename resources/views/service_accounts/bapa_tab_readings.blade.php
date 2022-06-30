<table class="table table-hover table-bordered table-sm p-0">
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
                    <a href="{{ route('readings.view-full-report-bapa', [$item->ServicePeriod, urlencode($bapaName)]) }}" title="View Full Report" style="margin-right: 10px;"><i class="fas fa-file text-success"></i></a>
                    <button class="btn btn-link btn-sm text-warning" onclick="verifyBillNo('{{ $item->ServicePeriod }}', '{{ urlencode($bapaName) }}')" style="margin-right: 5px; margin-bottom: 5px;" ><i class="fas fa-print text-warning"></i></button>
                    {{-- <a href="{{ route('bills.print-bulk-bill-old-format-bapa', [$item->ServicePeriod, urlencode($bapaName), 'All']) }}" style="margin-right: 10px;"><i class="fas fa-print text-warning"></i></a> --}}
                    <a href="{{ route('bills.bapa-view-readings', [$item->ServicePeriod, urlencode($bapaName)]) }}"><i class="fas fa-eye"></i></a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- Print Ledger History --}}
<div class="modal fade" id="modal-print-bill" aria-hidden="true" style="display: none;" period="">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Print Bill</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" id="period">
                    <input type="hidden" id="bapaname">
                    <div class="form-group col-lg-12">
                        <label for="FromBillNo">From Bill #</label>
                        <input type="text" id="FromBillNo" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="print-bill-bapa" class="btn btn-primary"><i class="fas fa-print ico-tab-mini"></i>Print</button>
            </div>
        </div>
    </div>
</div>

@push('page_scripts')
    <script>
        var periodx = ''
        var bapaname =''
        function verifyBillNo(period, bapaName) {
            periodx = period
            bapaname = bapaName
            $('#modal-print-bill').modal('show')
        }

        $(document).ready(function() {
            $('#modal-print-bill').on('shown.bs.modal', function () {
                $('#period').val(periodx)
                $('#bapaname').val(bapaname)
            })

            $('#print-bill-bapa').on('click', function() {
                if(jQuery.isEmptyObject($('#FromBillNo').val())) {
                    window.location.href  = "{{ url('/bills/print-bulk-bill-old-format-bapa') }}/" + periodx + "/" + bapaname + "/All"
                } else {
                    window.location.href  = "{{ url('/bills/print-bulk-bill-old-format-bapa') }}/" + periodx + "/" + bapaname + "/" + $('#FromBillNo').val()
                }            
            })
        })
    </script>
@endpush