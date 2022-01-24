<?php
namespace App\Imports;

use App\Models\Rates;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use App\Models\IDGenerator;

class CommercialHVRate implements WithMappedCells, WithCalculatedFormulas, ToModel 
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
            'GenerationSystemCharge' => 'K11',
            'TransmissionDeliveryChargeKW' => 'K12',
            'TransmissionDeliveryChargeKWH' => 'K13',
            'SystemLossCharge' => 'K14',
            'DistributionDemandCharge' => 'K16',
            'DistributionSystemCharge' => 'K17',
            'SupplyRetailCustomerCharge' => 'K18',
            'SupplySystemCharge' => 'K19',
            'MeteringRetailCustomerCharge' => 'K20',
            'MeteringSystemCharge' => 'K21',
            'RFSC' => 'K22',
            'LifelineRate' => 'K24',
            'InterClassCrossSubsidyCharge' => 'K25',
            'PPARefund' => 'K26',
            'SeniorCitizenSubsidy' => 'K27',
            'MissionaryElectrificationCharge' => 'K29',
            'EnvironmentalCharge' => 'K30',
            'StrandedContractCosts' => 'K31',
            'NPCStrandedDebt' => 'K32',
            'FeedInTariffAllowance' => 'K33',
            'MissionaryElectrificationREDCI' => 'K34',
            'GenerationVAT' => 'K37',
            'TransmissionVAT' => 'K38',
            'SystemLossVAT' => 'K39',
            'DistributionVAT' => 'K40',
            'RealPropertyTax' => 'K41',
            'TotalRateVATExcluded' => 'K35',
            'TotalRateVATIncluded' => 'K43',
        ];
    }
    
    public function model(array $row)
    {
        return new Rates([
            'id' => IDGenerator::generateIDandRandString(),
            'RateFor' => $this->district,
            'ServicePeriod' => $this->servicePeriod,
            'UserId' => $this->userId,
            'ConsumerType' => 'COMMERCIAL HIGH VOLTAGE',
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

