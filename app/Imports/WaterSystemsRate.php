<?php
namespace App\Imports;

use App\Models\Rates;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use App\Models\IDGenerator;

class WaterSystemsRate implements WithMappedCells, WithCalculatedFormulas, ToModel 
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
            'GenerationSystemCharge' => 'G11',
            'TransmissionDeliveryChargeKW' => 'G12',
            'TransmissionDeliveryChargeKWH' => 'G13',
            'SystemLossCharge' => 'G14',
            'DistributionDemandCharge' => 'G16',
            'DistributionSystemCharge' => 'G17',
            'SupplyRetailCustomerCharge' => 'G18',
            'SupplySystemCharge' => 'G19',
            'MeteringRetailCustomerCharge' => 'G20',
            'MeteringSystemCharge' => 'G21',
            'RFSC' => 'G22',
            'LifelineRate' => 'G24',
            'InterClassCrossSubsidyCharge' => 'G25',
            'PPARefund' => 'G26',
            'SeniorCitizenSubsidy' => 'G27',
            'MissionaryElectrificationCharge' => 'G29',
            'EnvironmentalCharge' => 'G30',
            'StrandedContractCosts' => 'G31',
            'NPCStrandedDebt' => 'G32',
            'FeedInTariffAllowance' => 'G33',
            'MissionaryElectrificationREDCI' => 'G34',
            'GenerationVAT' => 'G37',
            'TransmissionVAT' => 'G38',
            'SystemLossVAT' => 'G39',
            'DistributionVAT' => 'G40',
            'RealPropertyTax' => 'G41',
            'TotalRateVATExcluded' => 'G35',
            'TotalRateVATIncluded' => 'G43',
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

