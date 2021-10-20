<div class="table-responsive">
    <table class="table" id="ticketsRepositories-table">
        <thead>
        <tr>
            <th>Name</th>
        <th>Description</th>
        <th>Parentticket</th>
        <th>Type</th>
        <th>Kpscategory</th>
        <th>Kpsissue</th>
            <th colspan="3">Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach($ticketsRepositories as $ticketsRepository)
            <tr>
                <td>{{ $ticketsRepository->Name }}</td>
            <td>{{ $ticketsRepository->Description }}</td>
            <td>{{ $ticketsRepository->ParentTicket }}</td>
            <td>{{ $ticketsRepository->Type }}</td>
            <td>{{ $ticketsRepository->KPSCategory }}</td>
            <td>{{ $ticketsRepository->KPSIssue }}</td>
                <td width="120">
                    {!! Form::open(['route' => ['ticketsRepositories.destroy', $ticketsRepository->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('ticketsRepositories.show', [$ticketsRepository->id]) }}"
                           class='btn btn-default btn-xs'>
                            <i class="far fa-eye"></i>
                        </a>
                        <a href="{{ route('ticketsRepositories.edit', [$ticketsRepository->id]) }}"
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
