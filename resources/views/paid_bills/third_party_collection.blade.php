@extends('layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h4>Third-Party Payment Console</h4>
            </div>
            <div class="col-sm-6">
                <a class="btn btn-primary float-right"
                    href="{{ route('paidBills.upload-third-party-collection') }}">
                    <i class="fas fa-file-upload ico-tab"></i>Upload Collection
                </a>
            </div>
        </div>
    </div>
</section>

<div class="row">

</div>

@endsection