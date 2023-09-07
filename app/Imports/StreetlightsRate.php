<?php
namespace App\Imports;

use App\Models\Rates;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use App\Models\IDGenerator;

class StreetlightsRate implements WithMappedCells, WithCalculatedFormulas, ToModel 
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
            'GenerationSystemCharge' => 'I' . $this->startingCell,
            'TransmissionDeliveryChargeKW' => 'I' . ($this->startingCell+1),
            'TransmissionDeliveryChargeKWH' => 'I' . ($this->startingCell+2),
            'SystemLossCharge' => 'I' . ($this->startingCell+3),
            'OtherGenerationRateAdjustment' => 'I' . ($this->startingCell+5),
            'OtherTransmissionCostAdjustmentKW' => 'I' . ($this->startingCell+6),
            'OtherTransmissionCostAdjustmentKWH' => 'I' . ($this->startingCell+7),
            'OtherSystemLossCostAdjustment' => 'I' . ($this->startingCell+8),
            'DistributionDemandCharge' => 'I' . ($this->startingCell+10),
            'DistributionSystemCharge' => 'I' . ($this->startingCell+11),
            'SupplyRetailCustomerCharge' => 'I' . ($this->startingCell+12),
            'SupplySystemCharge' => 'I' . ($this->startingCell+13),
            'MeteringRetailCustomerCharge' => 'I' . ($this->startingCell+14),
            'MeteringSystemCharge' => 'I' . ($this->startingCell+15),
            'RFSC' => 'I' . ($this->startingCell+17),
            'LifelineRate' => 'I' . ($this->startingCell+19),
            'InterClassCrossSubsidyCharge' => 'I' . ($this->startingCell+20),
            'PPARefund' => 'I' . ($this->startingCell+21),
            'SeniorCitizenSubsidy' => 'I' . ($this->startingCell+22),
            'OtherLifelineRateCostAdjustment' => 'I' . ($this->startingCell+24),
            'SeniorCitizenDiscountAndSubsidyAdjustment' => 'I' . ($this->startingCell+25),
            'MissionaryElectrificationCharge' => 'I' . ($this->startingCell+27),
            'EnvironmentalCharge' => 'I' . ($this->startingCell+28),
            'StrandedContractCosts' => 'I' . ($this->startingCell+29),
            'NPCStrandedDebt' => 'I' . ($this->startingCell+30),
            'FeedInTariffAllowance' => 'I' . ($this->startingCell+31),
            'MissionaryElectrificationREDCI' => 'I' . ($this->startingCell+32),
            'GenerationVAT' => 'I' . ($this->startingCell+37),
            'TransmissionVAT' => 'I' . ($this->startingCell+38),
            'SystemLossVAT' => 'I' . ($this->startingCell+39),
            'DistributionVAT' => 'I' . ($this->startingCell+40),
            'FranchiseTax' => 'I' . ($this->startingCell+41),
            'BusinessTax' => 'I' . ($this->startingCell+42),
            'RealPropertyTax' => 'I' . ($this->startingCell+43),
            'TotalRateVATExcluded' => 'I' . ($this->startingCell+33),
            'TotalRateVATExcludedWithAdjustments' => 'I' . ($this->startingCell+35),
            'TotalRateVATIncluded' => 'I' . ($this->startingCell+45),
        ];
    }
    
    public function model(array $row)
    {
        return new Rates([
            'id' => IDGenerator::generateIDandRandString(),
            'RateFor' => $this->district,
            'ServicePeriod' => $this->servicePeriod,
            'UserId' => $this->userId,
            'ConsumerType' => 'STREET LIGHTS',
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

