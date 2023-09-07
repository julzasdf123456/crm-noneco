<div class="table-responsive">
    <table class="table" id="demandLetterMonths-table">
        <thead>
        <tr>
            <th>Demandletterid</th>
        <th>Serviceperiod</th>
        <th>Accountnumber</th>
        <th>Netamount</th>
        <th>Surcharge</th>
        <th>Interest</th>
        <th>Totalamountdue</th>
        <th>Notes</th>
        <th>Status</th>
            <th colspan="3">Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach($demandLetterMonths as $demandLetterMonths)
            <tr>
                <td>{{ $demandLetterMonths->DemandLetterId }}</td>
            <td>{{ $demandLetterMonths->ServicePeriod }}</td>
            <td>{{ $demandLetterMonths->AccountNumber }}</td>
            <td>{{ $demandLetterMonths->NetAmount }}</td>
            <td>{{ $demandLetterMonths->Surcharge }}</td>
            <td>{{ $demandLetterMonths->Interest }}</td>
            <td>{{ $demandLetterMonths->TotalAmountDue }}</td>
            <td>{{ $demandLetterMonths->Notes }}</td>
            <td>{{ $demandLetterMonths->Status }}</td>
                <td width="120">
                    {!! Form::open(['route' => ['demandLetterMonths.destroy', $demandLetterMonths->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('demandLetterMonths.show', [$demandLetterMonths->id]) }}"
                           class='btn btn-default btn-xs'>
                            <i class="far fa-eye"></i>
                        </a>
                        <a href="{{ route('demandLetterMonths.edit', [$demandLetterMonths->id]) }}"
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
