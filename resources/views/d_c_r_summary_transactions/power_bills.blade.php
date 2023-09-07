<div class="card shadow-none" style="height: 70vh">
    <div class="card-body table-responsive p-0">
        <table class="table table-hover table-head-fixed text-nowrap table-sm">
            <thead>
                <th>OR Number</th>
                <th>OR Date</th>
                <th>Account Number</th>
                <th>Account Name</th>
                <th>Billing Month</th>
                <th class="text-right">KwhUsed</th>
                <th class="text-right">Surcharges</th>
                <th class="text-right">2% EWT</th>
                <th class="text-right">5% EVAT</th>
                <th class="text-right">OCL</th>
                <th class="text-right">Deductions/<br>Discounts</th>
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
                            <td><a href="{{ route('serviceAccounts.show', [$item->AccountNumber]) }}">{{ $item->OldAccountNo }}</a></td>
                            <td>{{ $item->ServiceAccountName }}</td>
                            <td>{{ $item->ServicePeriod }}</td>
                            <td class="text-right">{{ is_numeric($item->KwhUsed) ? number_format($item->KwhUsed, 2) : $item->KwhUsed }}</td>
                            <td class="text-right text-danger">{{ is_numeric($item->Surcharge) ? number_format($item->Surcharge, 2) : $item->Surcharge }}</td>
                            <td class="text-right text-info">{{ is_numeric($item->Form2307TwoPercent) ? number_format($item->Form2307TwoPercent, 2) : $item->Form2307TwoPercent }}</td>
                            <td class="text-right text-info">{{ is_numeric($item->Form2307FivePercent) ? number_format($item->Form2307FivePercent, 2) : $item->Form2307FivePercent }}</td>
                            <td class="text-right">{{ is_numeric($item->AdditionalCharges) ? number_format($item->AdditionalCharges, 2) : $item->AdditionalCharges }}</td>
                            <td class="text-right text-info">{{ is_numeric($item->Deductions) ? number_format($item->Deductions, 2) : $item->Deductions }}</td>
                            <td class="text-right text-primary">{{ is_numeric($item->CashPaid) ? number_format($item->CashPaid, 2) : $item->CashPaid }}</td>
                        </tr>   
                        @php
                            $total = $total + floatval($item->CashPaid);
                            $i++;
                        @endphp   
                    @endforeach    
                @endif        
            </tbody>
            <tfoot style="position: sticky; inset-block-end: 0; background-color: white;">                  
                <tr>
                    <th colspan="2">Total ({{ $i }} payments)</th> 
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>   
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th class="text-right text-primary" id="total-api-power-dcr">{{ number_format($total, 2) }}</th>
                </tr>      
            </tfoot>
        </table>
    </div>
</div>