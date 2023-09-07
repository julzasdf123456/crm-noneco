<?php
namespace App\Imports;

use App\Models\Rates;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use App\Models\IDGenerator;

class WaterSystemsRate implements WithMappedCells, WithCalculatedFormulas, ToModel 
{

    private $servicePeriod, $userId, $district, $areaCode, $startingCell, $incrementingCell;

    public function __construct($servicePeriod, $userId, $district, $areaCode, $startingCell, $incrementingCell)
    {
        $this->servicePeriod = $servicePeriod;
        $this->userId = $userId;
        $this->district = $district;
        $this->areaCode = $areaCode;
        $this->startingCell = $startingCell; // Cell 63
        $this->incrementingCell = $incrementingCell;
    }

    public function mapping(): array
    {
        return [
            'GenerationSystemCharge' => 'G' . $this->startingCell,
            'TransmissionDeliveryChargeKW' => 'G' . ($this->startingCell+1),
            'TransmissionDeliveryChargeKWH' => 'G' . ($this->startingCell+2),
            'SystemLossCharge' => 'G' . ($this->startingCell+3),
            'OtherGenerationRateAdjustment' => 'G' . ($this->startingCell+5),
            'OtherTransmissionCostAdjustmentKW' => 'G' . ($this->startingCell+6),
            'OtherTransmissionCostAdjustmentKWH' => 'G' . ($this->startingCell+7),
            'OtherSystemLossCostAdjustment' => 'G' . ($this->startingCell+8),
            'DistributionDemandCharge' => 'G' . ($this->startingCell+10),
            'DistributionSystemCharge' => 'G' . ($this->startingCell+11),
            'SupplyRetailCustomerCharge' => 'G' . ($this->startingCell+12),
            'SupplySystemCharge' => 'G' . ($this->startingCell+13),
            'MeteringRetailCustomerCharge' => 'G' . ($this->startingCell+14),
            'MeteringSystemCharge' => 'G' . ($this->startingCell+15),
            'RFSC' => 'G' . ($this->startingCell+17),
            'LifelineRate' => 'G' . ($this->startingCell+19),
            'InterClassCrossSubsidyCharge' => 'G' . ($this->startingCell+20),
            'PPARefund' => 'G' . ($this->startingCell+21),
            'SeniorCitizenSubsidy' => 'G' . ($this->startingCell+22),
            'OtherLifelineRateCostAdjustment' => 'G' . ($this->startingCell+24),
            'SeniorCitizenDiscountAndSubsidyAdjustment' => 'G' . ($this->startingCell+25),
            'MissionaryElectrificationCharge' => 'G' . ($this->startingCell+27),
            'EnvironmentalCharge' => 'G' . ($this->startingCell+28),
            'StrandedContractCosts' => 'G' . ($this->startingCell+29),
            'NPCStrandedDebt' => 'G' . ($this->startingCell+30),
            'FeedInTariffAllowance' => 'G' . ($this->startingCell+31),
            'MissionaryElectrificationREDCI' => 'G' . ($this->startingCell+32),
            'GenerationVAT' => 'G' . ($this->startingCell+37),
            'TransmissionVAT' => 'G' . ($this->startingCell+38),
            'SystemLossVAT' => 'G' . ($this->startingCell+39),
            'DistributionVAT' => 'G' . ($this->startingCell+40),
            'FranchiseTax' => 'G' . ($this->startingCell+41),
            'BusinessTax' => 'G' . ($this->startingCell+42),
            'RealPropertyTax' => 'G' . ($this->startingCell+43),
            'TotalRateVATExcluded' => 'G' . ($this->startingCell+33),
            'TotalRateVATExcludedWithAdjustments' => 'G' . ($this->startingCell+35),
            'TotalRateVATIncluded' => 'G' . ($this->startingCell+45),
        ];
    }
    
    public function model(array $row)
    {
        return new Rates([
            'id' => IDGenerator::generateIDandRandString(),
            'RateFor' => $this->district,
            'ServicePeriod' => $this->servicePeriod,
            'UserId' => $this->userId,
            'ConsumerType' => 'IRRIGATION/WATER SYSTEMS',
            'GenerationSystemCharge' => round(floatval($row['GenerationSystemCharge']), 4),
            'TransmissionDeliveryChargeKW' => round(floatval($row['TransmissionDeliveryChargeKW']), 4),
            'TransmissionDeliveryChargeKWH' => round(floatval($row['TransmissionDeliveryChargeKWH']), 4),
            'SystemLossCharge' => round(floatval($row['SystemLossCharge']), 4),
            'OtherGenerationRateAdjustment' => round(floatval($row['OtherGenerationRateAdjustment']), 4),
            'OtherTransmissionCostAdjustmentKW' => round(floatval($row['OtherTransmissionCostAdjustmentKW']), 4),
            'OtherTransmissionCostAdjustmentKWH' => round(floatval($row['OtherTransmissionCostAdjustmentKWH']), 4),
            'OtherSystemLossCostAdjustment' => round(floatval($row['OtherSystemLossCostAdjustment']), 4),
            'DistributionDemandCharge' => round(floatval($row['DistributionDemandCharge']), 4),
            'DistributionSystemCharge' => round(floatval($row['DistributionSystemCharge']), 4),
            'SupplyRetailCustomerCharge' => round(floatval($row['SupplyRetailCustomerCharge']), 4),
            'SupplySystemCharge' => round(floatval($row['SupplySystemCharge']), 4),
            'MeteringRetailCustomerCharge' => round(floatval($row['MeteringRetailCustomerCharge']), 4),
            'MeteringSystemCharge' => round(floatval($row['MeteringSystemCharge']), 4),
            'RFSC' => round(floatval($row['RFSC']), 4),
            'LifelineRate' => round(floatval($row['LifelineRate']), 4),
            'InterClassCrossSubsidyCharge' => round(floatval($row['InterClassCrossSubsidyCharge']), 4),
            'PPARefund' => round(floatval($row['PPARefund']), 4),
            'SeniorCitizenSubsidy' => round(floatval($row['SeniorCitizenSubsidy']), 4),
            'OtherLifelineRateCostAdjustment' => round(floatval($row['OtherLifelineRateCostAdjustment']), 4),
            'SeniorCitizenDiscountAndSubsidyAdjustment' => round(floatval($row['SeniorCitizenDiscountAndSubsidyAdjustment']), 4),
            'MissionaryElectrificationCharge' => round(floatval($row['MissionaryElectrificationCharge']), 4),
            'EnvironmentalCharge' => round(floatval($row['EnvironmentalCharge']), 4),
            'StrandedContractCosts' => round(floatval($row['StrandedContractCosts']), 4),
            'NPCStrandedDebt' => round(floatval($row['NPCStrandedDebt']), 4),
            'FeedInTariffAllowance' => round(floatval($row['FeedInTariffAllowance']), 4),
            'MissionaryElectrificationREDCI' => round(floatval($row['MissionaryElectrificationREDCI']), 4),
            'GenerationVAT' => round(floatval($row['GenerationVAT']), 4),
            'TransmissionVAT' => round(floatval($row['TransmissionVAT']), 4),
            'SystemLossVAT' => round(floatval($row['SystemLossVAT']), 4),
            'DistributionVAT' => round(floatval($row['DistributionVAT']), 4),
            'RealPropertyTax' => round(floatval($row['RealPropertyTax']), 4),
            'FranchiseTax' => round(floatval($row['FranchiseTax']), 4),
            'BusinessTax' => round(floatval($row['BusinessTax']), 4),
            'TotalRateVATExcluded' => round(floatval($row['TotalRateVATExcluded']), 4),
            'TotalRateVATIncluded' => round(floatval($row['TotalRateVATIncluded']), 4),
            'TotalRateVATExcludedWithAdjustments' => round(floatval($row['TotalRateVATExcludedWithAdjustments']), 4),
            'AreaCode' => $this->areaCode,
        ]);
    }
}

