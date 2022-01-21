<?php
namespace App\Imports;

use App\Models\Rates;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use App\Models\IDGenerator;

class IndustrialHVRate implements WithMappedCells, WithCalculatedFormulas, ToModel 
{

    private $servicePeriod, $userId, $district;

    public function __construct($servicePeriod, $userId, $district)
    {
        $this->servicePeriod = $servicePeriod;
        $this->userId = $userId;
        $this->district = $district;
    }

    public function mapping(): array
    {
        return [
            'GenerationSystemCharge' => 'J11',
            'TransmissionDeliveryChargeKW' => 'J12',
            'TransmissionDeliveryChargeKWH' => 'J13',
            'SystemLossCharge' => 'J14',
            'DistributionDemandCharge' => 'J16',
            'DistributionSystemCharge' => 'J17',
            'SupplyRetailCustomerCharge' => 'J18',
            'SupplySystemCharge' => 'J19',
            'MeteringRetailCustomerCharge' => 'J20',
            'MeteringSystemCharge' => 'J21',
            'RFSC' => 'J22',
            'LifelineRate' => 'J24',
            'InterClassCrossSubsidyCharge' => 'J25',
            'PPARefund' => 'J26',
            'SeniorCitizenSubsidy' => 'J27',
            'MissionaryElectrificationCharge' => 'J29',
            'EnvironmentalCharge' => 'J30',
            'StrandedContractCosts' => 'J31',
            'NPCStrandedDebt' => 'J32',
            'FeedInTariffAllowance' => 'J33',
            'MissionaryElectrificationREDCI' => 'J34',
            'GenerationVAT' => 'J37',
            'TransmissionVAT' => 'J38',
            'SystemLossVAT' => 'J39',
            'DistributionVAT' => 'J40',
            'TotalRateVATExcluded' => 'J35',
            'TotalRateVATIncluded' => 'J42',
        ];
    }
    
    public function model(array $row)
    {
        return new Rates([
            'id' => IDGenerator::generateIDandRandString(),
            'RateFor' => $this->district,
            'ServicePeriod' => $this->servicePeriod,
            'UserId' => $this->userId,
            'ConsumerType' => 'INDUSTRIAL HIGH VOLTAGE',
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
            'TotalRateVATExcluded' => $row['TotalRateVATExcluded'],
            'TotalRateVATIncluded' => $row['TotalRateVATIncluded'],
        ]);
    }
}

