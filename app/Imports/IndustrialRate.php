<?php
namespace App\Imports;

use App\Models\Rates;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use App\Models\IDGenerator;

class IndustrialRate implements WithMappedCells, WithCalculatedFormulas, ToModel 
{

    private $servicePeriod, $userId, $district, $areaCode;

    public function __construct($servicePeriod, $userId, $district, $areaCode)
    {
        $this->servicePeriod = $servicePeriod;
        $this->userId = $userId;
        $this->district = $district;
        $this->areaCode = $areaCode;
    }

    public function mapping(): array
    {
        return [
            'GenerationSystemCharge' => 'F11',
            'TransmissionDeliveryChargeKW' => 'F12',
            'TransmissionDeliveryChargeKWH' => 'F13',
            'SystemLossCharge' => 'F14',
            'DistributionDemandCharge' => 'F16',
            'DistributionSystemCharge' => 'F17',
            'SupplyRetailCustomerCharge' => 'F18',
            'SupplySystemCharge' => 'F19',
            'MeteringRetailCustomerCharge' => 'F20',
            'MeteringSystemCharge' => 'F21',
            'RFSC' => 'F22',
            'LifelineRate' => 'F24',
            'InterClassCrossSubsidyCharge' => 'F25',
            'PPARefund' => 'F26',
            'SeniorCitizenSubsidy' => 'F27',
            'MissionaryElectrificationCharge' => 'F29',
            'EnvironmentalCharge' => 'F30',
            'StrandedContractCosts' => 'F31',
            'NPCStrandedDebt' => 'F32',
            'FeedInTariffAllowance' => 'F33',
            'MissionaryElectrificationREDCI' => 'F34',
            'GenerationVAT' => 'F37',
            'TransmissionVAT' => 'F38',
            'SystemLossVAT' => 'F39',
            'DistributionVAT' => 'F40',
            'RealPropertyTax' => 'F41',
            'TotalRateVATExcluded' => 'F35',
            'TotalRateVATIncluded' => 'F43',
        ];
    }
    
    public function model(array $row)
    {
        return new Rates([
            'id' => IDGenerator::generateIDandRandString(),
            'RateFor' => $this->district,
            'ServicePeriod' => $this->servicePeriod,
            'UserId' => $this->userId,
            'ConsumerType' => 'INDUSTRIAL',
            'GenerationSystemCharge' => $row['GenerationSystemCharge'],
            'TransmissionDeliveryChargeKW' => $row['TransmissionDeliveryChargeKW'],
            'TransmissionDeliveryChargeKWH' => $row['TransmissionDeliveryChargeKWH'],
            'SystemLossCharge' => $row['SystemLossCharge'],
            'DistributionDemandCharge' => $row['DistributionDemandCharge'],
            'DistributionSystemCharge' => $row['DistributionSystemCharge'],
            'SupplyRetailCustomerCharge' => $row['SupplyRetailCustomerCharge'],
            'SupplySystemCharge' => $row['SupplySystemCharge'],
            'MeteringRetailCustomerCharge' => $row['MeteringRetailCustomerCharge'],
            'MeteringSystemCharge' => $row['MeteringSystemCharge'],
            'RFSC' => $row['RFSC'],
            'LifelineRate' => $row['LifelineRate'],
            'InterClassCrossSubsidyCharge' => $row['InterClassCrossSubsidyCharge'],
            'PPARefund' => $row['PPARefund'],
            'SeniorCitizenSubsidy' => $row['SeniorCitizenSubsidy'],
            'MissionaryElectrificationCharge' => $row['MissionaryElectrificationCharge'],
            'EnvironmentalCharge' => $row['EnvironmentalCharge'],
            'StrandedContractCosts' => $row['StrandedContractCosts'],
            'NPCStrandedDebt' => $row['NPCStrandedDebt'],
            'FeedInTariffAllowance' => $row['FeedInTariffAllowance'],
            'MissionaryElectrificationREDCI' => $row['MissionaryElectrificationREDCI'],
            'GenerationVAT' => $row['GenerationVAT'],
            'TransmissionVAT' => $row['TransmissionVAT'],
            'SystemLossVAT' => $row['SystemLossVAT'],
            'DistributionVAT' => $row['DistributionVAT'],
            'RealPropertyTax' => $row['RealPropertyTax'],
            'TotalRateVATExcluded' => $row['TotalRateVATExcluded'],
            'TotalRateVATIncluded' => $row['TotalRateVATIncluded'],
            'AreaCode' => $this->areaCode,
        ]);
    }
}

