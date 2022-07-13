@extends('layouts.app')

@section('content')
<p style="padding-top: 8px;"><i class="fas fa-chart-line ico-tab"></i>Collection Summary - <strong class="text-primary">{{ $office }}</strong></p>

<div class="row">
    <div class="col-lg-12">
        <div class="card shadow-none p-0">
            <div class="card-body px-4 py-1">
                <form action="{{ route('dCRSummaryTransactions.collection-office-expand', [urlencode($office)]) }}" method="GET">
                    <div class="row">
                        <div class="form-group col-lg-2">
                            <label for="From">From</label>
                            <input type="text" class="form-control form-control-sm" id="From" name="From" placeholder="From" value="{{ isset($_GET['From']) ? $_GET['From'] : '' }}">
                            @push('page_scripts')
                                <script type="text/javascript">
                                    $('#From').datetimepicker({
                                        format: 'YYYY-MM-DD',
                                        useCurrent: true,
                                        sideBySide: true
                                    })
                                </script>
                            @endpush
                        </div>

                        <div class="form-group col-lg-2">
                            <label for="To">To</label>
                            <input type="text" class="form-control form-control-sm" id="To" name="To" placeholder="To" value="{{ isset($_GET['To']) ? $_GET['To'] : '' }}">
                            @push('page_scripts')
                                <script type="text/javascript">
                                    $('#To').datetimepicker({
                                        format: 'YYYY-MM-DD',
                                        useCurrent: true,
                                        sideBySide: true
                                    })
                                </script>
                            @endpush
                        </div>

                        <div class="form-group col-lg-2">
                            <label for="Teller">Teller</label>
                            <select name="Teller" id="Teller" class="form-control form-control-sm">
                                <option value="All">All</option>
                                @foreach ($tellers as $item)
                                    <option value="{{ $item->id }}" {{ isset($_GET['Teller']) && $_GET['Teller']==$item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-lg-2">
                            <label for="Action">Action</label>
                            <br>
                            <button class="btn btn-sm btn-warning" type="submit" id="filter-btn"><i class="fas fa-filter"></i>Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- RESULTS --}}
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active" href="#dcr-summary" data-toggle="tab">
                        <i class="fas fa-list"></i>
                        DCR Summary</a></li>

                    <li class="nav-item"><a class="nav-link" href="#power-bills" data-toggle="tab">
                        <i class="fas fa-user"></i>
                        Power Bills Payments</a></li>

                    <li class="nav-item"><a class="nav-link" href="#non-power-bills" data-toggle="tab">
                        <i class="fas fa-circle"></i>
                        Non-Power Bills Payments</a></li>

                    <li class="nav-item"><a class="nav-link" href="#check-payments" data-toggle="tab">
                        <i class="fas fa-circle"></i>
                        Check Payments</a></li>

                    <li class="nav-item"><a class="nav-link" href="#cancelled-ors" data-toggle="tab">
                        <i class="fas fa-circle"></i>
                        Cancelled ORs</a></li>
                </ul>
            </div>
            <div class="card-body p-0">
                <div class="tab-content">
                    <div class="tab-pane active" id="dcr-summary">
                        @include('d_c_r_summary_transactions.dcr_summary')
                    </div>

                    <div class="tab-pane" id="power-bills">
                        @include('d_c_r_summary_transactions.power_bills')
                    </div>

                    <div class="tab-pane" id="non-power-bills">
                        @include('d_c_r_summary_transactions.non_power_bills')
                    </div>

                    <div class="tab-pane" id="check-payments">
                        @include('d_c_r_summary_transactions.tab_check_admin_dcr')
                    </div>

                    <div class="tab-pane" id="cancelled-ors">
                        @include('d_c_r_summary_transactions.tab_cancelled_ors_admin_dcr')
                    </div>
                </div>                    
            </div>
        </div>
    </div>
</div>
@endsection