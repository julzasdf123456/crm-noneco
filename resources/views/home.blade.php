@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-8 offset-lg-2 col-md-12">
        <br>
        <div class="card shadow-none">
            <div class="card-header">
                <span class="card-title text-primary"><strong>July 7, 2022 Update!</strong> <i class="text-muted">2:30 PM</i> <br> <i>What's new?</i></span>
            </div>
            <div class="card-body">
                <h4><span class="text-muted"># </span> Billing Analyst Modules</h4>
                <ul>
                    <li>Billing analyst can now print Stuck Ups, Change Meters, No Displays, and Other Unbilled Readings, which can be accessed in 
                        <span class="text-primary"><strong>Meter Reading -> Reading Monitor -> Reading Monitoring Console -> View Report</strong></span></li>
                    <li>Billing analyst can now view and print all <strong>Billed</strong> and <strong>Unbilled Consumers</strong>. A new menu is created under
                        <span class="text-primary"><strong><a href="{{ route('readings.billed-and-unbilled-reports') }}">Reports -> Meter Reading -> Billed & Unbilled</a></strong></span></li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection