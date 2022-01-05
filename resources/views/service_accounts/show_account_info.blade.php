@php
    use App\Models\ServiceAccounts;
@endphp
<div class="card card-outline {{ $serviceAccounts->AccountStatus=='ACTIVE' ? 'card-success' : 'card-danger' }}" title="{{ $serviceAccounts->AccountStatus=='ACTIVE' ? 'Account Active' : 'Account Disconnected' }}"">
    <div class="card-header border-0">
        <span class="card-title">
            <strong>{{ $serviceAccounts->ServiceAccountName }}  {{ $serviceAccounts->AccountCount != null ? '(# ' . $serviceAccounts->AccountCount . ')' : '' }}</strong>
        </span>
    </div>
    <div class="card-body table-responsive px-0">
        <table class="table table-hover table-sm">
            <thead></thead>
            <tbody>
                <tr>
                    <td class="text-muted">Account Number</td>
                    <td><strong>{{ $serviceAccounts->id }}</strong> (Old Acct: {{ $serviceAccounts->OldAccountNo }})</td>
                </tr>
                <tr>
                    <td class="text-muted">Account Address</td>
                    <th>{{ ServiceAccounts::getAddress($serviceAccounts) }}</th>
                </tr>
                <tr>
                    <td class="text-muted">Consumer Type</td>
                    <th>{{ $serviceAccounts->AccountType }}</th>
                </tr>
                <tr>
                    <td class="text-muted">Connection Date</td>
                    <th>{{ $serviceAccounts->ConnectionDate==null ? '-' : date('F d, Y', strtotime($serviceAccounts->ConnectionDate)) }}</th>
                </tr>
                <tr>
                    <td class="text-muted">Area Code</td>
                    <th>{{ $serviceAccounts->AreaCode }}</th>
                </tr>
                <tr>
                    <td class="text-muted">Sequence No.</td>
                    <th>{{ $serviceAccounts->SequenceCode }}</th>
                </tr>
                <tr>
                    <td class="text-muted">Group Code</td>
                    <th>{{ $serviceAccounts->GroupCode }}</th>
                </tr>
                <tr>
                    <td class="text-muted">For Distribution</td>
                    <th class="{{ $serviceAccounts->ForDistribution=='Yes' ? 'text-success' : 'text-muted' }}">{{ $serviceAccounts->ForDistribution=='Yes' ? 'Yes' : 'No' }}</th>
                </tr>
                <tr>
                    <td class="text-muted">Main</td>
                    <th class="{{ $serviceAccounts->Main=='Yes' ? 'text-success' : 'text-muted' }}">{{ $serviceAccounts->Main=='Yes' ? 'Yes' : 'No' }}</th>
                </tr>
                <tr>
                    <td class="text-muted">BAPA</td>
                    <th class="{{ $serviceAccounts->Organization=='BAPA' ? 'text-success' : 'text-muted' }}">{{ $serviceAccounts->Organization=='BAPA' ? 'Yes' : 'No' }}</th>
                </tr>
                <tr>
                    <td class="text-muted">Senior Citizen</td>
                    <th class="{{ $serviceAccounts->SeniorCitizen=='Yes' ? 'text-success' : 'text-muted' }}">{{ $serviceAccounts->SeniorCitizen=='Yes' ? 'Yes' : 'No' }}</th>
                </tr>
                <tr>
                    <td class="text-muted">EVAT 5%</td>
                    <th class="{{ $serviceAccounts->Evat5Percent=='Yes' ? 'text-success' : 'text-muted' }}">{{ $serviceAccounts->Evat5Percent=='Yes' ? 'Yes' : 'No' }}</th>
                </tr>
                <tr>
                    <td class="text-muted">EWT 2%</td>
                    <th class="{{ $serviceAccounts->Ewt2Percent=='Yes' ? 'text-success' : 'text-muted' }}">{{ $serviceAccounts->Ewt2Percent=='Yes' ? 'Yes' : 'No' }}</th>
                </tr>
                <tr>
                    <th>Coreloss</th>
                    <th>{{ $serviceAccounts->Coreloss }}</th>
                </tr>
            </tbody>
            
        </table>

        <div class="content px-3">
            <div class="divider"></div>
            {{-- <span class="text-muted"><i>Tools</i></span><br> --}}
            <a href="{{ route('serviceAccounts.update-step-one', [$serviceAccounts->id]) }}" class="btn btn-tool text-success" title="Update Consumer Info"><i class="fas fa-pen"></i></a>
            <a href="" class="btn btn-tool text-danger" title="Disconnect this account"><i class="fas fa-unlink"></i></a>
        </div>
    </div>
</div>