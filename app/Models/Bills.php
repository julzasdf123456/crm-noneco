<?php

namespace App\Models;

use Eloquent as Model;
use App\Models\Rates;
use App\Models\Bills;
use App\Models\IDGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Bills
 * @package App\Models
 * @version January 27, 2022, 2:09 pm PST
 *
 * @property string $BillNumber
 * @property string $AccountNumber
 * @property string $ServicePeriod
 * @property string $Multiplier
 * @property string $Coreloss
 * @property string $KwhUsed
 * @property string $PreviousKwh
 * @property string $PresentKwh
 * @property string $DemandPreviousKwh
 * @property string $DemandPresentKwh
 * @property string $AdditionalKwh
 * @property string $AdditionalDemandKwh
 * @property string $KwhAmount
 * @property string $EffectiveRate
 * @property string $AdditionalCharges
 * @property string $Deductions
 * @property string $NetAmount
 * @property string $BillingDate
 * @property string $ServiceDateFrom
 * @property string $ServiceDateTo
 * @property string $DueDate
 * @property string $MeterNumber
 * @property string $ConsumerType
 * @property string $BillType
 * @property string $GenerationSystemCharge
 * @property string $TransmissionDeliveryChargeKW
 * @property string $TransmissionDeliveryChargeKWH
 * @property string $SystemLossCharge
 * @property string $DistributionDemandCharge
 * @property string $DistributionSystemCharge
 * @property string $SupplyRetailCustomerCharge
 * @property string $SupplySystemCharge
 * @property string $MeteringRetailCustomerCharge
 * @property string $MeteringSystemCharge
 * @property string $RFSC
 * @property string $LifelineRate
 * @property string $InterClassCrossSubsidyCharge
 * @property string $PPARefund
 * @property string $SeniorCitizenSubsidy
 * @property string $MissionaryElectrificationCharge
 * @property string $EnvironmentalCharge
 * @property string $StrandedContractCosts
 * @property string $NPCStrandedDebt
 * @property string $FeedInTariffAllowance
 * @property string $MissionaryElectrificationREDCI
 * @property string $GenerationVAT
 * @property string $TransmissionVAT
 * @property string $SystemLossVAT
 * @property string $DistributionVAT
 * @property string $RealPropertyTax
 * @property string $Notes
 * @property string $UserId
 * @property string $BilledFrom
 */
class Bills extends Model
{
    // use SoftDeletes;

    use HasFactory;

    public $table = 'Billing_Bills';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];

    protected $primaryKey = 'id';

    public $incrementing = false;

    public $fillable = [
        'id',
        'BillNumber',
        'AccountNumber',
        'ServicePeriod',
        'Multiplier',
        'Coreloss',
        'KwhUsed',
        'PreviousKwh',
        'PresentKwh',
        'DemandPreviousKwh',
        'DemandPresentKwh',
        'AdditionalKwh',
        'AdditionalDemandKwh',
        'KwhAmount',
        'EffectiveRate',
        'AdditionalCharges',
        'Deductions',
        'NetAmount',
        'BillingDate',
        'ServiceDateFrom',
        'ServiceDateTo',
        'DueDate',
        'MeterNumber',
        'ConsumerType',
        'BillType',
        'GenerationSystemCharge',
        'TransmissionDeliveryChargeKW',
        'TransmissionDeliveryChargeKWH',
        'SystemLossCharge',
        'DistributionDemandCharge',
        'DistributionSystemCharge',
        'SupplyRetailCustomerCharge',
        'SupplySystemCharge',
        'MeteringRetailCustomerCharge',
        'MeteringSystemCharge',
        'RFSC',
        'LifelineRate',
        'InterClassCrossSubsidyCharge',
        'PPARefund',
        'SeniorCitizenSubsidy',
        'MissionaryElectrificationCharge',
        'EnvironmentalCharge',
        'StrandedContractCosts',
        'NPCStrandedDebt',
        'FeedInTariffAllowance',
        'MissionaryElectrificationREDCI',
        'GenerationVAT',
        'TransmissionVAT',
        'SystemLossVAT',
        'DistributionVAT',
        'RealPropertyTax',
        'Notes',
        'UserId',
        'BilledFrom',
        'AveragedCount',
        'MergedToCollectible',
        'OtherGenerationRateAdjustment',
        'OtherTransmissionCostAdjustmentKW',
        'OtherTransmissionCostAdjustmentKWH',
        'OtherSystemLossCostAdjustment',
        'OtherLifelineRateCostAdjustment',
        'SeniorCitizenDiscountAndSubsidyAdjustment',
        'FranchiseTax',
        'BusinessTax',
        'AdjustmentType',
        'Form2307Amount',
        'DeductedDeposit',
        'ExcessDeposit',
        'IsUnlockedForPayment',
        'UnlockedBy',
        'Evat2Percent',
        'Evat5Percent'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'BillNumber' => 'string',
        'AccountNumber' => 'string',
        'ServicePeriod' => 'string',
        'Multiplier' => 'string',
        'Coreloss' => 'string',
        'KwhUsed' => 'string',
        'PreviousKwh' => 'string',
        'PresentKwh' => 'string',
        'DemandPreviousKwh' => 'string',
        'DemandPresentKwh' => 'string',
        'AdditionalKwh' => 'string',
        'AdditionalDemandKwh' => 'string',
        'KwhAmount' => 'string',
        'EffectiveRate' => 'string',
        'AdditionalCharges' => 'string',
        'Deductions' => 'string',
        'NetAmount' => 'string',
        'BillingDate' => 'string',
        'ServiceDateFrom' => 'string',
        'ServiceDateTo' => 'string',
        'DueDate' => 'string',
        'MeterNumber' => 'string',
        'ConsumerType' => 'string',
        'BillType' => 'string',
        'GenerationSystemCharge' => 'string',
        'TransmissionDeliveryChargeKW' => 'string',
        'TransmissionDeliveryChargeKWH' => 'string',
        'SystemLossCharge' => 'string',
        'DistributionDemandCharge' => 'string',
        'DistributionSystemCharge' => 'string',
        'SupplyRetailCustomerCharge' => 'string',
        'SupplySystemCharge' => 'string',
        'MeteringRetailCustomerCharge' => 'string',
        'MeteringSystemCharge' => 'string',
        'RFSC' => 'string',
        'LifelineRate' => 'string',
        'InterClassCrossSubsidyCharge' => 'string',
        'PPARefund' => 'string',
        'SeniorCitizenSubsidy' => 'string',
        'MissionaryElectrificationCharge' => 'string',
        'EnvironmentalCharge' => 'string',
        'StrandedContractCosts' => 'string',
        'NPCStrandedDebt' => 'string',
        'FeedInTariffAllowance' => 'string',
        'MissionaryElectrificationREDCI' => 'string',
        'GenerationVAT' => 'string',
        'TransmissionVAT' => 'string',
        'SystemLossVAT' => 'string',
        'DistributionVAT' => 'string',
        'RealPropertyTax' => 'string',
        'Notes' => 'string',
        'UserId' => 'string',
        'BilledFrom' => 'string',
        'AveragedCount' => 'string',
        'MergedToCollectible' => 'string',
        'OtherGenerationRateAdjustment' => 'string',
        'OtherTransmissionCostAdjustmentKW' => 'string',
        'OtherTransmissionCostAdjustmentKWH' => 'string',
        'OtherSystemLossCostAdjustment' => 'string',
        'OtherLifelineRateCostAdjustment' => 'string',
        'SeniorCitizenDiscountAndSubsidyAdjustment' => 'string',
        'FranchiseTax' => 'string',
        'BusinessTax' => 'string',
        'AdjustmentType' => 'string',
        'Form2307Amount' => 'string',
        'DeductedDeposit' => 'string',
        'ExcessDeposit' => 'string',
        'IsUnlockedForPayment' => 'string',
        'UnlockedBy' => 'string',
        'Evat2Percent' => 'string',
        'Evat5Percent' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'id' => 'string',
        'BillNumber' => 'nullable|string|max:255',
        'AccountNumber' => 'nullable|string|max:255',
        'ServicePeriod' => 'nullable',
        'Multiplier' => 'nullable|string|max:255',
        'Coreloss' => 'nullable|string|max:255',
        'KwhUsed' => 'nullable|string|max:255',
        'PreviousKwh' => 'nullable|string|max:255',
        'PresentKwh' => 'nullable|string|max:255',
        'DemandPreviousKwh' => 'nullable|string|max:255',
        'DemandPresentKwh' => 'nullable|string|max:255',
        'AdditionalKwh' => 'nullable|string|max:255',
        'AdditionalDemandKwh' => 'nullable|string|max:255',
        'KwhAmount' => 'nullable|string|max:255',
        'EffectiveRate' => 'nullable|string|max:255',
        'AdditionalCharges' => 'nullable|string|max:255',
        'Deductions' => 'nullable|string|max:255',
        'NetAmount' => 'nullable|string|max:255',
        'BillingDate' => 'nullable',
        'ServiceDateFrom' => 'nullable',
        'ServiceDateTo' => 'nullable',
        'DueDate' => 'nullable',
        'MeterNumber' => 'nullable|string|max:255',
        'ConsumerType' => 'nullable|string|max:255',
        'BillType' => 'nullable|string|max:255',
        'GenerationSystemCharge' => 'nullable|string|max:20',
        'TransmissionDeliveryChargeKW' => 'nullable|string|max:20',
        'TransmissionDeliveryChargeKWH' => 'nullable|string|max:20',
        'SystemLossCharge' => 'nullable|string|max:20',
        'DistributionDemandCharge' => 'nullable|string|max:20',
        'DistributionSystemCharge' => 'nullable|string|max:20',
        'SupplyRetailCustomerCharge' => 'nullable|string|max:20',
        'SupplySystemCharge' => 'nullable|string|max:20',
        'MeteringRetailCustomerCharge' => 'nullable|string|max:20',
        'MeteringSystemCharge' => 'nullable|string|max:20',
        'RFSC' => 'nullable|string|max:20',
        'LifelineRate' => 'nullable|string|max:20',
        'InterClassCrossSubsidyCharge' => 'nullable|string|max:20',
        'PPARefund' => 'nullable|string|max:20',
        'SeniorCitizenSubsidy' => 'nullable|string|max:20',
        'MissionaryElectrificationCharge' => 'nullable|string|max:20',
        'EnvironmentalCharge' => 'nullable|string|max:20',
        'StrandedContractCosts' => 'nullable|string|max:20',
        'NPCStrandedDebt' => 'nullable|string|max:20',
        'FeedInTariffAllowance' => 'nullable|string|max:20',
        'MissionaryElectrificationREDCI' => 'nullable|string|max:20',
        'GenerationVAT' => 'nullable|string|max:20',
        'TransmissionVAT' => 'nullable|string|max:20',
        'SystemLossVAT' => 'nullable|string|max:20',
        'DistributionVAT' => 'nullable|string|max:20',
        'RealPropertyTax' => 'nullable|string|max:20',
        'Notes' => 'nullable|string|max:2500',
        'UserId' => 'nullable|string|max:255',
        'BilledFrom' => 'nullable|string|max:255',
        'created_at' => 'nullable',
        'updated_at' => 'nullable',
        'AveragedCount' => 'nullable|string',
        'MergedToCollectible' => 'nullable|string',
        'OtherGenerationRateAdjustment' => 'nullable|string',
        'OtherTransmissionCostAdjustmentKW' => 'nullable|string',
        'OtherTransmissionCostAdjustmentKWH' => 'nullable|string',
        'OtherSystemLossCostAdjustment' => 'nullable|string',
        'OtherLifelineRateCostAdjustment' => 'nullable|string',
        'SeniorCitizenDiscountAndSubsidyAdjustment' => 'nullable|string',
        'FranchiseTax' => 'nullable|string',
        'BusinessTax' => 'nullable|string',
        'AdjustmentType' => 'nullable|string',
        'Form2307Amount' => 'nullable|string',
        'DeductedDeposit' => 'nullable|string',
        'ExcessDeposit' => 'nullable|string',
        'IsUnlockedForPayment' => 'nullable|string',
        'UnlockedBy' => 'nullable|string',
        'Evat2Percent' => 'nullable|string',
        'Evat5Percent' => 'nullable|string'
    ];

    public static function createDueDate($readDate) {
        return date('Y-m-d', strtotime($readDate . ' +9 days'));
    }

    public static function computePenalty($netAmount) {
        if ($netAmount > 1000) {
            return ($netAmount * .3) + $netAmount;
        } else {
            return 56.00 + $netAmount;
        }
    }

    public static function getPenalty($netAmount) {
        if ($netAmount > 1000) {
            return $netAmount * .3;
        } else {
            return 56.00;
        }
    }

    public static function getFinalPenalty($bill) {
        if ($bill->ConsumerType == 'RESIDENTIAL' || $bill->ConsumerType == 'RESIDENTIAL RURAL' || $bill->ConsumerType == 'RURAL RESIDENTIAL') {
            return 0;
        } else {
            return round(floatval($bill->NetAmount) * .05, 2);
        }
    }

    public static function getAccountType($account) {
        if ($account->AccountType == 'RESIDENTIAL RURAL' || $account->AccountType == 'RURAL RESIDENTIAL') {
            return 'RESIDENTIAL';
        } else {
            return $account->AccountType;
        }
    }

    public static function assessDueBillAndGetSurcharge($bill) {
        if (date('Y-m-d', strtotime($bill->DueDate)) < date('Y-m-d')) {
            return Bills::getFinalPenalty($bill);
        } else {
            return 0;
        }
    }

    public static function getServiceDateFrom($accountNumber, $readDate, $period) {
        $bill = Bills::where('AccountNumber', $accountNumber)
            ->where('ServicePeriod', date('Y-m-d', strtotime($period . ' -1 month')))
            ->orderByDesc('ServicePeriod')
            ->first();

        if ($bill != null) {
            return $bill->ServiceDateFrom;
        } else {
            return date('Y-m-d', strtotime($readDate . ' -1 month'));
        }
    }

    public static function computeNetAmount($bill) {
        $amount = 0.0;

        $amount = $bill->GenerationSystemCharge +
                $bill->TransmissionDeliveryChargeKW +
                $bill->TransmissionDeliveryChargeKWH + 
                $bill->SystemLossCharge +
                $bill->DistributionDemandCharge + 
                $bill->DistributionSystemCharge + 
                $bill->SupplyRetailCustomerCharge + 
                $bill->SupplySystemCharge +
                $bill->MeteringRetailCustomerCharge + 
                $bill->MeteringSystemCharge + 
                $bill->RFSC + 
                $bill->LifelineRate + 
                $bill->InterClassCrossSubsidyCharge + 
                $bill->PPARefund + 
                $bill->SeniorCitizenSubsidy +
                $bill->MissionaryElectrificationCharge + 
                $bill->EnvironmentalCharge + 
                $bill->StrandedContractCosts + 
                $bill->NPCStrandedDebt + 
                $bill->FeedInTariffAllowance + 
                $bill->MissionaryElectrificationREDCI + 
                $bill->GenerationVAT + 
                $bill->TransmissionVAT +
                $bill->SystemLossVAT +
                $bill->DistributionVAT + 
                $bill->RealPropertyTax +
                $bill->OtherGenerationRateAdjustment +
                $bill->OtherTransmissionCostAdjustmentKW +
                $bill->OtherTransmissionCostAdjustmentKWH +
                $bill->OtherSystemLossCostAdjustment +
                $bill->OtherLifelineRateCostAdjustment +
                $bill->SeniorCitizenDiscountAndSubsidyAdjustment +
                $bill->FranchiseTax +
                $bill->BusinessTax +
                $bill->AdditionalCharges -
                $bill->Deductions;

        return round($amount, 4);
    }

    // MODIFY THIS
    public static function computeLifeLine($account, $bill, $rate) {
        $kwhUsed = floatval($bill->KwhUsed) * floatval($bill->Multiplier);
        // MODIFY THIS
        $deductibles = $bill->GenerationSystemCharge +
                    $bill->TransmissionDeliveryChargeKWH +
                    $bill->TransmissionDeliveryChargeKW +
                    $bill->SystemLossCharge +
                    $bill->OtherGenerationRateAdjustment +
                    $bill->OtherTransmissionCostAdjustmentKW +
                    $bill->OtherTransmissionCostAdjustmentKWH +
                    $bill->OtherSystemLossCostAdjustment +
                    $bill->DistributionDemandCharge +
                    $bill->DistributionSystemCharge +
                    $bill->SupplyRetailCustomerCharge +
                    $bill->SupplySystemCharge +
                    $bill->MeteringSystemCharge;

        if ($account->AccountType == 'RESIDENTIAL') {
            if ($kwhUsed < 15) {
                return -($deductibles * .5);
            } elseif ($kwhUsed >= 16 && $kwhUsed < 17) {
                return -($deductibles * .4);
            } elseif ($kwhUsed >= 17 && $kwhUsed < 18) {
                return -($deductibles * .3);
            } elseif ($kwhUsed >= 18 && $kwhUsed < 19) {
                return -($deductibles * .2);
            } elseif ($kwhUsed >= 19 && $kwhUsed < 20) {
                return -($deductibles * .15);
            } elseif ($kwhUsed >= 20 && $kwhUsed < 21) {
                return -($deductibles * .1);
            } elseif ($kwhUsed >= 21 && $kwhUsed <= 25) {
                return -($deductibles * .05);
            } elseif($kwhUsed > 25) {
                return $kwhUsed * Rates::floatRate($rate->LifelineRate);
            }    
        } else {
            return $kwhUsed * Rates::floatRate($rate->LifelineRate);
        }         
    }

    // MODIFY HIS
    public static function computeSeniorCitizen($account, $bill, $rate) {
        $kwhUsed = floatval($bill->KwhUsed) * floatval($bill->Multiplier);
        // MODIFY THIS
        $deductibles = $bill->GenerationSystemCharge +
                    $bill->TransmissionDeliveryChargeKWH +
                    $bill->TransmissionDeliveryChargeKW +
                    $bill->SystemLossCharge +
                    $bill->OtherGenerationRateAdjustment +
                    $bill->OtherTransmissionCostAdjustmentKW +
                    $bill->OtherTransmissionCostAdjustmentKWH +
                    $bill->OtherSystemLossCostAdjustment +
                    $bill->DistributionDemandCharge +
                    $bill->DistributionSystemCharge +
                    $bill->SupplyRetailCustomerCharge +
                    $bill->SupplySystemCharge +
                    $bill->MeteringRetailCustomerCharge +
                    $bill->MeteringSystemCharge;

        if ($account->SeniorCitizen == 'Yes' && $kwhUsed <= 100) {
            return -($deductibles * .05);
        } else {
            return $kwhUsed * Rates::floatRate($rate->SeniorCitizenSubsidy);
        }
    }

    public static function get2307($bill) {
        $taxables = $bill->GenerationVAT +
            $bill->TransmissionVAT +
            $bill->SystemLossVAT +
            $bill->DistributionVAT +
            $bill->FranchiseTax +
            $bill->RealPropertyTax +
            $bill->BusinessTax;

        return round($taxables * (2/12), 4);
    }

    public static function getDistributionVat($bill) {
        $vatables = $bill->DistributionSystemCharge +
            $bill->SupplySystemCharge +
            $bill->MeteringRetailCustomerCharge +
            $bill->MeteringSystemCharge +
            $bill->LifelineRate +
            $bill->OtherLifelineRateCostAdjustment;

        return $vatables * .12;
    }

    /**
     * COMPUTES THE BILL AND SAVE
     */
    public static function computeRegularBill($account, $billId, $kwh, $prev, $pres, $period, $readDate, $additionalCharges, $deductions, $is2307) {
        $rate = Rates::where('ConsumerType', Bills::getAccountType($account))
            ->where('ServicePeriod', $period)
            ->where('AreaCode', $account->Town)
            ->first();

        $meter = DB::table('Billing_Meters')
            ->where('ServiceAccountId', $account->AccountNumber)
            ->orderByDesc('created_at')
            ->first();

        if ($rate != null) {            
            // VARIABLES
            $effectiveRate = Rates::floatRate($rate->TotalRateVATIncluded);
            $kwhAmountUsed = round(floatval($kwh), 4);
            $multiplier = round(floatval($account->Multiplier != null ? $account->Multiplier : 1), 4);
            $kwh = $kwhAmountUsed * $multiplier;
            $additionalCharges = round(floatval($additionalCharges), 4);
            $deductions = round(floatval($deductions), 4);

            // IF BILL UPDATE
            if ($billId != null) {
                $bill = Bills::find($billId);

                if ($bill != null) {
                    $bill->KwhUsed = $kwhAmountUsed;
                    $bill->KwhAmount = round($kwh * $effectiveRate, 2);
                    $bill->AdditionalCharges = $additionalCharges;
                    $bill->Deductions = $deductions;
                    $bill->ServiceDateFrom = Bills::getServiceDateFrom($account->AccountNumber, $readDate, $period);
                    $bill->ServiceDateTo = $readDate;
                    $bill->BillingDate = date('Y-m-d');
                    $bill->DueDate = Bills::createDueDate($readDate);
                    $bill->MeterNumber = $meter != null ? $meter->SerialNumber : null;
                    $bill->ConsumerType = $account->AccountType;
                    $bill->BillType = $account->AccountType;    
                    $bill->Multiplier = $account->Multiplier;  
                    $bill->Coreloss = $account->Coreloss;  

                    // CHARGES
                    $bill->GenerationSystemCharge = round($kwh * Rates::floatRate($rate->GenerationSystemCharge), 4);
                    $bill->TransmissionDeliveryChargeKW = round($kwh * Rates::floatRate($rate->TransmissionDeliveryChargeKW), 4);
                    $bill->TransmissionDeliveryChargeKWH = round($kwh * Rates::floatRate($rate->TransmissionDeliveryChargeKWH), 4);
                    $bill->SystemLossCharge = round($kwh * Rates::floatRate($rate->SystemLossCharge), 4);
                    $bill->DistributionDemandCharge = round($kwh * Rates::floatRate($rate->DistributionDemandCharge), 4);
                    $bill->DistributionSystemCharge = round($kwh * Rates::floatRate($rate->DistributionSystemCharge), 4);
                    $bill->SupplyRetailCustomerCharge = round($kwh * Rates::floatRate($rate->SupplyRetailCustomerCharge), 4);
                    $bill->SupplySystemCharge = round($kwh * Rates::floatRate($rate->SupplySystemCharge), 4);
                    $bill->MeteringRetailCustomerCharge = round(Rates::floatRate($rate->MeteringRetailCustomerCharge), 4);
                    $bill->MeteringSystemCharge = round($kwh * Rates::floatRate($rate->MeteringSystemCharge), 4);
                    $bill->RFSC = round($kwh * Rates::floatRate($rate->RFSC), 4);
                    $bill->InterClassCrossSubsidyCharge = round($kwh * Rates::floatRate($rate->InterClassCrossSubsidyCharge), 4);
                    $bill->PPARefund = round($kwh * Rates::floatRate($rate->PPARefund), 4);
                    $bill->MissionaryElectrificationCharge = round($kwh * Rates::floatRate($rate->MissionaryElectrificationCharge), 4);
                    $bill->EnvironmentalCharge = round($kwh * Rates::floatRate($rate->EnvironmentalCharge), 4);
                    $bill->StrandedContractCosts = round($kwh * Rates::floatRate($rate->StrandedContractCosts), 4);
                    $bill->NPCStrandedDebt = round($kwh * Rates::floatRate($rate->NPCStrandedDebt), 4);
                    $bill->FeedInTariffAllowance = round($kwh * Rates::floatRate($rate->FeedInTariffAllowance), 4);
                    $bill->MissionaryElectrificationREDCI = round($kwh * Rates::floatRate($rate->MissionaryElectrificationREDCI), 4);
                    $bill->GenerationVAT = round($kwh * Rates::floatRate($rate->GenerationVAT), 4);
                    $bill->TransmissionVAT = round($kwh * Rates::floatRate($rate->TransmissionVAT), 4);
                    $bill->SystemLossVAT = round($kwh * Rates::floatRate($rate->SystemLossVAT), 4);
                    $bill->RealPropertyTax = round($kwh * Rates::floatRate($rate->RealPropertyTax), 4);
                    
                    $bill->OtherGenerationRateAdjustment = round($kwh * Rates::floatRate($rate->OtherGenerationRateAdjustment), 4);
                    $bill->OtherTransmissionCostAdjustmentKW = round($kwh * Rates::floatRate($rate->OtherTransmissionCostAdjustmentKW), 4);
                    $bill->OtherTransmissionCostAdjustmentKWH = round($kwh * Rates::floatRate($rate->OtherTransmissionCostAdjustmentKWH), 4);
                    $bill->OtherSystemLossCostAdjustment = round($kwh * Rates::floatRate($rate->OtherSystemLossCostAdjustment), 4);

                    if ($account->SeniorCitizen == 'Yes' && $kwh <= 100) {
                        $bill->OtherLifelineRateCostAdjustment = 0;
                        $bill->SeniorCitizenDiscountAndSubsidyAdjustment = 0;
                    } else {
                        $bill->OtherLifelineRateCostAdjustment = 0;
                        $bill->SeniorCitizenDiscountAndSubsidyAdjustment = round($kwh * Rates::floatRate($rate->SeniorCitizenDiscountAndSubsidyAdjustment), 4);
                    }                    
                    
                    $bill->FranchiseTax = round($kwh * Rates::floatRate($rate->FranchiseTax), 4);
                    $bill->BusinessTax = round($kwh * Rates::floatRate($rate->BusinessTax), 4);

                    $bill->LifelineRate = round(Bills::computeLifeLine($account, $bill, $rate), 4);
                    $bill->SeniorCitizenSubsidy = round(Bills::computeSeniorCitizen($account, $bill, $rate), 4);
                    
                    $bill->DistributionVAT = round(Bills::getDistributionVat($bill), 4);

                    if ($is2307 == 'true') {
                        $form2307 = Bills::get2307($bill);
                        $bill->Form2307Amount = $form2307;

                        // TO BE CREATED DYNAMICALLY
                        $bill->NetAmount = Bills::computeNetAmount($bill) - $form2307;
                    } else {
                        $form2307 = -floatval($bill->Form2307Amount);
                        $bill->Form2307Amount = null;

                        // TO BE CREATED DYNAMICALLY
                        $bill->NetAmount = Bills::computeNetAmount($bill);
                    }

                    $bill->NetAmount = round($bill->NetAmount, 2);
                    
                    $bill->BilledFrom = 'WEB';
                    $bill->UserId = Auth::id();

                    $bill->save();
                } else {
                    $bill = null;
                }
            } else { // IF NEW BILL
                // QUERY FIRST IF BILL EXISTS
                $bill = Bills::where('ServicePeriod', $period)
                    ->where('AccountNumber', $account->id)
                    ->first();

                if ($bill != null) {
                    $bill->KwhUsed = $kwhAmountUsed;
                    $bill->KwhAmount = round($kwh * $effectiveRate, 2);
                    $bill->AdditionalCharges = $additionalCharges;
                    $bill->Deductions = $deductions;
                    $bill->ServiceDateFrom = Bills::getServiceDateFrom($account->AccountNumber, $readDate, $period);
                    $bill->ServiceDateTo = $readDate;
                    $bill->BillingDate = date('Y-m-d');
                    $bill->DueDate = Bills::createDueDate($readDate);
                    $bill->MeterNumber = $meter != null ? $meter->SerialNumber : null;
                    $bill->ConsumerType = $account->AccountType;
                    $bill->BillType = $account->AccountType;    
                    $bill->Multiplier = $account->Multiplier;  
                    $bill->Coreloss = $account->Coreloss;   

                    // CHARGES
                    $bill->GenerationSystemCharge = round($kwh * Rates::floatRate($rate->GenerationSystemCharge), 4);
                    $bill->TransmissionDeliveryChargeKW = round($kwh * Rates::floatRate($rate->TransmissionDeliveryChargeKW), 4);
                    $bill->TransmissionDeliveryChargeKWH = round($kwh * Rates::floatRate($rate->TransmissionDeliveryChargeKWH), 4);
                    $bill->SystemLossCharge = round($kwh * Rates::floatRate($rate->SystemLossCharge), 4);
                    $bill->DistributionDemandCharge = round($kwh * Rates::floatRate($rate->DistributionDemandCharge), 4);
                    $bill->DistributionSystemCharge = round($kwh * Rates::floatRate($rate->DistributionSystemCharge), 4);
                    $bill->SupplyRetailCustomerCharge = round($kwh * Rates::floatRate($rate->SupplyRetailCustomerCharge), 4);
                    $bill->SupplySystemCharge = round($kwh * Rates::floatRate($rate->SupplySystemCharge), 4);
                    $bill->MeteringRetailCustomerCharge = round(Rates::floatRate($rate->MeteringRetailCustomerCharge), 4);
                    $bill->MeteringSystemCharge = round($kwh * Rates::floatRate($rate->MeteringSystemCharge), 4);
                    $bill->RFSC = round($kwh * Rates::floatRate($rate->RFSC), 4);
                    $bill->InterClassCrossSubsidyCharge = round($kwh * Rates::floatRate($rate->InterClassCrossSubsidyCharge), 4);
                    $bill->PPARefund = round($kwh * Rates::floatRate($rate->PPARefund), 4);
                    $bill->MissionaryElectrificationCharge = round($kwh * Rates::floatRate($rate->MissionaryElectrificationCharge), 4);
                    $bill->EnvironmentalCharge = round($kwh * Rates::floatRate($rate->EnvironmentalCharge), 4);
                    $bill->StrandedContractCosts = round($kwh * Rates::floatRate($rate->StrandedContractCosts), 4);
                    $bill->NPCStrandedDebt = round($kwh * Rates::floatRate($rate->NPCStrandedDebt), 4);
                    $bill->FeedInTariffAllowance = round($kwh * Rates::floatRate($rate->FeedInTariffAllowance), 4);
                    $bill->MissionaryElectrificationREDCI = round($kwh * Rates::floatRate($rate->MissionaryElectrificationREDCI), 4);
                    $bill->GenerationVAT = round($kwh * Rates::floatRate($rate->GenerationVAT), 4);
                    $bill->TransmissionVAT = round($kwh * Rates::floatRate($rate->TransmissionVAT), 4);
                    $bill->SystemLossVAT = round($kwh * Rates::floatRate($rate->SystemLossVAT), 4);
                    $bill->RealPropertyTax = round($kwh * Rates::floatRate($rate->RealPropertyTax), 4);

                    $bill->OtherGenerationRateAdjustment = round($kwh * Rates::floatRate($rate->OtherGenerationRateAdjustment), 4);
                    $bill->OtherTransmissionCostAdjustmentKW = round($kwh * Rates::floatRate($rate->OtherTransmissionCostAdjustmentKW), 4);
                    $bill->OtherTransmissionCostAdjustmentKWH = round($kwh * Rates::floatRate($rate->OtherTransmissionCostAdjustmentKWH), 4);
                    $bill->OtherSystemLossCostAdjustment = round($kwh * Rates::floatRate($rate->OtherSystemLossCostAdjustment), 4);
                    
                    if ($account->SeniorCitizen == 'Yes' && $kwh <= 100) {
                        $bill->OtherLifelineRateCostAdjustment = 0;
                        $bill->SeniorCitizenDiscountAndSubsidyAdjustment = 0;
                    } else {
                        $bill->OtherLifelineRateCostAdjustment = 0;
                        $bill->SeniorCitizenDiscountAndSubsidyAdjustment = round($kwh * Rates::floatRate($rate->SeniorCitizenDiscountAndSubsidyAdjustment), 4);
                    } 

                    $bill->FranchiseTax = round($kwh * Rates::floatRate($rate->FranchiseTax), 4);
                    $bill->BusinessTax = round($kwh * Rates::floatRate($rate->BusinessTax), 4);

                    $bill->LifelineRate = round(Bills::computeLifeLine($account, $bill, $rate), 4);
                    $bill->SeniorCitizenSubsidy = round(Bills::computeSeniorCitizen($account, $bill, $rate), 4);

                    $bill->DistributionVAT = round(Bills::getDistributionVat($bill), 4);

                    if ($is2307 == 'true') {
                        $form2307 = Bills::get2307($bill);
                        $bill->Form2307Amount = $form2307;

                        // TO BE CREATED DYNAMICALLY
                        $bill->NetAmount = Bills::computeNetAmount($bill) - $form2307;
                    } else {
                        $form2307 = -floatval($bill->Form2307Amount);
                        $bill->Form2307Amount = null;

                        // TO BE CREATED DYNAMICALLY
                        $bill->NetAmount = Bills::computeNetAmount($bill);
                    }

                    $bill->NetAmount = round($bill->NetAmount, 2);
                    
                    $bill->BilledFrom = 'WEB';
                    $bill->UserId = Auth::id();

                    $bill->save();
                } else {
                    $bill = new Bills;
                    $bill->id = IDGenerator::generateIDandRandString();
                    $bill->BillNumber = IDGenerator::generateBillNumber($account->Town);
                    $bill->AccountNumber = $account->id;
                    $bill->ServicePeriod = $period;
                    $bill->Multiplier = $account->Multiplier;
                    $bill->Coreloss = $account->Coreloss;
                    $bill->KwhUsed = $kwhAmountUsed;
                    $bill->PreviousKwh = ($prev == 0 ? 0 : round(floatval($prev), 4));
                    $bill->PresentKwh = round(floatval($pres), 4);
                    $bill->EffectiveRate = $effectiveRate;
                    $bill->KwhAmount = round($kwh * $effectiveRate, 2);
                    $bill->AdditionalCharges = $additionalCharges;
                    $bill->Deductions = $deductions;
                    $bill->ServiceDateFrom = Bills::getServiceDateFrom($account->AccountNumber, $readDate, $period);
                    $bill->ServiceDateTo = $readDate;
                    $bill->BillingDate = date('Y-m-d');
                    $bill->DueDate = Bills::createDueDate($readDate);
                    $bill->MeterNumber = ($meter != null ? $meter->SerialNumber : null);
                    $bill->ConsumerType = $account->AccountType;
                    $bill->BillType = $account->AccountType;  
                    $bill->Multiplier = $account->Multiplier;  
                    $bill->Coreloss = $account->Coreloss;     

                    // CHARGES
                    $bill->GenerationSystemCharge = round($kwh * Rates::floatRate($rate->GenerationSystemCharge), 4);
                    $bill->TransmissionDeliveryChargeKW = round($kwh * Rates::floatRate($rate->TransmissionDeliveryChargeKW), 4);
                    $bill->TransmissionDeliveryChargeKWH = round($kwh * Rates::floatRate($rate->TransmissionDeliveryChargeKWH), 4);
                    $bill->SystemLossCharge = round($kwh * Rates::floatRate($rate->SystemLossCharge), 4);
                    $bill->DistributionDemandCharge = round($kwh * Rates::floatRate($rate->DistributionDemandCharge), 4);
                    $bill->DistributionSystemCharge = round($kwh * Rates::floatRate($rate->DistributionSystemCharge), 4);
                    $bill->SupplyRetailCustomerCharge = round($kwh * Rates::floatRate($rate->SupplyRetailCustomerCharge), 4);
                    $bill->SupplySystemCharge = round($kwh * Rates::floatRate($rate->SupplySystemCharge), 4);
                    $bill->MeteringRetailCustomerCharge = round(Rates::floatRate($rate->MeteringRetailCustomerCharge), 4);
                    $bill->MeteringSystemCharge = round($kwh * Rates::floatRate($rate->MeteringSystemCharge), 4);
                    $bill->RFSC = round($kwh * Rates::floatRate($rate->RFSC), 4);
                    $bill->InterClassCrossSubsidyCharge = round($kwh * Rates::floatRate($rate->InterClassCrossSubsidyCharge), 4);
                    $bill->PPARefund = round($kwh * Rates::floatRate($rate->PPARefund), 4);
                    $bill->MissionaryElectrificationCharge = round($kwh * Rates::floatRate($rate->MissionaryElectrificationCharge), 4);
                    $bill->EnvironmentalCharge = round($kwh * Rates::floatRate($rate->EnvironmentalCharge), 4);
                    $bill->StrandedContractCosts = round($kwh * Rates::floatRate($rate->StrandedContractCosts), 4);
                    $bill->NPCStrandedDebt = round($kwh * Rates::floatRate($rate->NPCStrandedDebt), 4);
                    $bill->FeedInTariffAllowance = round($kwh * Rates::floatRate($rate->FeedInTariffAllowance), 4);
                    $bill->MissionaryElectrificationREDCI = round($kwh * Rates::floatRate($rate->MissionaryElectrificationREDCI), 4);
                    $bill->GenerationVAT = round($kwh * Rates::floatRate($rate->GenerationVAT), 4);
                    $bill->TransmissionVAT = round($kwh * Rates::floatRate($rate->TransmissionVAT), 4);
                    $bill->SystemLossVAT = round($kwh * Rates::floatRate($rate->SystemLossVAT), 4);
                    $bill->RealPropertyTax = round($kwh * Rates::floatRate($rate->RealPropertyTax), 4);

                    $bill->OtherGenerationRateAdjustment = round($kwh * Rates::floatRate($rate->OtherGenerationRateAdjustment), 4);
                    $bill->OtherTransmissionCostAdjustmentKW = round($kwh * Rates::floatRate($rate->OtherTransmissionCostAdjustmentKW), 4);
                    $bill->OtherTransmissionCostAdjustmentKWH = round($kwh * Rates::floatRate($rate->OtherTransmissionCostAdjustmentKWH), 4);
                    $bill->OtherSystemLossCostAdjustment = round($kwh * Rates::floatRate($rate->OtherSystemLossCostAdjustment), 4);
                    
                    if ($account->SeniorCitizen == 'Yes' && $kwh <= 100) {
                        $bill->OtherLifelineRateCostAdjustment = 0;
                        $bill->SeniorCitizenDiscountAndSubsidyAdjustment = 0;
                    } else {
                        $bill->OtherLifelineRateCostAdjustment = 0;
                        $bill->SeniorCitizenDiscountAndSubsidyAdjustment = round($kwh * Rates::floatRate($rate->SeniorCitizenDiscountAndSubsidyAdjustment), 4);
                    } 

                    $bill->FranchiseTax = round($kwh * Rates::floatRate($rate->FranchiseTax), 4);
                    $bill->BusinessTax = round($kwh * Rates::floatRate($rate->BusinessTax), 4);

                    $bill->LifelineRate = round(Bills::computeLifeLine($account, $bill, $rate), 4);
                    $bill->SeniorCitizenSubsidy = round(Bills::computeSeniorCitizen($account, $bill, $rate), 4);

                    $bill->DistributionVAT = round(Bills::getDistributionVat($bill), 4);

                    if ($is2307 == 'true') {
                        $form2307 = Bills::get2307($bill);
                        $bill->Form2307Amount = $form2307;

                        // TO BE CREATED DYNAMICALLY
                        $bill->NetAmount = Bills::computeNetAmount($bill) - $form2307;
                    } else {
                        $form2307 = -floatval($bill->Form2307Amount);
                        $bill->Form2307Amount = null;

                        // TO BE CREATED DYNAMICALLY
                        $bill->NetAmount = Bills::computeNetAmount($bill);
                    }

                    $bill->NetAmount = round($bill->NetAmount, 2);
                    
                    $bill->BilledFrom = 'WEB';
                    $bill->UserId = Auth::id();

                    $bill->save();
                }
                
            }
            return $bill;
        } else {
            return null;
        }
    }

    /**
     * COMPUTES THE BILL ONLY
     */
    public static function computeRegularBillAndDontSave($account, $billId, $kwh, $prev, $pres, $period, $readDate, $additionalCharges, $deductions, $is2307) {
        $rate = Rates::where('ConsumerType', Bills::getAccountType($account))
            ->where('ServicePeriod', $period)
            ->where('AreaCode', $account->Town)
            ->first();

        $meter = DB::table('Billing_Meters')
            ->where('ServiceAccountId', $account->AccountNumber)
            ->orderByDesc('created_at')
            ->first();

        if ($rate != null) {            
            // VARIABLES
            $effectiveRate = Rates::floatRate($rate->TotalRateVATIncluded);
            $kwhAmountUsed = round(floatval($kwh), 4);
            $multiplier = round(floatval($account->Multiplier != null ? $account->Multiplier : 1), 4);
            $kwh = $kwhAmountUsed * $multiplier;
            $additionalCharges = round(floatval($additionalCharges), 4);
            $deductions = round(floatval($deductions), 4);

            // IF BILL UPDATE
            if ($billId != null) {
                $bill = Bills::find($billId);

                if ($bill != null) {
                    $bill->KwhUsed = $kwhAmountUsed;
                    $bill->KwhAmount = round($kwh * $effectiveRate, 2);
                    $bill->AdditionalCharges = $additionalCharges;
                    $bill->Deductions = $deductions;
                    $bill->ServiceDateFrom = Bills::getServiceDateFrom($account->AccountNumber, $readDate, $period);
                    $bill->ServiceDateTo = $readDate;
                    $bill->DueDate = Bills::createDueDate($readDate);
                    $bill->MeterNumber = $meter != null ? $meter->SerialNumber : null;
                    $bill->ConsumerType = $account->AccountType;
                    $bill->BillType = $account->AccountType;    
                    $bill->Multiplier = $account->Multiplier;  
                    $bill->Coreloss = $account->Coreloss;  

                    // CHARGES
                    $bill->GenerationSystemCharge = round($kwh * Rates::floatRate($rate->GenerationSystemCharge), 4);
                    $bill->TransmissionDeliveryChargeKW = round($kwh * Rates::floatRate($rate->TransmissionDeliveryChargeKW), 4);
                    $bill->TransmissionDeliveryChargeKWH = round($kwh * Rates::floatRate($rate->TransmissionDeliveryChargeKWH), 4);
                    $bill->SystemLossCharge = round($kwh * Rates::floatRate($rate->SystemLossCharge), 4);
                    $bill->DistributionDemandCharge = round($kwh * Rates::floatRate($rate->DistributionDemandCharge), 4);
                    $bill->DistributionSystemCharge = round($kwh * Rates::floatRate($rate->DistributionSystemCharge), 4);
                    $bill->SupplyRetailCustomerCharge = round($kwh * Rates::floatRate($rate->SupplyRetailCustomerCharge), 4);
                    $bill->SupplySystemCharge = round($kwh * Rates::floatRate($rate->SupplySystemCharge), 4);
                    $bill->MeteringRetailCustomerCharge = round(Rates::floatRate($rate->MeteringRetailCustomerCharge), 4);
                    $bill->MeteringSystemCharge = round($kwh * Rates::floatRate($rate->MeteringSystemCharge), 4);
                    $bill->RFSC = round($kwh * Rates::floatRate($rate->RFSC), 4);
                    $bill->InterClassCrossSubsidyCharge = round($kwh * Rates::floatRate($rate->InterClassCrossSubsidyCharge), 4);
                    $bill->PPARefund = round($kwh * Rates::floatRate($rate->PPARefund), 4);
                    $bill->MissionaryElectrificationCharge = round($kwh * Rates::floatRate($rate->MissionaryElectrificationCharge), 4);
                    $bill->EnvironmentalCharge = round($kwh * Rates::floatRate($rate->EnvironmentalCharge), 4);
                    $bill->StrandedContractCosts = round($kwh * Rates::floatRate($rate->StrandedContractCosts), 4);
                    $bill->NPCStrandedDebt = round($kwh * Rates::floatRate($rate->NPCStrandedDebt), 4);
                    $bill->FeedInTariffAllowance = round($kwh * Rates::floatRate($rate->FeedInTariffAllowance), 4);
                    $bill->MissionaryElectrificationREDCI = round($kwh * Rates::floatRate($rate->MissionaryElectrificationREDCI), 4);
                    $bill->GenerationVAT = round($kwh * Rates::floatRate($rate->GenerationVAT), 4);
                    $bill->TransmissionVAT = round($kwh * Rates::floatRate($rate->TransmissionVAT), 4);
                    $bill->SystemLossVAT = round($kwh * Rates::floatRate($rate->SystemLossVAT), 4);
                    $bill->RealPropertyTax = round($kwh * Rates::floatRate($rate->RealPropertyTax), 4);
                    
                    $bill->OtherGenerationRateAdjustment = round($kwh * Rates::floatRate($rate->OtherGenerationRateAdjustment), 4);
                    $bill->OtherTransmissionCostAdjustmentKW = round($kwh * Rates::floatRate($rate->OtherTransmissionCostAdjustmentKW), 4);
                    $bill->OtherTransmissionCostAdjustmentKWH = round($kwh * Rates::floatRate($rate->OtherTransmissionCostAdjustmentKWH), 4);
                    $bill->OtherSystemLossCostAdjustment = round($kwh * Rates::floatRate($rate->OtherSystemLossCostAdjustment), 4);

                    if ($account->SeniorCitizen == 'Yes' && $kwh <= 100) {
                        $bill->OtherLifelineRateCostAdjustment = 0;
                        $bill->SeniorCitizenDiscountAndSubsidyAdjustment = 0;
                    } else {
                        $bill->OtherLifelineRateCostAdjustment = 0;
                        $bill->SeniorCitizenDiscountAndSubsidyAdjustment = round($kwh * Rates::floatRate($rate->SeniorCitizenDiscountAndSubsidyAdjustment), 4);
                    }                    
                    
                    $bill->FranchiseTax = round($kwh * Rates::floatRate($rate->FranchiseTax), 4);
                    $bill->BusinessTax = round($kwh * Rates::floatRate($rate->BusinessTax), 4);

                    $bill->LifelineRate = round(Bills::computeLifeLine($account, $bill, $rate), 4);
                    $bill->SeniorCitizenSubsidy = round(Bills::computeSeniorCitizen($account, $bill, $rate), 4);
                    
                    $bill->DistributionVAT = round(Bills::getDistributionVat($bill), 4);

                    if ($is2307 == 'true') {
                        $form2307 = Bills::get2307($bill);
                        $bill->Form2307Amount = $form2307;

                        // TO BE CREATED DYNAMICALLY
                        $bill->NetAmount = Bills::computeNetAmount($bill) - $form2307;
                    } else {
                        $form2307 = -floatval($bill->Form2307Amount);
                        $bill->Form2307Amount = null;

                        // TO BE CREATED DYNAMICALLY
                        $bill->NetAmount = Bills::computeNetAmount($bill);
                    }

                    $bill->NetAmount = round($bill->NetAmount, 2);
                    
                    $bill->BilledFrom = 'WEB';
                    $bill->UserId = Auth::id();

                    // $bill->save();
                } else {
                    $bill = null;
                }
            } else { // IF NEW BILL
                // QUERY FIRST IF BILL EXISTS
                $bill = Bills::where('ServicePeriod', $period)
                    ->where('AccountNumber', $account->id)
                    ->first();

                if ($bill != null) {
                    $bill->KwhUsed = $kwhAmountUsed;
                    $bill->KwhAmount = round($kwh * $effectiveRate, 2);
                    $bill->AdditionalCharges = $additionalCharges;
                    $bill->Deductions = $deductions;
                    $bill->ServiceDateFrom = Bills::getServiceDateFrom($account->AccountNumber, $readDate, $period);
                    $bill->ServiceDateTo = $readDate;
                    $bill->DueDate = Bills::createDueDate($readDate);
                    $bill->MeterNumber = $meter != null ? $meter->SerialNumber : null;
                    $bill->ConsumerType = $account->AccountType;
                    $bill->BillType = $account->AccountType;    
                    $bill->Multiplier = $account->Multiplier;  
                    $bill->Coreloss = $account->Coreloss;   

                    // CHARGES
                    $bill->GenerationSystemCharge = round($kwh * Rates::floatRate($rate->GenerationSystemCharge), 4);
                    $bill->TransmissionDeliveryChargeKW = round($kwh * Rates::floatRate($rate->TransmissionDeliveryChargeKW), 4);
                    $bill->TransmissionDeliveryChargeKWH = round($kwh * Rates::floatRate($rate->TransmissionDeliveryChargeKWH), 4);
                    $bill->SystemLossCharge = round($kwh * Rates::floatRate($rate->SystemLossCharge), 4);
                    $bill->DistributionDemandCharge = round($kwh * Rates::floatRate($rate->DistributionDemandCharge), 4);
                    $bill->DistributionSystemCharge = round($kwh * Rates::floatRate($rate->DistributionSystemCharge), 4);
                    $bill->SupplyRetailCustomerCharge = round($kwh * Rates::floatRate($rate->SupplyRetailCustomerCharge), 4);
                    $bill->SupplySystemCharge = round($kwh * Rates::floatRate($rate->SupplySystemCharge), 4);
                    $bill->MeteringRetailCustomerCharge = round(Rates::floatRate($rate->MeteringRetailCustomerCharge), 4);
                    $bill->MeteringSystemCharge = round($kwh * Rates::floatRate($rate->MeteringSystemCharge), 4);
                    $bill->RFSC = round($kwh * Rates::floatRate($rate->RFSC), 4);
                    $bill->InterClassCrossSubsidyCharge = round($kwh * Rates::floatRate($rate->InterClassCrossSubsidyCharge), 4);
                    $bill->PPARefund = round($kwh * Rates::floatRate($rate->PPARefund), 4);
                    $bill->MissionaryElectrificationCharge = round($kwh * Rates::floatRate($rate->MissionaryElectrificationCharge), 4);
                    $bill->EnvironmentalCharge = round($kwh * Rates::floatRate($rate->EnvironmentalCharge), 4);
                    $bill->StrandedContractCosts = round($kwh * Rates::floatRate($rate->StrandedContractCosts), 4);
                    $bill->NPCStrandedDebt = round($kwh * Rates::floatRate($rate->NPCStrandedDebt), 4);
                    $bill->FeedInTariffAllowance = round($kwh * Rates::floatRate($rate->FeedInTariffAllowance), 4);
                    $bill->MissionaryElectrificationREDCI = round($kwh * Rates::floatRate($rate->MissionaryElectrificationREDCI), 4);
                    $bill->GenerationVAT = round($kwh * Rates::floatRate($rate->GenerationVAT), 4);
                    $bill->TransmissionVAT = round($kwh * Rates::floatRate($rate->TransmissionVAT), 4);
                    $bill->SystemLossVAT = round($kwh * Rates::floatRate($rate->SystemLossVAT), 4);
                    $bill->RealPropertyTax = round($kwh * Rates::floatRate($rate->RealPropertyTax), 4);

                    $bill->OtherGenerationRateAdjustment = round($kwh * Rates::floatRate($rate->OtherGenerationRateAdjustment), 4);
                    $bill->OtherTransmissionCostAdjustmentKW = round($kwh * Rates::floatRate($rate->OtherTransmissionCostAdjustmentKW), 4);
                    $bill->OtherTransmissionCostAdjustmentKWH = round($kwh * Rates::floatRate($rate->OtherTransmissionCostAdjustmentKWH), 4);
                    $bill->OtherSystemLossCostAdjustment = round($kwh * Rates::floatRate($rate->OtherSystemLossCostAdjustment), 4);
                    
                    if ($account->SeniorCitizen == 'Yes' && $kwh <= 100) {
                        $bill->OtherLifelineRateCostAdjustment = 0;
                        $bill->SeniorCitizenDiscountAndSubsidyAdjustment = 0;
                    } else {
                        $bill->OtherLifelineRateCostAdjustment = 0;
                        $bill->SeniorCitizenDiscountAndSubsidyAdjustment = round($kwh * Rates::floatRate($rate->SeniorCitizenDiscountAndSubsidyAdjustment), 4);
                    } 

                    $bill->FranchiseTax = round($kwh * Rates::floatRate($rate->FranchiseTax), 4);
                    $bill->BusinessTax = round($kwh * Rates::floatRate($rate->BusinessTax), 4);

                    $bill->LifelineRate = round(Bills::computeLifeLine($account, $bill, $rate), 4);
                    $bill->SeniorCitizenSubsidy = round(Bills::computeSeniorCitizen($account, $bill, $rate), 4);

                    $bill->DistributionVAT = round(Bills::getDistributionVat($bill), 4);

                    if ($is2307 == 'true') {
                        $form2307 = Bills::get2307($bill);
                        $bill->Form2307Amount = $form2307;

                        // TO BE CREATED DYNAMICALLY
                        $bill->NetAmount = Bills::computeNetAmount($bill) - $form2307;
                    } else {
                        $form2307 = -floatval($bill->Form2307Amount);
                        $bill->Form2307Amount = null;

                        // TO BE CREATED DYNAMICALLY
                        $bill->NetAmount = Bills::computeNetAmount($bill);
                    }

                    $bill->NetAmount = round($bill->NetAmount, 2);
                    
                    $bill->BilledFrom = 'WEB';
                    $bill->UserId = Auth::id();

                    // $bill->save();
                } else {
                    $bill = new Bills;
                    $bill->id = IDGenerator::generateIDandRandString();
                    $bill->BillNumber = IDGenerator::generateBillNumber($account->Town);
                    $bill->AccountNumber = $account->id;
                    $bill->ServicePeriod = $period;
                    $bill->Multiplier = $account->Multiplier;
                    $bill->Coreloss = $account->Coreloss;
                    $bill->KwhUsed = $kwhAmountUsed;
                    $bill->PreviousKwh = $prev;
                    $bill->PresentKwh = round(floatval($pres), 4);
                    $bill->EffectiveRate = $effectiveRate;
                    $bill->KwhAmount = round($kwh * $effectiveRate, 2);
                    $bill->AdditionalCharges = $additionalCharges;
                    $bill->Deductions = $deductions;
                    $bill->ServiceDateFrom = Bills::getServiceDateFrom($account->AccountNumber, $readDate, $period);
                    $bill->ServiceDateTo = $readDate;
                    $bill->DueDate = Bills::createDueDate($readDate);
                    $bill->MeterNumber = $meter != null ? $meter->SerialNumber : null;
                    $bill->ConsumerType = $account->AccountType;
                    $bill->BillType = $account->AccountType;  
                    $bill->Multiplier = $account->Multiplier;  
                    $bill->Coreloss = $account->Coreloss;     

                    // CHARGES
                    $bill->GenerationSystemCharge = round($kwh * Rates::floatRate($rate->GenerationSystemCharge), 4);
                    $bill->TransmissionDeliveryChargeKW = round($kwh * Rates::floatRate($rate->TransmissionDeliveryChargeKW), 4);
                    $bill->TransmissionDeliveryChargeKWH = round($kwh * Rates::floatRate($rate->TransmissionDeliveryChargeKWH), 4);
                    $bill->SystemLossCharge = round($kwh * Rates::floatRate($rate->SystemLossCharge), 4);
                    $bill->DistributionDemandCharge = round($kwh * Rates::floatRate($rate->DistributionDemandCharge), 4);
                    $bill->DistributionSystemCharge = round($kwh * Rates::floatRate($rate->DistributionSystemCharge), 4);
                    $bill->SupplyRetailCustomerCharge = round($kwh * Rates::floatRate($rate->SupplyRetailCustomerCharge), 4);
                    $bill->SupplySystemCharge = round($kwh * Rates::floatRate($rate->SupplySystemCharge), 4);
                    $bill->MeteringRetailCustomerCharge = round(Rates::floatRate($rate->MeteringRetailCustomerCharge), 4);
                    $bill->MeteringSystemCharge = round($kwh * Rates::floatRate($rate->MeteringSystemCharge), 4);
                    $bill->RFSC = round($kwh * Rates::floatRate($rate->RFSC), 4);
                    $bill->InterClassCrossSubsidyCharge = round($kwh * Rates::floatRate($rate->InterClassCrossSubsidyCharge), 4);
                    $bill->PPARefund = round($kwh * Rates::floatRate($rate->PPARefund), 4);
                    $bill->MissionaryElectrificationCharge = round($kwh * Rates::floatRate($rate->MissionaryElectrificationCharge), 4);
                    $bill->EnvironmentalCharge = round($kwh * Rates::floatRate($rate->EnvironmentalCharge), 4);
                    $bill->StrandedContractCosts = round($kwh * Rates::floatRate($rate->StrandedContractCosts), 4);
                    $bill->NPCStrandedDebt = round($kwh * Rates::floatRate($rate->NPCStrandedDebt), 4);
                    $bill->FeedInTariffAllowance = round($kwh * Rates::floatRate($rate->FeedInTariffAllowance), 4);
                    $bill->MissionaryElectrificationREDCI = round($kwh * Rates::floatRate($rate->MissionaryElectrificationREDCI), 4);
                    $bill->GenerationVAT = round($kwh * Rates::floatRate($rate->GenerationVAT), 4);
                    $bill->TransmissionVAT = round($kwh * Rates::floatRate($rate->TransmissionVAT), 4);
                    $bill->SystemLossVAT = round($kwh * Rates::floatRate($rate->SystemLossVAT), 4);
                    $bill->RealPropertyTax = round($kwh * Rates::floatRate($rate->RealPropertyTax), 4);

                    $bill->OtherGenerationRateAdjustment = round($kwh * Rates::floatRate($rate->OtherGenerationRateAdjustment), 4);
                    $bill->OtherTransmissionCostAdjustmentKW = round($kwh * Rates::floatRate($rate->OtherTransmissionCostAdjustmentKW), 4);
                    $bill->OtherTransmissionCostAdjustmentKWH = round($kwh * Rates::floatRate($rate->OtherTransmissionCostAdjustmentKWH), 4);
                    $bill->OtherSystemLossCostAdjustment = round($kwh * Rates::floatRate($rate->OtherSystemLossCostAdjustment), 4);
                    
                    if ($account->SeniorCitizen == 'Yes' && $kwh <= 100) {
                        $bill->OtherLifelineRateCostAdjustment = 0;
                        $bill->SeniorCitizenDiscountAndSubsidyAdjustment = 0;
                    } else {
                        $bill->OtherLifelineRateCostAdjustment = 0;
                        $bill->SeniorCitizenDiscountAndSubsidyAdjustment = round($kwh * Rates::floatRate($rate->SeniorCitizenDiscountAndSubsidyAdjustment), 4);
                    } 

                    $bill->FranchiseTax = round($kwh * Rates::floatRate($rate->FranchiseTax), 4);
                    $bill->BusinessTax = round($kwh * Rates::floatRate($rate->BusinessTax), 4);

                    $bill->LifelineRate = round(Bills::computeLifeLine($account, $bill, $rate), 4);
                    $bill->SeniorCitizenSubsidy = round(Bills::computeSeniorCitizen($account, $bill, $rate), 4);

                    $bill->DistributionVAT = round(Bills::getDistributionVat($bill), 4);

                    if ($is2307 == 'true') {
                        $form2307 = Bills::get2307($bill);
                        $bill->Form2307Amount = $form2307;

                        // TO BE CREATED DYNAMICALLY
                        $bill->NetAmount = Bills::computeNetAmount($bill) - $form2307;
                    } else {
                        $form2307 = -floatval($bill->Form2307Amount);
                        $bill->Form2307Amount = null;

                        // TO BE CREATED DYNAMICALLY
                        $bill->NetAmount = Bills::computeNetAmount($bill);
                    }

                    $bill->NetAmount = round($bill->NetAmount, 2);
                    
                    $bill->BilledFrom = 'WEB';
                    $bill->UserId = Auth::id();

                    // $bill->save();
                }
                
            }
            return $bill;
        } else {
            return null;
        }
    }

    /**
     * 2%
     */
    public static function add2Percent($billId) {
        $bill = Bills::find($billId);

        $percentage = floatval($bill->NetAmount) * .02;

        $bill->Evat2Percent = $percentage;
        $bill->NetAmount = round(floatval($bill->NetAmount) + $percentage, 2);
        $bill->save();

        return $bill;
    }

    public static function remove2Percent($billId) {
        $bill = Bills::find($billId);
        
        $bill->NetAmount = round(floatval($bill->NetAmount) - floatval($bill->Evat2Percent), 2);
        $bill->Evat2Percent = null;
        $bill->save();

        return $bill;
    }

    /**
     * 5%
     */
    public static function add5Percent($billId) {
        $bill = Bills::find($billId);

        $percentage = floatval($bill->NetAmount) * .05;

        $bill->Evat5Percent = $percentage;
        $bill->NetAmount = round(floatval($bill->NetAmount) + $percentage, 2);
        $bill->save();

        return $bill;
    }

    public static function remove5Percent($billId) {
        $bill = Bills::find($billId);
        
        $bill->NetAmount = round(floatval($bill->NetAmount) - floatval($bill->Evat5Percent), 2);
        $bill->Evat5Percent = null;
        $bill->save();

        return $bill;
    }
}
