<?php
namespace App\Imports;

use App\Models\Rates;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use App\Models\IDGenerator;

class StreetlightsRate implements WithMappedCells, WithCalculatedFormulas, ToModel 
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
            'GenerationSystemCharge' => 'I11',
            'TransmissionDeliveryChargeKW' => 'I12',
            'TransmissionDeliveryChargeKWH' => 'I13',
            'SystemLossCharge' => 'I14',
            'DistributionDemandCharge' => 'I16',
            'DistributionSystemCharge' => 'I17',
            'SupplyRetailCustomerCharge' => 'I18',
            'SupplySystemCharge' => 'I19',
            'MeteringRetailCustomerCharge' => 'I20',
            'MeteringSystemCharge' => 'I21',
            'RFSC' => 'I22',
            'LifelineRate' => 'I24',
            'InterClassCrossSubsidyCharge' => 'I25',
            'PPARefund' => 'I26',
            'SeniorCitizenSubsidy' => 'I27',
            'MissionaryElectrificationCharge' => 'I29',
            'EnvironmentalCharge' => 'I30',
            'StrandedContractCosts' => 'I31',
            'NPCStrandedDebt' => 'I32',
            'FeedInTariffAllowance' => 'I33',
            'MissionaryElectrificationREDCI' => 'I34',
            'GenerationVAT' => 'I37',
            'TransmissionVAT' => 'I38',
            'SystemLossVAT' => 'I39',
            'DistributionVAT' => 'I40',
            'RealPropertyTax' => 'I41',
            'TotalRateVATExcluded' => 'I35',
            'TotalRateVATIncluded' => 'I43',
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

