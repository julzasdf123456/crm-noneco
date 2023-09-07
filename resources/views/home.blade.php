@extends('layouts.app')

@section('content')

<br>
<div class="row">
    @include('d_c_r_summary_transactions.dashboard_collection_summary')
    
    @include('readings.dashboard_readings')

    @include('kwh_sales.dashboard_annual_sales')
</div>
@endsection
