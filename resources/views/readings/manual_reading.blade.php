@php
    use App\Models\ServiceAccounts;
@endphp

@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            {!! Form::open(['route' => 'readings.manual-reading', 'method' => 'GET']) !!}
                <div class="row mb-2">
                    <div class="col-md-6 offset-md-3">
                        <input type="text" class="form-control" placeholder="Search" name="params" value="{{ isset($_GET['params']) ? $_GET['params'] : '' }}" autofocus>
                    </div>
                    <div class="col-md-3">
                        {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
                    </div>
                </div>
            {!! Form::close() !!}
        </div>
    </section>

    <div class="content px-3">

        @include('flash::message')

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-lg-12">
                <table class="table table-hover table-sm">
                    <thead>
                        <th>Account No.</th>
                        <th>Service Account Name</th>
                        <th>Address</th>
                        <th>Account Type</th>
                        <th>Contestable</th>
                        <th></th>
                    </thead>
                    <tbody>
                        @foreach ($serviceAccounts as $item)
                            <tr>
                                <td><a href="{{ route('serviceAccounts.show', [$item->id]) }}">{{ $item->OldAccountNo != null ? $item->OldAccountNo : '-' }}</a></td>
                                <td>{{ $item->ServiceAccountName }} {{ $item->AccountCount != null ? '(# ' . $item->AccountCount . ')' : '' }}</td>                
                                <td>{{ ServiceAccounts::getAddress($item) }}</td>
                                <td>{{ $item->AccountType }}</td>
                                <td>{{ $item->Contestable }}</td>
                                <td width="120">
                                    {!! Form::open(['route' => ['serviceAccounts.destroy', $item->id], 'method' => 'delete']) !!}
                                    <div class='btn-group'>
                                        <a href="{{ route('readings.manual-reading-console', [$item->id]) }}"
                                           class='btn btn-primary btn-xs'>
                                            <i class="far fa-eye"></i> Perform Reading
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
            </div>
        </div>
        
        {{ $serviceAccounts->links() }}
    </div>

@endsection