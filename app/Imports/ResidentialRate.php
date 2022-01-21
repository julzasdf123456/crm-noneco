<?php
namespace App\Imports;

use App\Models\Rates;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use App\Models\IDGenerator;

class ResidentialRate implements WithMappedCells, WithCalculatedFormulas, ToModel 
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
            'ConsumerType' => 'D8',
            'GenerationSystemCharge' => 'D11',
            'TransmissionDeliveryChargeKW' => 'D12',
            'TransmissionDeliveryChargeKWH' => 'D13',
            'SystemLossCharge' => 'D14',
            'DistributionDemandCharge' => 'D16',
            'DistributionSystemCharge' => 'D17',
            'SupplyRetailCustomerCharge' => 'D18',
            'SupplySystemCharge' => 'D19',
            'MeteringRetailCustomerCharge' => 'D20',
            'MeteringSystemCharge' => 'D21',
            'RFSC' => 'D22',
            'LifelineRate' => 'D24',
            'InterClassCrossSubsidyCharge' => 'D25',
            'PPARefund' => 'D26',
            'SeniorCitizenSubsidy' => 'D27',
            'MissionaryElectrificationCharge' => 'D29',
            'EnvironmentalCharge' => 'D30',
            'StrandedContractCosts' => 'D31',
            'NPCStrandedDebt' => 'D32',
            'FeedInTariffAllowance' => 'D33',
            'MissionaryElectrificationREDCI' => 'D34',
            'GenerationVAT' => 'D37',
            'TransmissionVAT' => 'D38',
            'SystemLossVAT' => 'D39',
            'DistributionVAT' => 'D40',
            'TotalRateVATExcluded' => 'D35',
            'TotalRateVATIncluded' => 'D42',
        ];
    }
    
    public function model(array $row)
    {
        return new Rates([
            'id' => IDGenerator::generateIDandRandString(),
            'RateFor' => $this->district,
            'ServicePeriod' => $this->servicePeriod,
            'UserId' => $this->userId,
            'ConsumerType' => $row['ConsumerType'],
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

