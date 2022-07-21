@extends('layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h4>Sales Distribution Report - {{ date('F Y', strtotime($period)) }}</h4>
            </div>
        </div>
    </div>
</section>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active" href="#overall" data-toggle="tab">
                        <i class="fas fa-circle"></i>
                        Overall Sales Draft</a></li>
                    <li class="nav-item"><a class="nav-link" href="#merged" data-toggle="tab">
                        <i class="fas fa-circle"></i>
                        Merged Sales Draft</a></li>
                    <li class="nav-item"><a class="nav-link" href="#consolidated-all" data-toggle="tab">
                        <i class="fas fa-circle"></i>
                        Consolidated ERC</a></li>
                </ul>
            </div>
            <div class="card-body p-0">
                <div class="tab-content">
                    <div class="tab-pane active" id="overall">
                        @include('kwh_sales.attach_over_all_sales_draft')
                    </div>
                    <div class="tab-pane" id="merged">
                        @include('kwh_sales.attach_merged_sales_draft')
                    </div>
                    <div class="tab-pane" id="consolidated-all">
                        @include('kwh_sales.attach_consolidate_erc')
                    </div>
                </div>
            </div>
        </div>
    </div>    
</div>
@endsection