@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Demand Letters</h4>
                </div>
                <div class="col-sm-6">
                    <div class="margin">
                        <div class="btn-group float-right">
                            <button type="button" class="btn btn-primary">Create Demand Letter</button>
                            <button type="button" class="btn btn-primary dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <div class="dropdown-menu" role="menu">
                            <a class="dropdown-item" href="">Per Route</a>
                            <a class="dropdown-item" href="{{ route('demandLetters.per-account', ['0', '0']) }}">Per Account</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('flash::message')

        <div class="clearfix"></div>

        <div class="card">
            <div class="card-body p-0">
                @include('demand_letters.table')

                <div class="card-footer clearfix">
                    <div class="float-right">
                        
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection

