@extends('layouts.app')

@push('page_css')
    
@endpush

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <h4>Create Change Meter - Search Consumer</h4>
            </div>
        </div>
    </div>
</section> 

<div class="content">
    <div class="row">
        <div class="container-fluid">
            {!! Form::open(['route' => 'tickets.change-meter', 'method' => 'GET']) !!}
                <div class="row mb-2">
                    <div class="col-md-6 offset-md-3">
                        <input type="text" class="form-control" placeholder="Search" name="params" value="{{ old('params') }}">
                    </div>
                    <div class="col-md-3">
                        {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
                    </div>
                </div>
            {!! Form::close() !!}
        </div>
    </div>

    <div class="content px-3">

        @include('flash::message')

        <div class="clearfix"></div>

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
                        <td>{{ $item->ServiceAccountName }} {{ $item->AccountCount != null ? '(# ' . $item->AccountCount . ')' : '' }}</td>                
                        <td>{{ $item->Barangay }}, {{ $item->Town }}</td>
                        <td width="120">
                            {!! Form::open(['route' => ['serviceAccounts.destroy', $item->id], 'method' => 'delete']) !!}
                            <div class='btn-group'>
                                <a href="{{ route('tickets.create-change-meter', [$item->id]) }}"
                                   class='btn btn-primary btn-xs'>
                                    <i class="fas fa-forward"></i>
                                </a>
                                {{-- <a href="{{ route('serviceAccounts.edit', [$item->id]) }}"
                                   class='btn btn-default btn-xs'>
                                    <i class="far fa-edit"></i>
                                </a>
                                {!! Form::button('<i class="far fa-trash-alt"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!} --}}
                            </div>
                            {!! Form::close() !!}
                        </td>
                    </tr>
                    
                @endforeach
            </tbody>
        </table>
        
        {{ $serviceAccounts->links() }}
    </div>
</div> 
@endsection