<?php
namespace App\Imports;

use App\Models\Rates;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use App\Models\IDGenerator;

class PublicBuildingHVRate implements WithMappedCells, WithCalculatedFormulas, ToModel 
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
            'GenerationSystemCharge' => 'L' . $this->startingCell,
            'TransmissionDeliveryChargeKW' => 'L' . ($this->startingCell+1),
            'TransmissionDeliveryChargeKWH' => 'L' . ($this->startingCell+2),
            'SystemLossCharge' => 'L' . ($this->startingCell+3),
            'OtherGenerationRateAdjustment' => 'L' . ($this->startingCell+5),
            'OtherTransmissionCostAdjustmentKW' => 'L' . ($this->startingCell+6),
            'OtherTransmissionCostAdjustmentKWH' => 'L' . ($this->startingCell+7),
            'OtherSystemLossCostAdjustment' => 'L' . ($this->startingCell+8),
            'DistributionDemandCharge' => 'L' . ($this->startingCell+10),
            'DistributionSystemCharge' => 'L' . ($this->startingCell+11),
            'SupplyRetailCustomerCharge' => 'L' . ($this->startingCell+12),
            'SupplySystemCharge' => 'L' . ($this->startingCell+13),
            'MeteringRetailCustomerCharge' => 'L' . ($this->startingCell+14),
            'MeteringSystemCharge' => 'L' . ($this->startingCell+15),
            'RFSC' => 'L' . ($this->startingCell+17),
            'LifelineRate' => 'L' . ($this->startingCell+19),
            'InterClassCrossSubsidyCharge' => 'L' . ($this->startingCell+20),
            'PPARefund' => 'L' . ($this->startingCell+21),
            'SeniorCitizenSubsidy' => 'L' . ($this->startingCell+22),
            'OtherLifelineRateCostAdjustment' => 'L' . ($this->startingCell+24),
            'SeniorCitizenDiscountAndSubsidyAdjustment' => 'L' . ($this->startingCell+25),
            'MissionaryElectrificationCharge' => 'L' . ($this->startingCell+27),
            'EnvironmentalCharge' => 'L' . ($this->startingCell+28),
            'StrandedContractCosts' => 'L' . ($this->startingCell+29),
            'NPCStrandedDebt' => 'L' . ($this->startingCell+30),
            'FeedInTariffAllowance' => 'L' . ($this->startingCell+31),
            'MissionaryElectrificationREDCI' => 'L' . ($this->startingCell+32),
            'GenerationVAT' => 'L' . ($this->startingCell+37),
            'TransmissionVAT' => 'L' . ($this->startingCell+38),
            'SystemLossVAT' => 'L' . ($this->startingCell+39),
            'DistributionVAT' => 'L' . ($this->startingCell+40),
            'FranchiseTax' => 'L' . ($this->startingCell+41),
            'BusinessTax' => 'L' . ($this->startingCell+42),
            'RealPropertyTax' => 'L' . ($this->startingCell+43),
            'TotalRateVATExcluded' => 'L' . ($this->startingCell+33),
            'TotalRateVATExcludedWithAdjustments' => 'L' . ($this->startingCell+35),
            'TotalRateVATIncluded' => 'L' . ($this->startingCell+45),
        ];
    }
    
    public function model(array $row)
    {
        return new Rates([
            'id' => IDGenerator::generateIDandRandString(),
            'RateFor' => $this->district,
            'ServicePeriod' => $this->servicePeriod,
            'UserId' => $this->userId,
            'ConsumerType' => 'PUBLIC BUILDING HIGH VOLTAGE',
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

