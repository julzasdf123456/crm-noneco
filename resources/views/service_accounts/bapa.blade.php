@extends('layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h4>BAPA Organizer and Control</h4>
            </div>
            <div class="col-sm-6">
                <a class="btn btn-primary float-right"
                   href="{{ route('serviceAccounts.create-bapa') }}">
                    Create New BAPA
                </a>
            </div>
        </div>
    </div>
</section>

<div class="row">
    <div class="col-lg-12">
        <table class="table table-hover">
            <thead>
                <th>BAPA Name</th>
                <th>No. of Members</th>
                <th></th>
            </thead>
            <tbody>
                @foreach ($bapa as $item)
                    @if ($item->OrganizationParentAccount != null)
                    <tr>
                        <td>{{ $item->OrganizationParentAccount }}</td>
                        <td>{{ $item->MembersTotal }}</td>
                        <td class="text-right">
                            <a href="" class="btn btn-xs btn-primary"><i class="fas fa-eye ico-tab-mini"></i> View</a>
                        </td>
                    </tr>
                    @endif                    
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection