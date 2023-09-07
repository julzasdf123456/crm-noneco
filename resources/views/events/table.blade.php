<div class="table-responsive">
    <table class="table table-sm table-hover" id="events-table">
        <thead>
        <tr>
            <th>Event Name</th>
        <th>Event Start</th>
        <th>Event End</th>
        <th>Registration Start</th>
        <th>Registration End</th>
            <th colspan="3">Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach($events as $events)
            <tr>
                <td>{{ $events->EventTitle }}</td>
                <td>{{ $events->EventStart != null ? date('M d, Y h:i A', strtotime($events->EventStart)) : '' }}</td>
                <td>{{ $events->EventEnd != null ? date('M d, Y h:i A', strtotime($events->EventEnd)) : '' }}</td>
                <td>{{ $events->RegistrationStart != null ? date('M d, Y h:i A', strtotime($events->RegistrationStart)) : '' }}</td>
                <td>{{ $events->RegistrationEnd != null ? date('M d, Y h:i A', strtotime($events->RegistrationEnd)) : '' }}</td>
                <td width="120">
                    {!! Form::open(['route' => ['events.destroy', $events->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('events.show', [$events->id]) }}"
                           class='btn btn-default btn-xs'>
                            <i class="far fa-eye"></i>
                        </a>
                        <a href="{{ route('events.edit', [$events->id]) }}"
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
