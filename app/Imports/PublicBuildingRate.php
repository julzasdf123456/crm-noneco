<?php
namespace App\Imports;

use App\Models\Rates;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use App\Models\IDGenerator;

class PublicBuildingRate implements WithMappedCells, WithCalculatedFormulas, ToModel 
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
            'GenerationSystemCharge' => 'H11',
            'TransmissionDeliveryChargeKW' => 'H12',
            'TransmissionDeliveryChargeKWH' => 'H13',
            'SystemLossCharge' => 'H14',
            'DistributionDemandCharge' => 'H16',
            'DistributionSystemCharge' => 'H17',
            'SupplyRetailCustomerCharge' => 'H18',
            'SupplySystemCharge' => 'H19',
            'MeteringRetailCustomerCharge' => 'H20',
            'MeteringSystemCharge' => 'H21',
            'RFSC' => 'H22',
            'LifelineRate' => 'H24',
            'InterClassCrossSubsidyCharge' => 'H25',
            'PPARefund' => 'H26',
            'SeniorCitizenSubsidy' => 'H27',
            'MissionaryElectrificationCharge' => 'H29',
            'EnvironmentalCharge' => 'H30',
            'StrandedContractCosts' => 'H31',
            'NPCStrandedDebt' => 'H32',
            'FeedInTariffAllowance' => 'H33',
            'MissionaryElectrificationREDCI' => 'H34',
            'GenerationVAT' => 'H37',
            'TransmissionVAT' => 'H38',
            'SystemLossVAT' => 'H39',
            'DistributionVAT' => 'H40',
            'TotalRateVATExcluded' => 'H35',
            'TotalRateVATIncluded' => 'H42',
        ];
    }
    
    public function model(array $row)
    {
        return new Rates([
            'id' => IDGenerator::generateIDandRandString(),
            'RateFor' => $this->district,
            'ServicePeriod' => $this->servicePeriod,
            'UserId' => $this->userId,
            'ConsumerType' => 'PUBLIC BUILDING',
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

