<div class="table-responsive">
    <table class="table" id="demandLetters-table">
        <thead>
        <tr>
            <th>Accountnumber</th>
        <th>Userid</th>
        <th>Status</th>
        <th>Datesent</th>
        <th>Notes</th>
            <th colspan="3">Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach($demandLetters as $demandLetters)
            <tr>
                <td>{{ $demandLetters->AccountNumber }}</td>
            <td>{{ $demandLetters->UserId }}</td>
            <td>{{ $demandLetters->Status }}</td>
            <td>{{ $demandLetters->DateSent }}</td>
            <td>{{ $demandLetters->Notes }}</td>
                <td width="120">
                    {!! Form::open(['route' => ['demandLetters.destroy', $demandLetters->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('demandLetters.show', [$demandLetters->id]) }}"
                           class='btn btn-default btn-xs'>
                            <i class="far fa-eye"></i>
                        </a>
                        <a href="{{ route('demandLetters.edit', [$demandLetters->id]) }}"
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
