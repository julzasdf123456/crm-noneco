@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Unbilled Readings Console - {{ date('F Y', strtotime($servicePeriod)) }}</h4>
                </div>
            </div>
        </div>
    </section>
@endsection