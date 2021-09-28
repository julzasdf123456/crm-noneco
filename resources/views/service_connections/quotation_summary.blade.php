@php
    use App\Models\ServiceConnections;
    use Illuminate\Support\Facades\DB;
@endphp
@extends('layouts.app')

@section('content')
<div class="content">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4 class="m-0">Summary</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Summary</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs" id="custom-content-below-tab" role="tablist">
        <li class="nav-item">
            <a class="nav-link" id="custom-content-below-home-tab" data-toggle="pill" href="#materials" role="tab" aria-controls="custom-content-below-home" aria-selected="true">Material Summary</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" id="custom-content-below-profile-tab" data-toggle="pill" href="#construction" role="tab" aria-controls="custom-content-below-profile" aria-selected="false">Construction Summary</a>
        </li>
    </ul>
    <div class="tab-content" id="custom-content-below-tabContent">
        {{-- Bills of Materials --}}
        <div class="tab-pane fade show" id="materials" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
            <div class="row">
                <div class="col-md-12 col-lg-12">
                    <div class="row">
                        <div class="col-lg-3 col-md-4">
                            
                        </div>
                        <div class="col-lg-9 col-md-8">
                            <div class="header">
                                <p class="text-center p-0" style="margin: 0;"><strong>{{ env("APP_COMPANY") }}</strong></p>
                                <p class="text-center p-0">{{ env("APP_ADDRESS") }}</p>

                                <h4 class="text-center p-0">Bill of Materials</h4>
                            </div>
                        </div>
                    </div>
                    
                </div>

                <div class="col-lg-3 col-md-4">
                    <div class="card card-outline card-primary">
                        {!! Form::model($billOfMaterialsSummary, ['route' => ['billsOfMaterialsSummaries.update', $billOfMaterialsSummary->id], 'method' => 'patch']) !!}

                        {{-- HIDDEN INPUTS --}}
                        <input type="hidden" name="ServiceConnectionId" value="{{ $serviceConnection->id }}">
                        <div class="card-header">
                            <span class="card-title">Settings</span>

                            <div class="card-tools">
                                <a href="{{ route('billOfMaterialsMatrices.download-bill-of-materials', [$serviceConnection->id]) }}" class="btn btn-tool text-success" title="Download Excel File"><i class="fas fa-download"></i></a>
                                <button class="btn btn-tool" title="Print"><i class="fas fa-print"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="form-group">
                                                {!! Form::checkbox('ExcludeTransformerLaborCost', 'Yes', $billOfMaterialsSummary->ExcludeTransformerLaborCost=='Yes' ? true : false) !!}
                                                {!! Form::label('ExcludeTransformerLaborCost', 'Exclude Transformer Labor Cost') !!}
                                            </div>
                                        </td>                                
                                    </tr>
                                    {{-- <tr>
                                        <td>
                                            <div class="form-group">
                                                <label for="TransformerChangedPrice">Transformer Price</label>
                                                <input type="text" class="form-control" id="TransformerChangedPrice" name="TransformerChangedPrice">
                                            </div>
                                        </td>                                
                                    </tr> --}}
                                    @if ($serviceConnection->AccountApplicationType == 'Temporary')
                                        <tr>
                                            <td>
                                                <div class="form-group">
                                                    <label for="MonthDuration">Duration (in months)</label>
                                                    <input type="number" class="form-control" id="MonthDuration" name="MonthDuration" value="{{ $serviceConnection->TemporaryDurationInMonths }}">
                                                </div>
                                            </td>                                
                                        </tr>
                                    @endif
                                    <tr>
                                        <td>
                                            <div class="form-group">
                                                <label for="TransformerLaborCostPercentage">Transformer Labor Cost (%)</label>
                                                <input type="number" step=".001" class="form-control" id="TransformerLaborCostPercentage" name="TransformerLaborCostPercentage" value="{{ $billOfMaterialsSummary->TransformerLaborCostPercentage }}">
                                            </div>
                                        </td>                                
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="form-group">
                                                <label for="MaterialLaborCostPercentage">Materials Labor Cost (%)</label>
                                                <input type="number" step=".001" class="form-control" id="MaterialLaborCostPercentage" name="MaterialLaborCostPercentage" value="{{ $billOfMaterialsSummary->MaterialLaborCostPercentage }}">
                                            </div>
                                        </td>                                
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="form-group">
                                                <label for="HandlingCostPercentage">Handling and etc. Cost (%)</label>
                                                <input type="number" step=".001" class="form-control" id="HandlingCostPercentage" name="HandlingCostPercentage" value="{{ $billOfMaterialsSummary->HandlingCostPercentage }}">
                                            </div>
                                        </td>                                
                                    </tr>
                                </tbody>                        
                            </table>
                            
                        </div>
                        <div class="card-footer">
                            {!! Form::submit('Apply', ['class' => 'btn btn-sm btn-primary']) !!}
                        </div>
                        {!! Form::close() !!}
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <span class="card-title">Structures in this BoM</span>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-sm">
                                <thead>
                                    <th>Structure</th>
                                    <th>Quantity</th>
                                </thead>
                                <tbody>
                                    @if ($structures != null)
                                        @foreach ($structures as $item)
                                            <tr>
                                                <td><a href="{{ route('structures.show', [$item->id]) }}">{{ $item->StructureId }}</a></td>
                                                <td>{{ $item->Quantity }}</td>
                                            </tr>
                                        @endforeach
                                    @endif                                    
                                </tbody>
                                
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-9 col-md-8">
                    <div class="row invoice-info">
                        <div class="col-sm-4 invoice-col">
                            <address>
                                Date : <strong>{{ date('F d, Y') }}</strong><br>
                                Project Name: <strong>{{ $serviceConnection->ServiceAccountName }}</strong><br>
                                Project Address: <strong>{{ ServiceConnections::getAddress($serviceConnection) }}</strong><br>
                            </address>
                        </div>
                    </div>

                    <div class="table-body">
                        <table class="table table-sm table-hover">
                            <thead>
                                <th>NEA Code</th>
                                <th>Description</th>
                                <th class="text-right">Unit Cost (Php)</th>
                                <th class="text-right">Project Requirements</th>
                                <th class="text-right">Extended Cost</th>
                            </thead>
                            <tbody>
                                @foreach ($materials as $item)
                                    <tr>
                                        <td class="px-4">{{ $item->id }}</td>
                                        <td>{{ $item->Description }}</td>
                                        <td class="text-right">{{ number_format($item->Amount, 2) }}</td>
                                        <td class="text-right">{{ $item->ProjectRequirements }}</td>
                                        <td class="text-right">{{ number_format($item->Cost, 2) }}</td>
                                    </tr>
                                @endforeach
                                @if ($transformers != null)
                                    <tr>
                                        <th>Transformer</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                    @foreach ($transformers as $item)
                                        <tr>
                                            <td class="px-4">{{ $item->id }}</td>
                                            <td>{{ $item->Description }}</td>
                                            <td class="text-right">{{ number_format($item->Amount, 2) }}</td>
                                            <td class="text-right">{{ $item->Quantity }}</td>
                                            <td class="text-right">{{ number_format(floatval($item->Amount) * floatval($item->Quantity), 2) }}</td>
                                        </tr>
                                    @endforeach
                                @endif 
                                <tr>
                                    <td style="border-top: 1px solid #333333;"></td>
                                    <td style="border-top: 1px solid #333333;" class="text-right">Sub-Total</td>  
                                    <td style="border-top: 1px solid #333333;"></td>
                                    <td style="border-top: 1px solid #333333;"></td>
                                    <td style="border-top: 1px solid #333333;" class="text-right">{{ number_format($billOfMaterialsSummary->SubTotal, 2) }}</td>  
                                </tr>  
                                <tr>
                                    <td></td>
                                    <td class="text-right">Transformer Labor Cost</td>  
                                    <td></td>
                                    <td></td>
                                    <td class="text-right">{{ number_format($billOfMaterialsSummary->TransformerLaborCost, 2) }}</td>  
                                </tr>  
                                <tr>
                                    <td></td>
                                    <td class="text-right">Other Materials Labor Cost</td>  
                                    <td></td>
                                    <td></td>
                                    <td class="text-right">{{ number_format($billOfMaterialsSummary->MaterialLaborCost, 2) }}</td>  
                                </tr>  
                                <tr>
                                    <td></td>
                                    <td class="text-right">Total Labor Cost</td>  
                                    <td></td>
                                    <td></td>
                                    <td class="text-right">{{ number_format($billOfMaterialsSummary->LaborCost, 2) }}</td>  
                                </tr>  
                                <tr>
                                    <td></td>
                                    <td class="text-right">Contengency, Engineering & Handling, Etc.</td>  
                                    <td></td>
                                    <td></td>
                                    <td class="text-right">{{ number_format($billOfMaterialsSummary->HandlingCost, 2) }}</td> 
                                </tr>  
                                <tr>
                                    <th></th>
                                    <th class="text-right">Overall Total</th>  
                                    <th></th>
                                    <th></th>
                                    <th class="text-right">{{ number_format($billOfMaterialsSummary->Total, 2) }}</th> 
                                </tr>                       
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- Construction Assets --}}
        <div class="tab-pane fade active show" id="construction" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
            <table class="table table-sm">
                <thead>
                    <th>Item</th>
                    <th>Description</th>
                    <th class="text-right">Quantity</th>
                    <th class="text-right">Labor Cost</th>
                </thead>
                <tbody>
                    @if ($conAss != null)
                        @foreach ($conAss as $item)
                            <tr>
                                <td>{{ $item->ConAssGrouping }}</td>
                                <td>{{ $item->StructureId }}</td>
                                <td class="text-right">{{ $item->Quantity }}</td>
                                <td class="text-right">
                                    @php
                                        $laborCost = DB::table('CRM_BillOfMaterialsMatrix')
                                            ->leftJoin('CRM_Structures', 'CRM_BillOfMaterialsMatrix.StructureId', '=', 'CRM_Structures.id')
                                            ->select(DB::raw('SUM(CAST(CRM_BillOfMaterialsMatrix.Quantity as Integer) * CAST(CRM_BillOfMaterialsMatrix.Amount as Decimal)) AS Cost'))
                                            ->where('CRM_BillOfMaterialsMatrix.ServiceConnectionId', $serviceConnection->id)
                                            ->where('CRM_Structures.Data', $item->StructureId)
                                            ->first();
                                    @endphp
                                    {{ number_format($laborCost->Cost, 2) }}
                                </td>
                            </tr>
                        @endforeach                        
                    @endif
                </tbody>
            </table>
        </div>
    </div>    
</div>
@endsection