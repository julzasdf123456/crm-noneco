@php    
    use Illuminate\Support\Facades\DB;
@endphp
<a href="{{ route('kwhSales.consolidated-per-town', [$period]) }}" class="btn btn-warning btn-sm"><i class="fas fa-share ico-tab"></i>Go to Consolidated Per Town</a>
<table class="table table-sm table-borderless">
    <thead>
        <th>Rate</th>
        <th class="text-right">Residential</th>
        <th class="text-right">Low Voltage</th>
        <th class="text-right">High Voltage</th>
        <th class="text-right">Total Amount</th>
    </thead>
    <tbody>
        <tr>
            <th>GENERATION AND TRANSMISSION CHARGES:</th>
        </tr>
        <tr>
            @php
                $generationSystem = DB::table('Billing_Rates')
                    ->select(
                        DB::raw("(SELECT SUM(CAST(GenerationSystemCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('RESIDENTIAL', 'RURAL RESIDENTIAL', 'STREET LIGHTS')) AS Residential"),
                        DB::raw("(SELECT SUM(CAST(GenerationSystemCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL', 'INDUSTRIAL', 'PUBLIC BUILDING', 'IRRIGATION/WATER SYSTEMS')) AS LowVoltage"),
                        DB::raw("(SELECT SUM(CAST(GenerationSystemCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE', 'PUBLIC BUILDING HIGH VOLTAGE')) AS HighVoltage"),
                        DB::raw("(SELECT SUM(CAST(GenerationSystemCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "') AS TotalAmount")                  
                    )
                    ->limit(1)
                    ->first();
            @endphp
            <td style="padding-left: 40px;">Generation System</td>
            <td class="text-right">{{ number_format($generationSystem->Residential, 2) }}</td>
            <td class="text-right">{{ number_format($generationSystem->LowVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($generationSystem->HighVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($generationSystem->TotalAmount, 2) }}</td>
        </tr>
        <tr>
            @php
                $transmissionSystemKw = DB::table('Billing_Rates')
                    ->select(
                        DB::raw("(SELECT SUM(CAST(TransmissionDeliveryChargeKW AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('RESIDENTIAL', 'RURAL RESIDENTIAL', 'STREET LIGHTS')) AS Residential"),
                        DB::raw("(SELECT SUM(CAST(TransmissionDeliveryChargeKW AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL', 'INDUSTRIAL', 'PUBLIC BUILDING', 'IRRIGATION/WATER SYSTEMS')) AS LowVoltage"),
                        DB::raw("(SELECT SUM(CAST(TransmissionDeliveryChargeKW AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE', 'PUBLIC BUILDING HIGH VOLTAGE')) AS HighVoltage"),
                        DB::raw("(SELECT SUM(CAST(TransmissionDeliveryChargeKW AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "') AS TotalAmount")                  
                    )
                    ->limit(1)
                    ->first();
            @endphp
            <td style="padding-left: 40px;">Transmission Delivery Charge (kW)</td>
            <td class="text-right">{{ number_format($transmissionSystemKw->Residential, 2) }}</td>
            <td class="text-right">{{ number_format($transmissionSystemKw->LowVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($transmissionSystemKw->HighVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($transmissionSystemKw->TotalAmount, 2) }}</td>
        </tr>
        <tr>
            @php
                $transmissionSystemKwh = DB::table('Billing_Rates')
                    ->select(
                        DB::raw("(SELECT SUM(CAST(TransmissionDeliveryChargeKWH AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('RESIDENTIAL', 'RURAL RESIDENTIAL', 'STREET LIGHTS')) AS Residential"),
                        DB::raw("(SELECT SUM(CAST(TransmissionDeliveryChargeKWH AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL', 'INDUSTRIAL', 'PUBLIC BUILDING', 'IRRIGATION/WATER SYSTEMS')) AS LowVoltage"),
                        DB::raw("(SELECT SUM(CAST(TransmissionDeliveryChargeKWH AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE', 'PUBLIC BUILDING HIGH VOLTAGE')) AS HighVoltage"),
                        DB::raw("(SELECT SUM(CAST(TransmissionDeliveryChargeKWH AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "') AS TotalAmount")                  
                    )
                    ->limit(1)
                    ->first();
            @endphp
            <td style="padding-left: 40px;">Transmission Delivery Charge (kWH)</td>
            <td class="text-right">{{ number_format($transmissionSystemKwh->Residential, 2) }}</td>
            <td class="text-right">{{ number_format($transmissionSystemKwh->LowVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($transmissionSystemKwh->HighVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($transmissionSystemKwh->TotalAmount, 2) }}</td>
        </tr>
        <tr>
            @php
                $systemLossCharge = DB::table('Billing_Rates')
                    ->select(
                        DB::raw("(SELECT SUM(CAST(SystemLossCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('RESIDENTIAL', 'RURAL RESIDENTIAL', 'STREET LIGHTS')) AS Residential"),
                        DB::raw("(SELECT SUM(CAST(SystemLossCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL', 'INDUSTRIAL', 'PUBLIC BUILDING', 'IRRIGATION/WATER SYSTEMS')) AS LowVoltage"),
                        DB::raw("(SELECT SUM(CAST(SystemLossCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE', 'PUBLIC BUILDING HIGH VOLTAGE')) AS HighVoltage"),
                        DB::raw("(SELECT SUM(CAST(SystemLossCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "') AS TotalAmount")                  
                    )
                    ->limit(1)
                    ->first();
            @endphp
            <td style="padding-left: 40px;">System Loss Charge</td>
            <td class="text-right">{{ number_format($systemLossCharge->Residential, 2) }}</td>
            <td class="text-right">{{ number_format($systemLossCharge->LowVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($systemLossCharge->HighVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($systemLossCharge->TotalAmount, 2) }}</td>
        </tr>
        <tr>
            @php
                $ogaKwh = DB::table('Billing_Rates')
                    ->select(
                        DB::raw("(SELECT SUM(CAST(OtherGenerationRateAdjustment AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('RESIDENTIAL', 'RURAL RESIDENTIAL', 'STREET LIGHTS')) AS Residential"),
                        DB::raw("(SELECT SUM(CAST(OtherGenerationRateAdjustment AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL', 'INDUSTRIAL', 'PUBLIC BUILDING', 'IRRIGATION/WATER SYSTEMS')) AS LowVoltage"),
                        DB::raw("(SELECT SUM(CAST(OtherGenerationRateAdjustment AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE', 'PUBLIC BUILDING HIGH VOLTAGE')) AS HighVoltage"),
                        DB::raw("(SELECT SUM(CAST(OtherGenerationRateAdjustment AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "') AS TotalAmount")                  
                    )
                    ->limit(1)
                    ->first();
            @endphp
            <th style="padding-left: 40px;">Other Generation Rate Adjustment (OGA) (KWH)</th>
            <td class="text-right">{{ number_format($ogaKwh->Residential, 2) }}</td>
            <td class="text-right">{{ number_format($ogaKwh->LowVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($ogaKwh->HighVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($ogaKwh->TotalAmount, 2) }}</td>
        </tr>
        <tr>
            @php
                $otcaKw = DB::table('Billing_Rates')
                    ->select(
                        DB::raw("(SELECT SUM(CAST(OtherTransmissionCostAdjustmentKW AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('RESIDENTIAL', 'RURAL RESIDENTIAL', 'STREET LIGHTS')) AS Residential"),
                        DB::raw("(SELECT SUM(CAST(OtherTransmissionCostAdjustmentKW AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL', 'INDUSTRIAL', 'PUBLIC BUILDING', 'IRRIGATION/WATER SYSTEMS')) AS LowVoltage"),
                        DB::raw("(SELECT SUM(CAST(OtherTransmissionCostAdjustmentKW AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE', 'PUBLIC BUILDING HIGH VOLTAGE')) AS HighVoltage"),
                        DB::raw("(SELECT SUM(CAST(OtherTransmissionCostAdjustmentKW AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "') AS TotalAmount")                  
                    )
                    ->limit(1)
                    ->first();
            @endphp
            <th style="padding-left: 40px;">Other Transmission Cost Adjustment (OTCA) (KW)</th>
            <td class="text-right">{{ number_format($otcaKw->Residential, 2) }}</td>
            <td class="text-right">{{ number_format($otcaKw->LowVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($otcaKw->HighVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($otcaKw->TotalAmount, 2) }}</td>
        </tr>
        <tr>
            @php
                $otcaKwh = DB::table('Billing_Rates')
                    ->select(
                        DB::raw("(SELECT SUM(CAST(OtherTransmissionCostAdjustmentKWH AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('RESIDENTIAL', 'RURAL RESIDENTIAL', 'STREET LIGHTS')) AS Residential"),
                        DB::raw("(SELECT SUM(CAST(OtherTransmissionCostAdjustmentKWH AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL', 'INDUSTRIAL', 'PUBLIC BUILDING', 'IRRIGATION/WATER SYSTEMS')) AS LowVoltage"),
                        DB::raw("(SELECT SUM(CAST(OtherTransmissionCostAdjustmentKWH AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE', 'PUBLIC BUILDING HIGH VOLTAGE')) AS HighVoltage"),
                        DB::raw("(SELECT SUM(CAST(OtherTransmissionCostAdjustmentKWH AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "') AS TotalAmount")                  
                    )
                    ->limit(1)
                    ->first();
            @endphp
            <th style="padding-left: 40px;">Other Transmission Cost Adjustment (OTCA) (KWH)</th>
            <td class="text-right">{{ number_format($otcaKwh->Residential, 2) }}</td>
            <td class="text-right">{{ number_format($otcaKwh->LowVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($otcaKwh->HighVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($otcaKwh->TotalAmount, 2) }}</td>
        </tr>
        <tr>
            @php
                $osla = DB::table('Billing_Rates')
                    ->select(
                        DB::raw("(SELECT SUM(CAST(OtherSystemLossCostAdjustment AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('RESIDENTIAL', 'RURAL RESIDENTIAL', 'STREET LIGHTS')) AS Residential"),
                        DB::raw("(SELECT SUM(CAST(OtherSystemLossCostAdjustment AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL', 'INDUSTRIAL', 'PUBLIC BUILDING', 'IRRIGATION/WATER SYSTEMS')) AS LowVoltage"),
                        DB::raw("(SELECT SUM(CAST(OtherSystemLossCostAdjustment AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE', 'PUBLIC BUILDING HIGH VOLTAGE')) AS HighVoltage"),
                        DB::raw("(SELECT SUM(CAST(OtherSystemLossCostAdjustment AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "') AS TotalAmount")                  
                    )
                    ->limit(1)
                    ->first();
            @endphp
            <th style="padding-left: 40px;">Other System Loss Cost Adjustment (OSLA) (KWH)</th>
            <td class="text-right">{{ number_format($osla->Residential, 2) }}</td>
            <td class="text-right">{{ number_format($osla->LowVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($osla->HighVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($osla->TotalAmount, 2) }}</td>
        </tr>
        <tr>
            @php
                $totalGenTransResidential = floatval($osla->Residential) + floatval($otcaKwh->Residential) + floatval($otcaKw->Residential) + floatval($ogaKwh->Residential) + floatval($systemLossCharge->Residential) + floatval($transmissionSystemKwh->Residential) + floatval($transmissionSystemKw->Residential) + floatval($generationSystem->Residential);
                $totalGenTransLowVoltage = floatval($osla->LowVoltage) + floatval($otcaKwh->LowVoltage) + floatval($otcaKw->LowVoltage) + floatval($ogaKwh->LowVoltage) + floatval($systemLossCharge->LowVoltage) + floatval($transmissionSystemKwh->LowVoltage) + floatval($transmissionSystemKw->LowVoltage) + floatval($generationSystem->LowVoltage);
                $totalGenTransHighVoltage = floatval($osla->HighVoltage) + floatval($otcaKwh->HighVoltage) + floatval($otcaKw->HighVoltage) + floatval($ogaKwh->HighVoltage) + floatval($systemLossCharge->HighVoltage) + floatval($transmissionSystemKwh->HighVoltage) + floatval($transmissionSystemKw->HighVoltage) + floatval($generationSystem->HighVoltage);
                $totalGenTrans = floatval($osla->TotalAmount) + floatval($otcaKwh->TotalAmount) + floatval($otcaKw->TotalAmount) + floatval($ogaKwh->TotalAmount) + floatval($systemLossCharge->TotalAmount) + floatval($transmissionSystemKwh->TotalAmount) + floatval($transmissionSystemKw->TotalAmount) + floatval($generationSystem->TotalAmount);
            @endphp
            <th class="text-right">Sub-total</th>
            <th class="text-right">{{ number_format($totalGenTransResidential, 2) }}</th>
            <th class="text-right">{{ number_format($totalGenTransLowVoltage, 2) }}</th>
            <th class="text-right">{{ number_format($totalGenTransHighVoltage, 2) }}</th>
            <th class="text-right">{{ number_format($totalGenTrans, 2) }}</th>
        </tr>
        <tr>
            <th>DISTRIBUTION/SUPPLY/METERING CHARGES:</th>
        </tr>
        <tr>
            @php
                $distDemandCharge = DB::table('Billing_Rates')
                    ->select(
                        DB::raw("(SELECT SUM(CAST(DistributionDemandCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('RESIDENTIAL', 'RURAL RESIDENTIAL', 'STREET LIGHTS')) AS Residential"),
                        DB::raw("(SELECT SUM(CAST(DistributionDemandCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL', 'INDUSTRIAL', 'PUBLIC BUILDING', 'IRRIGATION/WATER SYSTEMS')) AS LowVoltage"),
                        DB::raw("(SELECT SUM(CAST(DistributionDemandCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE', 'PUBLIC BUILDING HIGH VOLTAGE')) AS HighVoltage"),
                        DB::raw("(SELECT SUM(CAST(DistributionDemandCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "') AS TotalAmount")                  
                    )
                    ->limit(1)
                    ->first();
            @endphp
            <td style="padding-left: 40px;">Distribution Demand Charge</td>
            <td class="text-right">{{ number_format($distDemandCharge->Residential, 2) }}</td>
            <td class="text-right">{{ number_format($distDemandCharge->LowVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($distDemandCharge->HighVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($distDemandCharge->TotalAmount, 2) }}</td>
        </tr>
        <tr>
            @php
                $distSystemCharge = DB::table('Billing_Rates')
                    ->select(
                        DB::raw("(SELECT SUM(CAST(DistributionSystemCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('RESIDENTIAL', 'RURAL RESIDENTIAL', 'STREET LIGHTS')) AS Residential"),
                        DB::raw("(SELECT SUM(CAST(DistributionSystemCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL', 'INDUSTRIAL', 'PUBLIC BUILDING', 'IRRIGATION/WATER SYSTEMS')) AS LowVoltage"),
                        DB::raw("(SELECT SUM(CAST(DistributionSystemCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE', 'PUBLIC BUILDING HIGH VOLTAGE')) AS HighVoltage"),
                        DB::raw("(SELECT SUM(CAST(DistributionSystemCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "') AS TotalAmount")                  
                    )
                    ->limit(1)
                    ->first();
            @endphp
            <td style="padding-left: 40px;">Distribution System Charge</td>
            <td class="text-right">{{ number_format($distSystemCharge->Residential, 2) }}</td>
            <td class="text-right">{{ number_format($distSystemCharge->LowVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($distSystemCharge->HighVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($distSystemCharge->TotalAmount, 2) }}</td>
        </tr>
        <tr>
            @php
                $supplyRetCustCharge = DB::table('Billing_Rates')
                    ->select(
                        DB::raw("(SELECT SUM(CAST(SupplyRetailCustomerCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('RESIDENTIAL', 'RURAL RESIDENTIAL', 'STREET LIGHTS')) AS Residential"),
                        DB::raw("(SELECT SUM(CAST(SupplyRetailCustomerCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL', 'INDUSTRIAL', 'PUBLIC BUILDING', 'IRRIGATION/WATER SYSTEMS')) AS LowVoltage"),
                        DB::raw("(SELECT SUM(CAST(SupplyRetailCustomerCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE', 'PUBLIC BUILDING HIGH VOLTAGE')) AS HighVoltage"),
                        DB::raw("(SELECT SUM(CAST(SupplyRetailCustomerCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "') AS TotalAmount")                  
                    )
                    ->limit(1)
                    ->first();
            @endphp
            <td style="padding-left: 40px;">Supply Retail Customer Charge</td>
            <td class="text-right">{{ number_format($supplyRetCustCharge->Residential, 2) }}</td>
            <td class="text-right">{{ number_format($supplyRetCustCharge->LowVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($supplyRetCustCharge->HighVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($supplyRetCustCharge->TotalAmount, 2) }}</td>
        </tr>
        <tr>
            @php
                $supplySystemCharge = DB::table('Billing_Rates')
                    ->select(
                        DB::raw("(SELECT SUM(CAST(SupplySystemCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('RESIDENTIAL', 'RURAL RESIDENTIAL', 'STREET LIGHTS')) AS Residential"),
                        DB::raw("(SELECT SUM(CAST(SupplySystemCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL', 'INDUSTRIAL', 'PUBLIC BUILDING', 'IRRIGATION/WATER SYSTEMS')) AS LowVoltage"),
                        DB::raw("(SELECT SUM(CAST(SupplySystemCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE', 'PUBLIC BUILDING HIGH VOLTAGE')) AS HighVoltage"),
                        DB::raw("(SELECT SUM(CAST(SupplySystemCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "') AS TotalAmount")                  
                    )
                    ->limit(1)
                    ->first();
            @endphp
            <td style="padding-left: 40px;">Supply System Charge</td>
            <td class="text-right">{{ number_format($supplySystemCharge->Residential, 2) }}</td>
            <td class="text-right">{{ number_format($supplySystemCharge->LowVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($supplySystemCharge->HighVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($supplySystemCharge->TotalAmount, 2) }}</td>
        </tr>
        <tr>
            @php
                $meteringRetCharge = DB::table('Billing_Rates')
                    ->select(
                        DB::raw("(SELECT SUM(CAST(MeteringRetailCustomerCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('RESIDENTIAL', 'RURAL RESIDENTIAL', 'STREET LIGHTS')) AS Residential"),
                        DB::raw("(SELECT SUM(CAST(MeteringRetailCustomerCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL', 'INDUSTRIAL', 'PUBLIC BUILDING', 'IRRIGATION/WATER SYSTEMS')) AS LowVoltage"),
                        DB::raw("(SELECT SUM(CAST(MeteringRetailCustomerCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE', 'PUBLIC BUILDING HIGH VOLTAGE')) AS HighVoltage"),
                        DB::raw("(SELECT SUM(CAST(MeteringRetailCustomerCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "') AS TotalAmount")                  
                    )
                    ->limit(1)
                    ->first();
            @endphp
            <td style="padding-left: 40px;">Metering Retail Customer Charge</td>
            <td class="text-right">{{ number_format($meteringRetCharge->Residential, 2) }}</td>
            <td class="text-right">{{ number_format($meteringRetCharge->LowVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($meteringRetCharge->HighVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($meteringRetCharge->TotalAmount, 2) }}</td>
        </tr>
        <tr>
            @php
                $meteringSystemCharge = DB::table('Billing_Rates')
                    ->select(
                        DB::raw("(SELECT SUM(CAST(MeteringSystemCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('RESIDENTIAL', 'RURAL RESIDENTIAL', 'STREET LIGHTS')) AS Residential"),
                        DB::raw("(SELECT SUM(CAST(MeteringSystemCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL', 'INDUSTRIAL', 'PUBLIC BUILDING', 'IRRIGATION/WATER SYSTEMS')) AS LowVoltage"),
                        DB::raw("(SELECT SUM(CAST(MeteringSystemCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE', 'PUBLIC BUILDING HIGH VOLTAGE')) AS HighVoltage"),
                        DB::raw("(SELECT SUM(CAST(MeteringSystemCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "') AS TotalAmount")                  
                    )
                    ->limit(1)
                    ->first();
            @endphp
            <td style="padding-left: 40px;">Metering System Charge</td>
            <td class="text-right">{{ number_format($meteringSystemCharge->Residential, 2) }}</td>
            <td class="text-right">{{ number_format($meteringSystemCharge->LowVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($meteringSystemCharge->HighVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($meteringSystemCharge->TotalAmount, 2) }}</td>
        </tr>
        <tr>
            @php
                $rfsc = DB::table('Billing_Rates')
                    ->select(
                        DB::raw("(SELECT SUM(CAST(RFSC AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('RESIDENTIAL', 'RURAL RESIDENTIAL', 'STREET LIGHTS')) AS Residential"),
                        DB::raw("(SELECT SUM(CAST(RFSC AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL', 'INDUSTRIAL', 'PUBLIC BUILDING', 'IRRIGATION/WATER SYSTEMS')) AS LowVoltage"),
                        DB::raw("(SELECT SUM(CAST(RFSC AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE', 'PUBLIC BUILDING HIGH VOLTAGE')) AS HighVoltage"),
                        DB::raw("(SELECT SUM(CAST(RFSC AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "') AS TotalAmount")                  
                    )
                    ->limit(1)
                    ->first();
            @endphp
            <td style="padding-left: 40px;">RFSC</td>
            <td class="text-right">{{ number_format($rfsc->Residential, 2) }}</td>
            <td class="text-right">{{ number_format($rfsc->LowVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($rfsc->HighVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($rfsc->TotalAmount, 2) }}</td>
        </tr>
        <tr>
            <td style="padding-left: 40px;">Member's Contribution for CAPEX <strong>(?)</strong></td>
            <td class="text-right"></td>
            <td class="text-right"></td>
            <td class="text-right"></td>
            <td class="text-right"></td>
        </tr>
        <tr>
            @php
                $totalDistResidential = floatval($rfsc->Residential) + floatval($meteringSystemCharge->Residential) + floatval($meteringRetCharge->Residential) + floatval($supplySystemCharge->Residential) + floatval($supplyRetCustCharge->Residential) + floatval($distSystemCharge->Residential) + floatval($distDemandCharge->Residential);
                $totalDistLowVoltage = floatval($rfsc->LowVoltage) + floatval($meteringSystemCharge->LowVoltage) + floatval($meteringRetCharge->LowVoltage) + floatval($supplySystemCharge->LowVoltage) + floatval($supplyRetCustCharge->LowVoltage) + floatval($distSystemCharge->LowVoltage) + floatval($distDemandCharge->LowVoltage);
                $totalDistHighVoltage = floatval($rfsc->HighVoltage) + floatval($meteringSystemCharge->HighVoltage) + floatval($meteringRetCharge->HighVoltage) + floatval($supplySystemCharge->HighVoltage) + floatval($supplyRetCustCharge->HighVoltage) + floatval($distSystemCharge->HighVoltage) + floatval($distDemandCharge->HighVoltage);
                $totalDist = floatval($rfsc->TotalAmount) + floatval($meteringSystemCharge->TotalAmount) + floatval($meteringRetCharge->TotalAmount) + floatval($supplySystemCharge->TotalAmount) + floatval($supplyRetCustCharge->TotalAmount) + floatval($distSystemCharge->TotalAmount) + floatval($distDemandCharge->TotalAmount);
            @endphp
            <th class="text-right">Sub-total</th>
            <th class="text-right">{{ number_format($totalDistResidential, 2) }}</th>
            <th class="text-right">{{ number_format($totalDistLowVoltage, 2) }}</th>
            <th class="text-right">{{ number_format($totalDistHighVoltage, 2) }}</th>
            <th class="text-right">{{ number_format($totalDist, 2) }}</th>
        </tr>
        <tr>
            <th>OTHERS:</th>
        </tr>
        <tr>
            @php
                $lifelineRate = DB::table('Billing_Rates')
                    ->select(
                        DB::raw("(SELECT SUM(CAST(LifelineRate AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('RESIDENTIAL', 'RURAL RESIDENTIAL', 'STREET LIGHTS')) AS Residential"),
                        DB::raw("(SELECT SUM(CAST(LifelineRate AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL', 'INDUSTRIAL', 'PUBLIC BUILDING', 'IRRIGATION/WATER SYSTEMS')) AS LowVoltage"),
                        DB::raw("(SELECT SUM(CAST(LifelineRate AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE', 'PUBLIC BUILDING HIGH VOLTAGE')) AS HighVoltage"),
                        DB::raw("(SELECT SUM(CAST(LifelineRate AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "') AS TotalAmount")                  
                    )
                    ->limit(1)
                    ->first();
            @endphp
            <td style="padding-left: 40px;">Lifeline Rate (Discount/Subsidy)</td>
            <td class="text-right">{{ number_format($lifelineRate->Residential, 2) }}</td>
            <td class="text-right">{{ number_format($lifelineRate->LowVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($lifelineRate->HighVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($lifelineRate->TotalAmount, 2) }}</td>
        </tr>
        <tr>
            @php
                $iccSubsidy = DB::table('Billing_Rates')
                    ->select(
                        DB::raw("(SELECT SUM(CAST(InterClassCrossSubsidyCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('RESIDENTIAL', 'RURAL RESIDENTIAL', 'STREET LIGHTS')) AS Residential"),
                        DB::raw("(SELECT SUM(CAST(InterClassCrossSubsidyCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL', 'INDUSTRIAL', 'PUBLIC BUILDING', 'IRRIGATION/WATER SYSTEMS')) AS LowVoltage"),
                        DB::raw("(SELECT SUM(CAST(InterClassCrossSubsidyCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE', 'PUBLIC BUILDING HIGH VOLTAGE')) AS HighVoltage"),
                        DB::raw("(SELECT SUM(CAST(InterClassCrossSubsidyCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "') AS TotalAmount")                  
                    )
                    ->limit(1)
                    ->first();
            @endphp
            <td style="padding-left: 40px;">Inter-Class Cross Subsidy Charge</td>
            <td class="text-right">{{ number_format($iccSubsidy->Residential, 2) }}</td>
            <td class="text-right">{{ number_format($iccSubsidy->LowVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($iccSubsidy->HighVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($iccSubsidy->TotalAmount, 2) }}</td>
        </tr>
        <tr>
            @php
                $totalOthersResidential = floatval($iccSubsidy->Residential) + floatval($lifelineRate->Residential);
                $totalOthersLowVoltage = floatval($iccSubsidy->LowVoltage) + floatval($lifelineRate->LowVoltage);
                $totalOthersHighVoltage = floatval($iccSubsidy->HighVoltage) + floatval($lifelineRate->HighVoltage);
                $totalOthers = floatval($iccSubsidy->TotalAmount) + floatval($lifelineRate->TotalAmount);
            @endphp
            <th class="text-right">Sub-total</th>
            <th class="text-right">{{ number_format($totalOthersResidential, 2) }}</th>
            <th class="text-right">{{ number_format($totalOthersLowVoltage, 2) }}</th>
            <th class="text-right">{{ number_format($totalOthersHighVoltage, 2) }}</th>
            <th class="text-right">{{ number_format($totalOthers, 2) }}</th>
        </tr>        
        <tr>
            @php
                $rpt = DB::table('Billing_Rates')
                    ->select(
                        DB::raw("(SELECT SUM(CAST(RealPropertyTax AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('RESIDENTIAL', 'RURAL RESIDENTIAL', 'STREET LIGHTS')) AS Residential"),
                        DB::raw("(SELECT SUM(CAST(RealPropertyTax AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL', 'INDUSTRIAL', 'PUBLIC BUILDING', 'IRRIGATION/WATER SYSTEMS')) AS LowVoltage"),
                        DB::raw("(SELECT SUM(CAST(RealPropertyTax AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE', 'PUBLIC BUILDING HIGH VOLTAGE')) AS HighVoltage"),
                        DB::raw("(SELECT SUM(CAST(RealPropertyTax AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "') AS TotalAmount")                  
                    )
                    ->limit(1)
                    ->first();
            @endphp
            <td style="padding-left: 40px;">Real Property Tax (RPT)</td>
            <td class="text-right">{{ number_format($rpt->Residential, 2) }}</td>
            <td class="text-right">{{ number_format($rpt->LowVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($rpt->HighVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($rpt->TotalAmount, 2) }}</td>
        </tr>
        <tr>
            <th>UNIVERSAL CHARGE:</th>
        </tr>
        <tr>
            @php
                $missionary = DB::table('Billing_Rates')
                    ->select(
                        DB::raw("(SELECT SUM(CAST(MissionaryElectrificationCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('RESIDENTIAL', 'RURAL RESIDENTIAL', 'STREET LIGHTS')) AS Residential"),
                        DB::raw("(SELECT SUM(CAST(MissionaryElectrificationCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL', 'INDUSTRIAL', 'PUBLIC BUILDING', 'IRRIGATION/WATER SYSTEMS')) AS LowVoltage"),
                        DB::raw("(SELECT SUM(CAST(MissionaryElectrificationCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE', 'PUBLIC BUILDING HIGH VOLTAGE')) AS HighVoltage"),
                        DB::raw("(SELECT SUM(CAST(MissionaryElectrificationCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "') AS TotalAmount")                  
                    )
                    ->limit(1)
                    ->first();
            @endphp
            <td style="padding-left: 40px;">Missionary Electrification Charge</td>
            <td class="text-right">{{ number_format($missionary->Residential, 2) }}</td>
            <td class="text-right">{{ number_format($missionary->LowVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($missionary->HighVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($missionary->TotalAmount, 2) }}</td>
        </tr>
        <tr>
            @php
                $environmental = DB::table('Billing_Rates')
                    ->select(
                        DB::raw("(SELECT SUM(CAST(EnvironmentalCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('RESIDENTIAL', 'RURAL RESIDENTIAL', 'STREET LIGHTS')) AS Residential"),
                        DB::raw("(SELECT SUM(CAST(EnvironmentalCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL', 'INDUSTRIAL', 'PUBLIC BUILDING', 'IRRIGATION/WATER SYSTEMS')) AS LowVoltage"),
                        DB::raw("(SELECT SUM(CAST(EnvironmentalCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE', 'PUBLIC BUILDING HIGH VOLTAGE')) AS HighVoltage"),
                        DB::raw("(SELECT SUM(CAST(EnvironmentalCharge AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "') AS TotalAmount")                  
                    )
                    ->limit(1)
                    ->first();
            @endphp
            <td style="padding-left: 40px;">Environmental Charge</td>
            <td class="text-right">{{ number_format($environmental->Residential, 2) }}</td>
            <td class="text-right">{{ number_format($environmental->LowVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($environmental->HighVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($environmental->TotalAmount, 2) }}</td>
        </tr>
        <tr>
            @php
                $strandedcc = DB::table('Billing_Rates')
                    ->select(
                        DB::raw("(SELECT SUM(CAST(StrandedContractCosts AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('RESIDENTIAL', 'RURAL RESIDENTIAL', 'STREET LIGHTS')) AS Residential"),
                        DB::raw("(SELECT SUM(CAST(StrandedContractCosts AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL', 'INDUSTRIAL', 'PUBLIC BUILDING', 'IRRIGATION/WATER SYSTEMS')) AS LowVoltage"),
                        DB::raw("(SELECT SUM(CAST(StrandedContractCosts AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE', 'PUBLIC BUILDING HIGH VOLTAGE')) AS HighVoltage"),
                        DB::raw("(SELECT SUM(CAST(StrandedContractCosts AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "') AS TotalAmount")                  
                    )
                    ->limit(1)
                    ->first();
            @endphp
            <td style="padding-left: 40px;">Stranded Contract Costs</td>
            <td class="text-right">{{ number_format($strandedcc->Residential, 2) }}</td>
            <td class="text-right">{{ number_format($strandedcc->LowVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($strandedcc->HighVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($strandedcc->TotalAmount, 2) }}</td>
        </tr>
        <tr>
            <td style="padding-left: 40px;">Cash Incentive for Renewable Energy <strong>(?)</strong></td>
            <td class="text-right"></td>
            <td class="text-right"></td>
            <td class="text-right"></td>
            <td class="text-right"></td>
        </tr>
        <tr>
            @php
                $fitAll = DB::table('Billing_Rates')
                    ->select(
                        DB::raw("(SELECT SUM(CAST(FeedInTariffAllowance AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('RESIDENTIAL', 'RURAL RESIDENTIAL', 'STREET LIGHTS')) AS Residential"),
                        DB::raw("(SELECT SUM(CAST(FeedInTariffAllowance AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL', 'INDUSTRIAL', 'PUBLIC BUILDING', 'IRRIGATION/WATER SYSTEMS')) AS LowVoltage"),
                        DB::raw("(SELECT SUM(CAST(FeedInTariffAllowance AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE', 'PUBLIC BUILDING HIGH VOLTAGE')) AS HighVoltage"),
                        DB::raw("(SELECT SUM(CAST(FeedInTariffAllowance AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "') AS TotalAmount")                  
                    )
                    ->limit(1)
                    ->first();
            @endphp
            <td style="padding-left: 40px;">Feed-inTariff Allowance</td>
            <td class="text-right">{{ number_format($fitAll->Residential, 2) }}</td>
            <td class="text-right">{{ number_format($fitAll->LowVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($fitAll->HighVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($fitAll->TotalAmount, 2) }}</td>
        </tr>
        <tr>
            <td style="padding-left: 40px;">NPC Contract Cost <strong>(?)</strong></td>
            <td class="text-right"></td>
            <td class="text-right"></td>
            <td class="text-right"></td>
            <td class="text-right"></td>
        </tr>
        <tr>
            @php
                $npcStrandedDebt = DB::table('Billing_Rates')
                    ->select(
                        DB::raw("(SELECT SUM(CAST(NPCStrandedDebt AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('RESIDENTIAL', 'RURAL RESIDENTIAL', 'STREET LIGHTS')) AS Residential"),
                        DB::raw("(SELECT SUM(CAST(NPCStrandedDebt AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL', 'INDUSTRIAL', 'PUBLIC BUILDING', 'IRRIGATION/WATER SYSTEMS')) AS LowVoltage"),
                        DB::raw("(SELECT SUM(CAST(NPCStrandedDebt AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE', 'PUBLIC BUILDING HIGH VOLTAGE')) AS HighVoltage"),
                        DB::raw("(SELECT SUM(CAST(NPCStrandedDebt AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "') AS TotalAmount")                  
                    )
                    ->limit(1)
                    ->first();
            @endphp
            <td style="padding-left: 40px;">NPC Stranded Debt</td>
            <td class="text-right">{{ number_format($npcStrandedDebt->Residential, 2) }}</td>
            <td class="text-right">{{ number_format($npcStrandedDebt->LowVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($npcStrandedDebt->HighVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($npcStrandedDebt->TotalAmount, 2) }}</td>
        </tr>
        <tr>
            @php
                $totalUnivResidential = floatval($npcStrandedDebt->Residential) + floatval($fitAll->Residential) + floatval($strandedcc->Residential) + floatval($environmental->Residential) + floatval($missionary->Residential);
                $totalUnivLowVoltage = floatval($npcStrandedDebt->LowVoltage) + floatval($fitAll->LowVoltage) + floatval($strandedcc->LowVoltage) + floatval($environmental->LowVoltage) + floatval($missionary->LowVoltage);
                $totalUnivHighVoltage = floatval($npcStrandedDebt->HighVoltage) + floatval($fitAll->HighVoltage) + floatval($strandedcc->HighVoltage) + floatval($environmental->HighVoltage) + floatval($missionary->HighVoltage);
                $totalUniv = floatval($npcStrandedDebt->TotalAmount) + floatval($fitAll->TotalAmount) + floatval($strandedcc->TotalAmount) + floatval($environmental->TotalAmount) + floatval($missionary->TotalAmount);
            @endphp
            <th class="text-right">Sub-total</th>
            <th class="text-right">{{ number_format($totalUnivResidential, 2) }}</th>
            <th class="text-right">{{ number_format($totalUnivLowVoltage, 2) }}</th>
            <th class="text-right">{{ number_format($totalUnivHighVoltage, 2) }}</th>
            <th class="text-right">{{ number_format($totalUniv, 2) }}</th>
        </tr>  
        <tr>
            <td style="padding-left: 40px;">UC Refund <strong>(?)</strong></td>
            <td class="text-right"></td>
            <td class="text-right"></td>
            <td class="text-right"></td>
            <td class="text-right"></td>
        </tr>
        <tr>
            @php
                $scDisc = DB::table('Billing_Rates')
                    ->select(
                        DB::raw("(SELECT SUM(CAST(SeniorCitizenSubsidy AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND CAST(SeniorCitizenSubsidy AS decimal(10,2)) < 0 AND ConsumerType IN ('RESIDENTIAL', 'RURAL RESIDENTIAL', 'STREET LIGHTS')) AS Residential"),
                        DB::raw("(SELECT SUM(CAST(SeniorCitizenSubsidy AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND CAST(SeniorCitizenSubsidy AS decimal(10,2)) < 0 AND ConsumerType IN ('COMMERCIAL', 'INDUSTRIAL', 'PUBLIC BUILDING', 'IRRIGATION/WATER SYSTEMS')) AS LowVoltage"),
                        DB::raw("(SELECT SUM(CAST(SeniorCitizenSubsidy AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND CAST(SeniorCitizenSubsidy AS decimal(10,2)) < 0 AND ConsumerType IN ('COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE', 'PUBLIC BUILDING HIGH VOLTAGE')) AS HighVoltage"),
                        DB::raw("(SELECT SUM(CAST(SeniorCitizenSubsidy AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND CAST(SeniorCitizenSubsidy AS decimal(10,2)) < 0) AS TotalAmount")                  
                    )
                    ->limit(1)
                    ->first();
            @endphp
            <td style="padding-left: 40px;">Senior Citizen Discount</td>
            <td class="text-right">{{ number_format($scDisc->Residential, 2) }}</td>
            <td class="text-right">{{ number_format($scDisc->LowVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($scDisc->HighVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($scDisc->TotalAmount, 2) }}</td>
        </tr>
        <tr>
            @php
                $scSubsidy = DB::table('Billing_Rates')
                    ->select(
                        DB::raw("(SELECT SUM(CAST(SeniorCitizenSubsidy AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND CAST(SeniorCitizenSubsidy AS decimal(10,2)) > 0 AND ConsumerType IN ('RESIDENTIAL', 'RURAL RESIDENTIAL', 'STREET LIGHTS')) AS Residential"),
                        DB::raw("(SELECT SUM(CAST(SeniorCitizenSubsidy AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND CAST(SeniorCitizenSubsidy AS decimal(10,2)) > 0 AND ConsumerType IN ('COMMERCIAL', 'INDUSTRIAL', 'PUBLIC BUILDING', 'IRRIGATION/WATER SYSTEMS')) AS LowVoltage"),
                        DB::raw("(SELECT SUM(CAST(SeniorCitizenSubsidy AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND CAST(SeniorCitizenSubsidy AS decimal(10,2)) > 0 AND ConsumerType IN ('COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE', 'PUBLIC BUILDING HIGH VOLTAGE')) AS HighVoltage"),
                        DB::raw("(SELECT SUM(CAST(SeniorCitizenSubsidy AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND CAST(SeniorCitizenSubsidy AS decimal(10,2)) > 0) AS TotalAmount")                  
                    )
                    ->limit(1)
                    ->first();
            @endphp
            <td style="padding-left: 40px;">Senior Citizen Subsidy</td>
            <td class="text-right">{{ number_format($scSubsidy->Residential, 2) }}</td>
            <td class="text-right">{{ number_format($scSubsidy->LowVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($scSubsidy->HighVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($scSubsidy->TotalAmount, 2) }}</td>
        </tr>
        <tr>
            @php
                $scAdj = DB::table('Billing_Rates')
                    ->select(
                        DB::raw("(SELECT SUM(CAST(SeniorCitizenDiscountAndSubsidyAdjustment AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('RESIDENTIAL', 'RURAL RESIDENTIAL', 'STREET LIGHTS')) AS Residential"),
                        DB::raw("(SELECT SUM(CAST(SeniorCitizenDiscountAndSubsidyAdjustment AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL', 'INDUSTRIAL', 'PUBLIC BUILDING', 'IRRIGATION/WATER SYSTEMS')) AS LowVoltage"),
                        DB::raw("(SELECT SUM(CAST(SeniorCitizenDiscountAndSubsidyAdjustment AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE', 'PUBLIC BUILDING HIGH VOLTAGE')) AS HighVoltage"),
                        DB::raw("(SELECT SUM(CAST(SeniorCitizenDiscountAndSubsidyAdjustment AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "') AS TotalAmount")                  
                    )
                    ->limit(1)
                    ->first();
            @endphp
            <th style="padding-left: 40px;">Senior Citizen Discount & Subsidy Adjustment (KWH)</th>
            <td class="text-right">{{ number_format($scAdj->Residential, 2) }}</td>
            <td class="text-right">{{ number_format($scAdj->LowVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($scAdj->HighVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($scAdj->TotalAmount, 2) }}</td>
        </tr>
        <tr>
            @php
                $othersAdj = DB::table('Billing_Rates')
                    ->select(
                        DB::raw("(SELECT SUM(CAST(OtherLifelineRateCostAdjustment AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('RESIDENTIAL', 'RURAL RESIDENTIAL', 'STREET LIGHTS')) AS Residential"),
                        DB::raw("(SELECT SUM(CAST(OtherLifelineRateCostAdjustment AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL', 'INDUSTRIAL', 'PUBLIC BUILDING', 'IRRIGATION/WATER SYSTEMS')) AS LowVoltage"),
                        DB::raw("(SELECT SUM(CAST(OtherLifelineRateCostAdjustment AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE', 'PUBLIC BUILDING HIGH VOLTAGE')) AS HighVoltage"),
                        DB::raw("(SELECT SUM(CAST(OtherLifelineRateCostAdjustment AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "') AS TotalAmount")                  
                    )
                    ->limit(1)
                    ->first();
            @endphp
            <th style="padding-left: 40px;">Other Lifeline Rate Cost Adjustment (OLRA) (KWH)</th>
            <td class="text-right">{{ number_format($othersAdj->Residential, 2) }}</td>
            <td class="text-right">{{ number_format($othersAdj->LowVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($othersAdj->HighVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($othersAdj->TotalAmount, 2) }}</td>
        </tr>
        <tr>
            <th>GOVERNMENT REVENUES:</th>
        </tr>
        <tr>
            @php
                $genVat = DB::table('Billing_Rates')
                    ->select(
                        DB::raw("(SELECT SUM(CAST(GenerationVAT AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('RESIDENTIAL', 'RURAL RESIDENTIAL', 'STREET LIGHTS')) AS Residential"),
                        DB::raw("(SELECT SUM(CAST(GenerationVAT AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL', 'INDUSTRIAL', 'PUBLIC BUILDING', 'IRRIGATION/WATER SYSTEMS')) AS LowVoltage"),
                        DB::raw("(SELECT SUM(CAST(GenerationVAT AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE', 'PUBLIC BUILDING HIGH VOLTAGE')) AS HighVoltage"),
                        DB::raw("(SELECT SUM(CAST(GenerationVAT AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "') AS TotalAmount")                  
                    )
                    ->limit(1)
                    ->first();
            @endphp
            <td style="padding-left: 40px;">VAT - Generation</td>
            <td class="text-right">{{ number_format($genVat->Residential, 2) }}</td>
            <td class="text-right">{{ number_format($genVat->LowVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($genVat->HighVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($genVat->TotalAmount, 2) }}</td>
        </tr>
        <tr>
            @php
                $transVat = DB::table('Billing_Rates')
                    ->select(
                        DB::raw("(SELECT SUM(CAST(TransmissionVAT AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('RESIDENTIAL', 'RURAL RESIDENTIAL', 'STREET LIGHTS')) AS Residential"),
                        DB::raw("(SELECT SUM(CAST(TransmissionVAT AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL', 'INDUSTRIAL', 'PUBLIC BUILDING', 'IRRIGATION/WATER SYSTEMS')) AS LowVoltage"),
                        DB::raw("(SELECT SUM(CAST(TransmissionVAT AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE', 'PUBLIC BUILDING HIGH VOLTAGE')) AS HighVoltage"),
                        DB::raw("(SELECT SUM(CAST(TransmissionVAT AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "') AS TotalAmount")                  
                    )
                    ->limit(1)
                    ->first();
            @endphp
            <td style="padding-left: 40px;">VAT - Transmission</td>
            <td class="text-right">{{ number_format($transVat->Residential, 2) }}</td>
            <td class="text-right">{{ number_format($transVat->LowVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($transVat->HighVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($transVat->TotalAmount, 2) }}</td>
        </tr>
        <tr>
            @php
                $sysLossVat = DB::table('Billing_Rates')
                    ->select(
                        DB::raw("(SELECT SUM(CAST(SystemLossVAT AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('RESIDENTIAL', 'RURAL RESIDENTIAL', 'STREET LIGHTS')) AS Residential"),
                        DB::raw("(SELECT SUM(CAST(SystemLossVAT AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL', 'INDUSTRIAL', 'PUBLIC BUILDING', 'IRRIGATION/WATER SYSTEMS')) AS LowVoltage"),
                        DB::raw("(SELECT SUM(CAST(SystemLossVAT AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE', 'PUBLIC BUILDING HIGH VOLTAGE')) AS HighVoltage"),
                        DB::raw("(SELECT SUM(CAST(SystemLossVAT AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "') AS TotalAmount")                  
                    )
                    ->limit(1)
                    ->first();
            @endphp
            <td style="padding-left: 40px;">VAT - System Loss</td>
            <td class="text-right">{{ number_format($sysLossVat->Residential, 2) }}</td>
            <td class="text-right">{{ number_format($sysLossVat->LowVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($sysLossVat->HighVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($sysLossVat->TotalAmount, 2) }}</td>
        </tr>
        <tr>
            @php
                $distVat = DB::table('Billing_Rates')
                    ->select(
                        DB::raw("(SELECT SUM(CAST(DistributionVAT AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('RESIDENTIAL', 'RURAL RESIDENTIAL', 'STREET LIGHTS')) AS Residential"),
                        DB::raw("(SELECT SUM(CAST(DistributionVAT AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL', 'INDUSTRIAL', 'PUBLIC BUILDING', 'IRRIGATION/WATER SYSTEMS')) AS LowVoltage"),
                        DB::raw("(SELECT SUM(CAST(DistributionVAT AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE', 'PUBLIC BUILDING HIGH VOLTAGE')) AS HighVoltage"),
                        DB::raw("(SELECT SUM(CAST(DistributionVAT AS decimal(10,2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "') AS TotalAmount")                  
                    )
                    ->limit(1)
                    ->first();
            @endphp
            <td style="padding-left: 40px;">VAT - Distribution & Others</td>
            <td class="text-right">{{ number_format($distVat->Residential, 2) }}</td>
            <td class="text-right">{{ number_format($distVat->LowVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($distVat->HighVoltage, 2) }}</td>
            <td class="text-right">{{ number_format($distVat->TotalAmount, 2) }}</td>
        </tr>
        <tr>
            @php
                $totalVatResidential = floatval($distVat->Residential) + floatval($sysLossVat->Residential) + floatval($transVat->Residential) + floatval($genVat->Residential);
                $totalVatLowVoltage = floatval($distVat->LowVoltage) + floatval($sysLossVat->LowVoltage) + floatval($transVat->LowVoltage) + floatval($genVat->LowVoltage);
                $totalVatHighVoltage = floatval($distVat->HighVoltage) + floatval($sysLossVat->HighVoltage) + floatval($transVat->HighVoltage) + floatval($genVat->HighVoltage);
                $totalVat = floatval($distVat->TotalAmount) + floatval($sysLossVat->TotalAmount) + floatval($transVat->TotalAmount) + floatval($genVat->TotalAmount);
            @endphp
            <th class="text-right">Sub-total</th>
            <th class="text-right">{{ number_format($totalVatResidential, 2) }}</th>
            <th class="text-right">{{ number_format($totalVatLowVoltage, 2) }}</th>
            <th class="text-right">{{ number_format($totalVatHighVoltage, 2) }}</th>
            <th class="text-right">{{ number_format($totalVat, 2) }}</th>
        </tr> 
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
        <tr>
            @php
                $grandTotalResidential = $totalGenTransResidential + $totalDistResidential + $totalOthersResidential + floatval($rpt->Residential) + $totalUnivResidential + floatval($scDisc->Residential) + floatval($scSubsidy->Residential) + floatval($scAdj->Residential) + floatval($othersAdj->Residential) + $totalVatResidential;
                $grandTotalLowVoltage = $totalGenTransLowVoltage + $totalDistLowVoltage + $totalOthersLowVoltage + floatval($rpt->LowVoltage) + $totalUnivLowVoltage + floatval($scDisc->LowVoltage) + floatval($scSubsidy->LowVoltage) + floatval($scAdj->LowVoltage) + floatval($othersAdj->LowVoltage) + $totalVatLowVoltage;
                $grandTotalHighVoltage = $totalGenTransHighVoltage + $totalDistHighVoltage + $totalOthersHighVoltage + floatval($rpt->HighVoltage) + $totalUnivHighVoltage + floatval($scDisc->HighVoltage) + floatval($scSubsidy->HighVoltage) + floatval($scAdj->HighVoltage) + floatval($othersAdj->HighVoltage) + $totalVatHighVoltage;
                $grandTotal = $totalGenTrans + $totalDist + $totalOthers + floatval($rpt->TotalAmount) + $totalUniv + floatval($scDisc->TotalAmount) + floatval($scSubsidy->TotalAmount) + floatval($scAdj->TotalAmount) + floatval($othersAdj->TotalAmount) + $totalVat;
            @endphp
            <th class="text-right">GRAND TOTAL</th>
            <th class="text-right">{{ number_format($grandTotalResidential, 2) }}</th>
            <th class="text-right">{{ number_format($grandTotalLowVoltage, 2) }}</th>
            <th class="text-right">{{ number_format($grandTotalHighVoltage, 2) }}</th>
            <th class="text-right">{{ number_format($grandTotal, 2) }}</th>
        </tr> 
        <tr>
            @php
                $totalQry = DB::table("Billing_Bills")
                    ->select(
                        DB::raw("(SELECT SUM(CAST(KwhUsed AS decimal(10, 2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('RESIDENTIAL', 'RURAL RESIDENTIAL', 'STREET LIGHTS')) AS TotalResidential"),
                        DB::raw("(SELECT SUM(CAST(KwhUsed AS decimal(10, 2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL', 'INDUSTRIAL', 'PUBLIC BUILDING', 'IRRIGATION/WATER SYSTEMS')) AS TotalLowVoltage"),
                        DB::raw("(SELECT SUM(CAST(KwhUsed AS decimal(10, 2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE', 'PUBLIC BUILDING HIGH VOLTAGE')) AS TotalHighVoltage"),
                        DB::raw("(SELECT SUM(CAST(KwhUsed AS decimal(10, 2))) FROM Billing_Bills WHERE ServicePeriod='" . $period . "') AS Total"),
                        DB::raw("(SELECT COUNT(id) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('RESIDENTIAL', 'RURAL RESIDENTIAL', 'STREET LIGHTS')) AS ResidentialCount"),
                        DB::raw("(SELECT COUNT(id) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL', 'INDUSTRIAL', 'PUBLIC BUILDING', 'IRRIGATION/WATER SYSTEMS')) AS LowVoltageCount"),
                        DB::raw("(SELECT COUNT(id) FROM Billing_Bills WHERE ServicePeriod='" . $period . "' AND ConsumerType IN ('COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE', 'PUBLIC BUILDING HIGH VOLTAGE')) AS HighVoltageCount"),
                        DB::raw("(SELECT COUNT(id) FROM Billing_Bills WHERE ServicePeriod='" . $period . "') AS TotalCount")
                    )
                    ->limit(1)
                    ->first();
            @endphp
            <th class="text-right">TOTAL KWH USED</th>
            <th class="text-right">{{ number_format($totalQry->TotalResidential, 2) }}</th>
            <th class="text-right">{{ number_format($totalQry->TotalLowVoltage, 2) }}</th>
            <th class="text-right">{{ number_format($totalQry->TotalHighVoltage, 2) }}</th>
            <th class="text-right">{{ number_format($totalQry->Total, 2) }}</th>
        </tr> 
        <tr>
            <th class="text-right">TOTAL DEMAND KW <strong>(?)</strong></th>
            <th class="text-right">{{ number_format($totalQry->ResidentialCount) }}</th>
            <th class="text-right">{{ number_format($totalQry->LowVoltageCount) }}</th>
            <th class="text-right">{{ number_format($totalQry->HighVoltageCount) }}</th>
            <th class="text-right">{{ number_format($totalQry->TotalCount) }}</th>
        </tr> 
    </tbody>
</table>