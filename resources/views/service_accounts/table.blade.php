<div class="table-responsive">
    <table class="table" id="serviceAccounts-table">
        <thead>
        <tr>
            <th>Serviceaccountname</th>
        <th>Town</th>
        <th>Barangay</th>
        <th>Purok</th>
        <th>Accounttype</th>
        <th>Accountstatus</th>
        <th>Contactnumber</th>
        <th>Emailaddress</th>
        <th>Serviceconnectionid</th>
        <th>Meterdetailsid</th>
        <th>Transformerdetailsid</th>
        <th>Polenumber</th>
        <th>Areacode</th>
        <th>Blockcode</th>
        <th>Sequencecode</th>
        <th>Feeder</th>
        <th>Computetype</th>
        <th>Organization</th>
        <th>Organizationparentaccount</th>
        <th>Gpsmeter</th>
            <th colspan="3">Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach($serviceAccounts as $serviceAccounts)
            <tr>
                <td>{{ $serviceAccounts->ServiceAccountName }}</td>
            <td>{{ $serviceAccounts->Town }}</td>
            <td>{{ $serviceAccounts->Barangay }}</td>
            <td>{{ $serviceAccounts->Purok }}</td>
            <td>{{ $serviceAccounts->AccountType }}</td>
            <td>{{ $serviceAccounts->AccountStatus }}</td>
            <td>{{ $serviceAccounts->ContactNumber }}</td>
            <td>{{ $serviceAccounts->EmailAddress }}</td>
            <td>{{ $serviceAccounts->ServiceConnectionId }}</td>
            <td>{{ $serviceAccounts->MeterDetailsId }}</td>
            <td>{{ $serviceAccounts->TransformerDetailsId }}</td>
            <td>{{ $serviceAccounts->PoleNumber }}</td>
            <td>{{ $serviceAccounts->AreaCode }}</td>
            <td>{{ $serviceAccounts->BlockCode }}</td>
            <td>{{ $serviceAccounts->SequenceCode }}</td>
            <td>{{ $serviceAccounts->Feeder }}</td>
            <td>{{ $serviceAccounts->ComputeType }}</td>
            <td>{{ $serviceAccounts->Organization }}</td>
            <td>{{ $serviceAccounts->OrganizationParentAccount }}</td>
            <td>{{ $serviceAccounts->GPSMeter }}</td>
                <td width="120">
                    {!! Form::open(['route' => ['serviceAccounts.destroy', $serviceAccounts->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('serviceAccounts.show', [$serviceAccounts->id]) }}"
                           class='btn btn-default btn-xs'>
                            <i class="far fa-eye"></i>
                        </a>
                        <a href="{{ route('serviceAccounts.edit', [$serviceAccounts->id]) }}"
                           class='btn btn-default btn-xs'>
                            <i class="far fa-edit"></i>
                        </a>
                        {!! Form::button('<i class="far fa-trash-alt"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
