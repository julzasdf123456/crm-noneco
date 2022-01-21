<?php
namespace App\Imports;

use App\Models\Rates;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use App\Models\IDGenerator;

class PublicBuildingHVRate implements WithMappedCells, WithCalculatedFormulas, ToModel 
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
            'GenerationSystemCharge' => 'L11',
            'TransmissionDeliveryChargeKW' => 'L12',
            'TransmissionDeliveryChargeKWH' => 'L13',
            'SystemLossCharge' => 'L14',
            'DistributionDemandCharge' => 'L16',
            'DistributionSystemCharge' => 'L17',
            'SupplyRetailCustomerCharge' => 'L18',
            'SupplySystemCharge' => 'L19',
            'MeteringRetailCustomerCharge' => 'L20',
            'MeteringSystemCharge' => 'L21',
            'RFSC' => 'L22',
            'LifelineRate' => 'L24',
            'InterClassCrossSubsidyCharge' => 'L25',
            'PPARefund' => 'L26',
            'SeniorCitizenSubsidy' => 'L27',
            'MissionaryElectrificationCharge' => 'L29',
            'EnvironmentalCharge' => 'L30',
            'StrandedContractCosts' => 'L31',
            'NPCStrandedDebt' => 'L32',
            'FeedInTariffAllowance' => 'L33',
            'MissionaryElectrificationREDCI' => 'L34',
            'GenerationVAT' => 'L37',
            'TransmissionVAT' => 'L38',
            'SystemLossVAT' => 'L39',
            'DistributionVAT' => 'L40',
            'TotalRateVATExcluded' => 'L35',
            'TotalRateVATIncluded' => 'L42',
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

