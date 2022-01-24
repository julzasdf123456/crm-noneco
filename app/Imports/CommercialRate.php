<?php
namespace App\Imports;

use App\Models\Rates;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use App\Models\IDGenerator;

class CommercialRate implements WithMappedCells, WithCalculatedFormulas, ToModel 
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
            'GenerationSystemCharge' => 'E11',
            'TransmissionDeliveryChargeKW' => 'E12',
            'TransmissionDeliveryChargeKWH' => 'E13',
            'SystemLossCharge' => 'E14',
            'DistributionDemandCharge' => 'E16',
            'DistributionSystemCharge' => 'E17',
            'SupplyRetailCustomerCharge' => 'E18',
            'SupplySystemCharge' => 'E19',
            'MeteringRetailCustomerCharge' => 'E20',
            'MeteringSystemCharge' => 'E21',
            'RFSC' => 'E22',
            'LifelineRate' => 'E24',
            'InterClassCrossSubsidyCharge' => 'E25',
            'PPARefund' => 'E26',
            'SeniorCitizenSubsidy' => 'E27',
            'MissionaryElectrificationCharge' => 'E29',
            'EnvironmentalCharge' => 'E30',
            'StrandedContractCosts' => 'E31',
            'NPCStrandedDebt' => 'E32',
            'FeedInTariffAllowance' => 'E33',
            'MissionaryElectrificationREDCI' => 'E34',
            'GenerationVAT' => 'E37',
            'TransmissionVAT' => 'E38',
            'SystemLossVAT' => 'E39',
            'DistributionVAT' => 'E40',
            'RealPropertyTax' => 'E41',
            'TotalRateVATExcluded' => 'E35',
            'TotalRateVATIncluded' => 'E43',
        ];
    }
    
    public function model(array $row)
    {
        return new Rates([
            'id' => IDGenerator::generateIDandRandString(),
            'RateFor' => $this->district,
            'ServicePeriod' => $this->servicePeriod,
            'UserId' => $this->userId,
            'ConsumerType' => 'COMMERCIAL',
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

