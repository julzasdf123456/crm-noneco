<table class="table table-hover">
    <thead>
        <th>Account Number</th>
        <th>Service Account Name</th>
        <th>Address</th>
        <th></th>
    </thead>
    <tbody>
        @foreach ($serviceAccounts as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->ServiceAccountName }}</td>                
                <td>{{ $item->Barangay }}, {{ $item->Town }}</td>
                <td width="120">
                    {!! Form::open(['route' => ['serviceAccounts.destroy', $item->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('serviceAccounts.show', [$item->id]) }}"
                           class='btn btn-default btn-xs'>
                            <i class="far fa-eye"></i>
                        </a>
                        <a href="{{ route('serviceAccounts.edit', [$item->id]) }}"
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

{{ $serviceAccounts->links() }}