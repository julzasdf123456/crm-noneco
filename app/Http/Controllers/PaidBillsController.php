<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePaidBillsRequest;
use App\Http\Requests\UpdatePaidBillsRequest;
use App\Repositories\PaidBillsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;   
use Illuminate\Support\Facades\Auth;    
use App\Models\ServiceAccounts;
use App\Models\Bills;
use App\Models\IDGenerator;
use App\Models\PaidBills;
use App\Models\PaidBillsDetails;
use App\Models\ORAssigning;
use App\Models\Notifiers;
use App\Models\BillingMeters;
use App\Models\ORCancellations;
use App\Models\Towns;
use App\Models\BAPAPayments;
use App\Models\DCRSummaryTransactions;
use App\Models\ArrearsLedgerDistribution;
use App\Models\BAPAAdjustmentDetails;
use App\Models\Denominations;
use App\Models\PrePaymentBalance;
use App\Models\TransactionDetails;
use App\Models\TransactionIndex;
use App\Models\PrePaymentTransHistory;
use App\Imports\ThirdPartyPaidBills;
use Maatwebsite\Excel\Facades\Excel;
use Flash;
use Response;

class PaidBillsController extends AppBaseController
{
    /** @var  PaidBillsRepository */
    private $paidBillsRepository;

    public function __construct(PaidBillsRepository $paidBillsRepo)
    {
        $this->middleware('auth');
        $this->paidBillsRepository = $paidBillsRepo;
    }

    /**
     * Display a listing of the PaidBills.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $orAssignedLast = ORAssigning::whereRaw("UserId ='" . Auth::id() . "'")
            ->orderByDesc('created_at')
            ->first();

        return view('paid_bills.index', [
            'orAssignedLast' => $orAssignedLast,
        ]);
    }

    /**
     * Show the form for creating a new PaidBills.
     *
     * @return Response
     */
    public function create()
    {
        return view('paid_bills.create');
    }

    /**
     * Store a newly created PaidBills in storage.
     *
     * @param CreatePaidBillsRequest $request
     *
     * @return Response
     */
    public function store(CreatePaidBillsRequest $request)
    {
        $input = $request->all();

        $paidBills = $this->paidBillsRepository->create($input);

        Flash::success('Paid Bills saved successfully.');

        return redirect(route('paidBills.index'));
    }

    /**
     * Display the specified PaidBills.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $paidBills = $this->paidBillsRepository->find($id);

        if (empty($paidBills)) {
            Flash::error('Paid Bills not found');

            return redirect(route('paidBills.index'));
        }

        return view('paid_bills.show')->with('paidBills', $paidBills);
    }

    /**
     * Show the form for editing the specified PaidBills.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $paidBills = $this->paidBillsRepository->find($id);

        if (empty($paidBills)) {
            Flash::error('Paid Bills not found');

            return redirect(route('paidBills.index'));
        }

        return view('paid_bills.edit')->with('paidBills', $paidBills);
    }

    /**
     * Update the specified PaidBills in storage.
     *
     * @param int $id
     * @param UpdatePaidBillsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePaidBillsRequest $request)
    {
        $paidBills = $this->paidBillsRepository->find($id);

        if (empty($paidBills)) {
            Flash::error('Paid Bills not found');

            return redirect(route('paidBills.index'));
        }

        $paidBills = $this->paidBillsRepository->update($request->all(), $id);

        Flash::success('Paid Bills updated successfully.');

        return redirect(route('paidBills.index'));
    }

    /**
     * Remove the specified PaidBills from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $paidBills = $this->paidBillsRepository->find($id);

        if (empty($paidBills)) {
            Flash::error('Paid Bills not found');

            return redirect(route('paidBills.index'));
        }

        $this->paidBillsRepository->delete($id);

        Flash::success('Paid Bills deleted successfully.');

        return redirect(route('paidBills.index'));
    }

    public function search(Request $request) {
        $results = DB::table('Billing_ServiceAccounts')
            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
            ->where('Billing_ServiceAccounts.ServiceAccountName', 'LIKE', '%' . $request['query'] . '%')
            ->orWhere('Billing_ServiceAccounts.id', 'LIKE', '%' . $request['query'] . '%')
            ->orWhere('Billing_ServiceAccounts.OldAccountNo', 'LIKE', '%' . $request['query'] . '%')
            ->select('Billing_ServiceAccounts.id',
                    'Billing_ServiceAccounts.ServiceAccountName',
                    'Billing_ServiceAccounts.OldAccountNo',
                    'Billing_ServiceAccounts.AccountCount',
                    'Billing_ServiceAccounts.Purok',
                    'Billing_ServiceAccounts.AccountType',
                    'Billing_ServiceAccounts.AccountStatus',
                    'Billing_ServiceAccounts.AreaCode',
                    'Billing_ServiceAccounts.SequenceCode',
                    'CRM_Towns.Town',
                    'CRM_Barangays.Barangay')
            ->orderBy('Billing_ServiceAccounts.ServiceAccountName')
            ->get();

        $output = "";

        if (count($results) > 0) {
            foreach($results as $item) {
                $output .= '
                        <tr onclick=fetchDetails("' . $item->id . '")>
                            <td>' . $item->id . '</td>
                            <td>' . $item->OldAccountNo . '</td>
                            <td>' . $item->ServiceAccountName . '</td>
                            <td>' . ServiceAccounts::getAddress($item) . '</td>
                            <td>' . $item->AccountStatus . '</td>
                            <td>
                                <button class="btn btn-link text-primary" onclick=fetchDetails("' . $item->id . '")><i class="fas fa-forward"></i></button>
                            </td>
                        </tr>
                    '; 
            }

            return response()->json($output, 200);
        } else {
            return response()->json([], 200);
        }        
    }

    public function fetchDetails(Request $request) {
        $unpaidBills = DB::table('Billing_Bills')
            ->leftJoin('Billing_ServiceAccounts', 'Billing_Bills.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
            ->where('Billing_Bills.AccountNumber', $request['AccountNumber'])
            ->whereNull('Billing_Bills.MergedToCollectible')
            ->whereNotIn('Billing_Bills.id', DB::table('Cashier_PaidBills')->where('AccountNumber', $request['AccountNumber'])->whereNull('Status')->pluck('Cashier_PaidBills.ObjectSourceId'))
            ->select('Billing_ServiceAccounts.ServiceAccountName',
                    'Billing_ServiceAccounts.OldAccountNo',
                    'Billing_ServiceAccounts.AccountCount',
                    'Billing_ServiceAccounts.Purok',
                    'Billing_ServiceAccounts.AccountType',
                    'Billing_ServiceAccounts.AccountStatus',
                    'Billing_ServiceAccounts.AreaCode',
                    'Billing_ServiceAccounts.SequenceCode',
                    'CRM_Towns.Town',
                    'CRM_Barangays.Barangay',
                    'Billing_Bills.*')
            ->orderByDesc('Billing_Bills.ServicePeriod')
            ->get();

        $output = "";

        if (count($unpaidBills) > 0) {
            foreach($unpaidBills as $item) {
                $ewt2 = round((floatval($item->DistributionSystemCharge) + floatval($item->SupplyRetailCustomerCharge) + floatval($item->MeteringRetailCustomerCharge)
                        + floatval($item->LifelineRate) + floatval($item->InterClassCrossSubsidyCharge)) * .02, 2);

                $evat5 = round((floatval($item->DistributionSystemCharge) + floatval($item->SupplyRetailCustomerCharge) + floatval($item->MeteringRetailCustomerCharge)
                        + floatval($item->LifelineRate) + floatval($item->InterClassCrossSubsidyCharge)) * .05, 2);
                if (date('Y-m-d', strtotime($item->DueDate)) < date('Y-m-d')) {
                    // ARREARS

                    // IF ARREARS IS LONG DUE, IT SHOULD FIRST BE UNLOCKED BY ADMINS TO BE ABLE TO PAY
                    if ($item->IsUnlockedForPayment == 'Yes') {
                        $output .= '
                            <tr onclick=addToPayables("' . $item->id . '")>
                                <td>' . $item->BillNumber . '</td>
                                <td>' . date('F Y', strtotime($item->ServicePeriod)) . '</td>
                                <td>' . date('F d, Y', strtotime($item->DueDate)) . '</td>
                                <th class="text-danger">₱ ' . number_format($item->NetAmount, 2) . ' + ' . Bills::getFinalPenalty($item) . '</th>
                                <td class="text-right">
                                    <button id="' . $item->id . '" ischecked="false" additionalCharges="' . $item->AdditionalCharges . '" deductions="' . $item->Deductions . '" surcharge="' . Bills::getFinalPenalty($item) . '" amount="' . $item->NetAmount . '" ewt="' . $ewt2 . '" evat="' . $evat5 . '" class="btn btn-link text-muted" onclick=addToPayables("' . $item->id . '")><i class="fas fa-check-circle"></i></button>
                                </td>
                            </tr>
                        '; 
                    } elseif ($item->IsUnlockedForPayment == 'Requested') {
                        $output .= '
                            <tr>
                                <td>' . $item->BillNumber . '</td>
                                <td>' . date('F Y', strtotime($item->ServicePeriod)) . '</td>
                                <td>' . date('F d, Y', strtotime($item->DueDate)) . '</td>
                                <th class="text-danger">₱ ' . number_format($item->NetAmount, 2) . ' + ' . Bills::getFinalPenalty($item) . '</th>
                                <td class="text-right">
                                    <button id="' . $item->id . '" ischecked="false" additionalCharges="' . $item->AdditionalCharges . '" deductions="' . $item->Deductions . '" surcharge="' . Bills::getFinalPenalty($item) . '" amount="' . $item->NetAmount . '" ewt="' . $ewt2 . '" evat="' . $evat5 . '" class="btn btn-link text-muted"><i class="fas fa-exclamation-circle"></i></button>
                                </td>
                            </tr>
                        ';
                    }  else {
                        $output .= '
                            <tr onclick=addToPayables("' . $item->id . '")>
                                <td>' . $item->BillNumber . '</td>
                                <td>' . date('F Y', strtotime($item->ServicePeriod)) . '</td>
                                <td>' . date('F d, Y', strtotime($item->DueDate)) . '</td>
                                <th>₱ ' . number_format($item->NetAmount, 2) . ' + ' . Bills::getFinalPenalty($item) . '</th>
                                <td class="text-right">
                                    <button id="' . $item->id . '" ischecked="false" additionalCharges="' . $item->AdditionalCharges . '" deductions="' . $item->Deductions . '" surcharge="' . Bills::getFinalPenalty($item) . '" amount="' . $item->NetAmount . '" ewt="' . $ewt2 . '" evat="' . $evat5 . '" class="btn btn-link text-muted" onclick=addToPayables("' . $item->id . '")><i class="fas fa-check-circle"></i></button>
                                </td>
                            </tr>
                        '; 
                    }                  
                } else {
                    // PROMPT PAYMENTS
                    $output .= '
                        <tr onclick=addToPayables("' . $item->id . '")>
                            <td>' . $item->BillNumber . '</td>
                            <td>' . date('F Y', strtotime($item->ServicePeriod)) . '</td>
                            <td>' . date('F d, Y', strtotime($item->DueDate)) . '</td>
                            <th>₱ ' . number_format($item->NetAmount, 2) . '</th>
                            <td class="text-right">
                                <button id="' . $item->id . '" ischecked="false" additionalCharges="' . $item->AdditionalCharges . '" deductions="' . $item->Deductions . '" amount="' . $item->NetAmount . '" ewt="' . $ewt2 . '" evat="' . $evat5 . '" class="btn btn-link text-muted" onclick=addToPayables("' . $item->id . '")><i class="fas fa-check-circle"></i></button>
                            </td>
                        </tr>
                    '; 
                }                
            }

            return response()->json($output, 200);
        } else {
            return response()->json([], 200);
        }            
    }

    public function fetchAccount(Request $request) {
        $account = DB::table('Billing_ServiceAccounts')
            ->where('id', $request['AccountNumber'])
            ->first();

        return response()->json($account, 200);
    }

    public function fetchPayable(Request $request) {
        $bill = Bills::find($request['BillId']);

        if ($bill != null) {
            return response()->json($bill, 200);
        } else {
            return response()->json(['res' => 'Bill not found'], 404);
        }
    }

    public function savePaidBillAndPrint(Request $request) {
        $billsId = $request['BillsId'];
        $len = count($billsId);

        for ($i=0; $i<$len; $i++) {
            $bill = Bills::find($billsId[$i]);

            if ($bill != null) {
                $account = ServiceAccounts::find($bill->AccountNumber);

                $paidBill = new PaidBills;
                $paidBill->id = IDGenerator::generateIDandRandString();
                $paidBill->BillNumber = $bill->BillNumber;
                $paidBill->AccountNumber = $bill->AccountNumber;
                $paidBill->ServicePeriod = $bill->ServicePeriod;
                $paidBill->KwhUsed = $bill->KwhUsed;
                $paidBill->Teller = Auth::id();
                $paidBill->OfficeTransacted = env('APP_LOCATION');
                $paidBill->PostingDate = date('Y-m-d');
                $paidBill->PostingTime = date('H:i:s');

                $paidBill->PaymentUsed = $request['PaymentUsed'];
                // CHECK IF PAYMENT IS CASH, CHECK, OR CARD
                // if ($request['PaymentUsed'] == 'Check') {
                //     $paidBill->CheckNo = $request['CheckNo'];
                //     $paidBill->Bank = $request['Bank'];
                // }

                if (date('Y-m-d', strtotime($bill->DueDate)) < date('Y-m-d')) {
                    $paidBill->Surcharge = Bills::getFinalPenalty($bill);
                } else {
                    $paidBill->Surcharge = "0";
                }
               
                // COMPUTE EWT 2%
                $ewt = 0;
                $evat = 0;
                if ($request['Ewt'] == 'true') {
                    $ewt = round((floatval($bill->DistributionSystemCharge) + floatval($bill->SupplyRetailCustomerCharge) + floatval($bill->MeteringRetailCustomerCharge)
                        + floatval($bill->LifelineRate) + floatval($bill->InterClassCrossSubsidyCharge)) * .02, 2);
                }

                // COMPUTE VAT 5%
                if ($request['VAT'] == 'true') {
                    $evat = round((floatval($bill->DistributionSystemCharge) + floatval($bill->SupplyRetailCustomerCharge) + floatval($bill->MeteringRetailCustomerCharge)
                        + floatval($bill->LifelineRate) + floatval($bill->InterClassCrossSubsidyCharge)) * .05, 2);
                }

                $paidBill->AdditionalCharges = $bill->AdditionalCharges;
                $paidBill->Deductions = $bill->Deductions;
                $paidBill->NetAmount = round((floatval($bill->NetAmount) + floatval($paidBill->Surcharge) + $evat) - $ewt, 2);
                $paidBill->Form2307TwoPercent = $ewt;
                $paidBill->Form2307FivePercent = $evat;
                $paidBill->Source = 'MONTHLY BILL';
                $paidBill->ObjectSourceId = $bill->id;
                $paidBill->ORNumber = $request['ORNumber'];
                $paidBill->ORDate = date('Y-m-d');
                $paidBill->UserId = Auth::id();
                $paidBill->save();

                /**
                 * SAVE DCR AND SALES REPORT
                 */
                if ($account != null) {                    
                    if ($account->ForDistribution == 'Yes') {
                        // IF ACCOUNT IS MARKED AS FOR DISTRIBUTION
                        if ($account->DistributionAccountCode != null) {
                            // GET AR CONSUMERS
                            $dcrSum = new DCRSummaryTransactions;
                            $dcrSum->id = IDGenerator::generateIDandRandString();
                            $dcrSum->GLCode = $account->DistributionAccountCode;
                            $dcrSum->Amount = DCRSummaryTransactions::getARConsumersAmount($bill);
                            $dcrSum->Day = date('Y-m-d');
                            $dcrSum->Time = date('H:i:s');
                            $dcrSum->Teller = Auth::id();
                            $dcrSum->ORNumber = $request['ORNumber'];
                            $dcrSum->ReportDestination = 'BOTH';
                            $dcrSum->Office = env('APP_LOCATION');
                            $dcrSum->AccountNumber = $bill->AccountNumber;
                            $dcrSum->save();
                        }                        
                    } else {
                        // GET AR CONSUMERS
                        $dcrSum = new DCRSummaryTransactions;
                        $dcrSum->id = IDGenerator::generateIDandRandString();
                        $dcrSum->GLCode = DCRSummaryTransactions::getARConsumers($account->Town);
                        $dcrSum->Amount = DCRSummaryTransactions::getARConsumersAmount($bill);
                        $dcrSum->Day = date('Y-m-d');
                        $dcrSum->Time = date('H:i:s');
                        $dcrSum->Teller = Auth::id();
                        $dcrSum->ORNumber = $request['ORNumber'];
                        $dcrSum->ReportDestination = 'COLLECTION';
                        $dcrSum->Office = env('APP_LOCATION');
                        $dcrSum->AccountNumber = $bill->AccountNumber;
                        $dcrSum->save();

                        // GET RPT FOR DCR
                        $dcrSum = new DCRSummaryTransactions;
                        $dcrSum->id = IDGenerator::generateIDandRandString();
                        $dcrSum->GLCode = DCRSummaryTransactions::getARConsumersRPT($account->Town);
                        $dcrSum->Amount = $bill->RealPropertyTax;
                        $dcrSum->Day = date('Y-m-d');
                        $dcrSum->Time = date('H:i:s');
                        $dcrSum->Teller = Auth::id();
                        $dcrSum->ORNumber = $request['ORNumber'];
                        $dcrSum->ReportDestination = 'COLLECTION';
                        $dcrSum->Office = env('APP_LOCATION');
                        $dcrSum->AccountNumber = $bill->AccountNumber;
                        $dcrSum->save();

                        // GET RPT  FOR SALES
                        $dcrSum = new DCRSummaryTransactions;
                        $dcrSum->id = IDGenerator::generateIDandRandString();
                        $dcrSum->GLCode = '140-143-30';
                        $dcrSum->Amount = $bill->RealPropertyTax;
                        $dcrSum->Day = date('Y-m-d');
                        $dcrSum->Time = date('H:i:s');
                        $dcrSum->Teller = Auth::id();
                        $dcrSum->ORNumber = $request['ORNumber'];
                        $dcrSum->ReportDestination = 'SALES';
                        $dcrSum->Office = env('APP_LOCATION');
                        $dcrSum->AccountNumber = $bill->AccountNumber;
                        $dcrSum->save();

                        // GET SALES AR BY CONSUMER TYPE 
                        if ($account->OrganizationParentAccount != null) {
                            // GET BAPA
                            $dcrSum = new DCRSummaryTransactions;
                            $dcrSum->id = IDGenerator::generateIDandRandString();
                            $dcrSum->GLCode = '311-448-00';
                            $dcrSum->Amount = DCRSummaryTransactions::getARConsumersAmount($bill);
                            $dcrSum->Day = date('Y-m-d');
                            $dcrSum->Time = date('H:i:s');
                            $dcrSum->Teller = Auth::id();
                            $dcrSum->ORNumber = $request['ORNumber'];
                            $dcrSum->ReportDestination = 'SALES';
                            $dcrSum->Office = env('APP_LOCATION');
                            $dcrSum->AccountNumber = $bill->AccountNumber;
                            $dcrSum->save();
                        } else {
                            // GET NOT BAPA
                            if ($account->AccountType == 'RURAL RESIDENTIAL' || $account->AccountType == 'RESIDENTIAL') {
                                // GET RESIDENTIALS
                                $dcrSum = new DCRSummaryTransactions;
                                $dcrSum->id = IDGenerator::generateIDandRandString();
                                $dcrSum->GLCode = DCRSummaryTransactions::getARConsumers($account->Town);;
                                $dcrSum->Amount = DCRSummaryTransactions::getARConsumersAmount($bill);
                                $dcrSum->Day = date('Y-m-d');
                                $dcrSum->Time = date('H:i:s');
                                $dcrSum->Teller = Auth::id();
                                $dcrSum->ORNumber = $request['ORNumber'];
                                $dcrSum->ReportDestination = 'SALES';
                                $dcrSum->Office = env('APP_LOCATION');
                                $dcrSum->AccountNumber = $bill->AccountNumber;
                                $dcrSum->save();
                            } else {
                                // GET NOT RESIDENTIALS
                                $dcrSum = new DCRSummaryTransactions;
                                $dcrSum->id = IDGenerator::generateIDandRandString();
                                $dcrSum->GLCode = DCRSummaryTransactions::getGLCodePerAccountType($account->AccountType);;
                                $dcrSum->Amount = DCRSummaryTransactions::getARConsumersAmount($bill);
                                $dcrSum->Day = date('Y-m-d');
                                $dcrSum->Time = date('H:i:s');
                                $dcrSum->Teller = Auth::id();
                                $dcrSum->ORNumber = $request['ORNumber'];
                                $dcrSum->ReportDestination = 'SALES';
                                $dcrSum->Office = env('APP_LOCATION');
                                $dcrSum->AccountNumber = $bill->AccountNumber;
                                $dcrSum->save();
                            }
                        }
                    }

                    // GET TERMED PAYMENT BUNDLES
                    if ($bill->AdditionalCharges != null) {
                        // GET TERMED PAYMENT
                        $termedPayment = ArrearsLedgerDistribution::where('AccountNumber', $account->id)
                            ->where('ServicePeriod', $bill->ServicePeriod)
                            ->whereNull('IsPaid')
                            ->first();

                        if ($termedPayment != null) {
                            $dcrSum = new DCRSummaryTransactions;
                            $dcrSum->id = IDGenerator::generateIDandRandString();
                            $dcrSum->GLCode = DCRSummaryTransactions::getARConsumersTermedPayments($account->Town);
                            $dcrSum->Amount = $termedPayment->Amount;
                            $dcrSum->Day = date('Y-m-d');
                            $dcrSum->Time = date('H:i:s');
                            $dcrSum->Teller = Auth::id();
                            $dcrSum->ORNumber = $request['ORNumber'];
                            $dcrSum->ReportDestination = 'COLLECTION';
                            $dcrSum->Office = env('APP_LOCATION');
                            $dcrSum->AccountNumber = $bill->AccountNumber;
                            $dcrSum->save();

                            $termedPayment->IsPaid = 'Yes';
                            $termedPayment->save();
                        }
                    }
                }

                // GET UC-NPC Stranded Debt COLLECTION
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '140-142-87';
                $dcrSum->Amount = $bill->NPCStrandedDebt;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $request['ORNumber'];
                $dcrSum->ReportDestination = 'COLLECTION';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET UC-NPC Stranded Debt Sales
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '230-232-65';
                $dcrSum->Amount = $bill->NPCStrandedDebt;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $request['ORNumber'];
                $dcrSum->ReportDestination = 'SALES';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET STRANDED CONTRACT COST COLLECTION
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '140-142-92';
                $dcrSum->Amount = $bill->StrandedContractCosts;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $request['ORNumber'];
                $dcrSum->ReportDestination = 'COLLECTION';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET STRANDED CONTRACT COST SALES
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '230-232-62';
                $dcrSum->Amount = $bill->StrandedContractCosts;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $request['ORNumber'];
                $dcrSum->ReportDestination = 'SALES';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET FIT ALL COLLECTION
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '140-142-88';
                $dcrSum->Amount = $bill->FeedInTariffAllowance;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $request['ORNumber'];
                $dcrSum->ReportDestination = 'COLLECTION';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET FIT ALL SALES
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '230-232-64';
                $dcrSum->Amount = $bill->FeedInTariffAllowance;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $request['ORNumber'];
                $dcrSum->ReportDestination = 'SALES';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET UCME REDCI COLLECTION
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '140-142-89';
                $dcrSum->Amount = $bill->MissionaryElectrificationREDCI;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $request['ORNumber'];
                $dcrSum->ReportDestination = 'COLLECTION';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET UCME REDCI SALES
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '230-232-63';
                $dcrSum->Amount = $bill->MissionaryElectrificationREDCI;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $request['ORNumber'];
                $dcrSum->ReportDestination = 'SALES';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET GENCO
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '140-142-94';
                $dcrSum->Amount = $bill->GenerationVAT;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $request['ORNumber'];
                $dcrSum->ReportDestination = 'COLLECTION';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET TRANSCO
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '140-142-95';
                $dcrSum->Amount = $bill->TransmissionVAT;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $request['ORNumber'];
                $dcrSum->ReportDestination = 'COLLECTION';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET SYSLOSS VAT
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '140-142-96';
                $dcrSum->Amount = $bill->SystemLossVAT;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $request['ORNumber'];
                $dcrSum->ReportDestination = 'COLLECTION';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET DIST/OTHERS VAT
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '140-142-97';
                $dcrSum->Amount = $bill->DistributionVAT;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $request['ORNumber'];
                $dcrSum->ReportDestination = 'COLLECTION';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET GENVAT, TRANSVAT, SYSLOSSVAT SALES
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '170-184-40';
                $dcrSum->Amount = DCRSummaryTransactions::getSalesGenTransSysLossVatAmount($bill);
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $request['ORNumber'];
                $dcrSum->ReportDestination = 'SALES';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET DIST AND OTHERS SALES
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '250-255-00';
                $dcrSum->Amount = DCRSummaryTransactions::getSalesDistOthersVatAmount($bill);
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $request['ORNumber'];
                $dcrSum->ReportDestination = 'SALES';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET UCME COLLECTION
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '140-142-98';
                $dcrSum->Amount = $bill->MissionaryElectrificationCharge;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $request['ORNumber'];
                $dcrSum->ReportDestination = 'COLLECTION';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET UCME SALES
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '230-232-60';
                $dcrSum->Amount = $bill->MissionaryElectrificationCharge;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $request['ORNumber'];
                $dcrSum->ReportDestination = 'SALES';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET EWT 2%
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '140-160-00';
                $dcrSum->Amount = $paidBill->Form2307TwoPercent;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $request['ORNumber'];
                $dcrSum->ReportDestination = 'COLLECTION';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET EVAT 5%
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '140-170-00';
                $dcrSum->Amount = $paidBill->Form2307FivePercent;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $request['ORNumber'];
                $dcrSum->ReportDestination = 'COLLECTION';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET ENVIRONMENT CHARGE COLLECTION
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '140-142-99';
                $dcrSum->Amount = $bill->EnvironmentalCharge;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $request['ORNumber'];
                $dcrSum->ReportDestination = 'COLLECTION';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET ENVIRONMENT CHARGE SALES
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '230-232-90';
                $dcrSum->Amount = $bill->EnvironmentalCharge;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $request['ORNumber'];
                $dcrSum->ReportDestination = 'SALES';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET RFSC COLLECTION
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '140-142-93';
                $dcrSum->Amount = $bill->RFSC;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $request['ORNumber'];
                $dcrSum->ReportDestination = 'COLLECTION';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET RFSC SALES
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '211-211-10';
                $dcrSum->Amount = $bill->RFSC;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $request['ORNumber'];
                $dcrSum->ReportDestination = 'SALES';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();
            }            
        }  

        // SAVE PAIDBILL DETAILS
        if ($request['PaymentUsed'] == 'Cash and Check') {
            $paidBillDetails = new PaidBillsDetails;
            $paidBillDetails->id = IDGenerator::generateIDandRandString();
            $paidBillDetails->AccountNumber = $request['AccountNumber'];
            $paidBillDetails->ORNumber = $request['ORNumber'];
            $paidBillDetails->Amount = $request['CashAmount'];
            $paidBillDetails->PaymentUsed = 'Cash';
            $paidBillDetails->UserId = Auth::id();
            $paidBillDetails->save();
        } elseif ($request['PaymentUsed'] == 'Cash') {
            $paidBillDetails = new PaidBillsDetails;
            $paidBillDetails->id = IDGenerator::generateIDandRandString();
            $paidBillDetails->AccountNumber = $request['AccountNumber'];
            $paidBillDetails->ORNumber = $request['ORNumber'];
            $paidBillDetails->Amount = $bill->NetAmount;
            $paidBillDetails->PaymentUsed = 'Cash';
            $paidBillDetails->UserId = Auth::id();
            $paidBillDetails->save();
        }

        // DELETE CHECK ITEMS FROM PaidBillDetails that are not on the CheckIds
        $checkIds = $request['CheckIds'];
        if ($checkIds != null) {
            PaidBillsDetails::where('ORNumber', $request['ORNumber'])
                ->where('PaymentUsed', 'Check')
                // ->where('AccountNumber', $request['AccountNumber'])
                ->whereNotIn('id', $checkIds)
                ->delete();
        }        
        
        // SAVE OR
        $saveOR = ORAssigning::where('ORNumber', $request['ORNumber'])
            ->whereRaw("UserId='" . Auth::id() . "'")
            ->first();        
        if ($saveOR == null) {
            $saveOR = new ORAssigning;
            $saveOR->id = IDGenerator::generateIDandRandString();
            $saveOR->ORNumber = $request['ORNumber'];
            $saveOR->UserId = Auth::id();
            $saveOR->DateAssigned = date('Y-m-d');
            $saveOR->TimeAssigned = date('H:i:s');
            $saveOR->Office = env('APP_LOCATION');
            $saveOR->save();
        }  

        return response()->json(['ORNumber' => $request['ORNumber'], 'Teller' => Auth::id()], 200);
    }

    public function printBillPayment($paidBillId) {
        $paidBill = PaidBills::where('ORNumber', $paidBillId)->get();

        $paidBillSingle = PaidBills::where('ORNumber', $paidBillId)->first();
    
        if ($paidBillSingle != null) {
            $account = DB::table('Billing_ServiceAccounts')
            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
            ->select('Billing_ServiceAccounts.id',
                    'Billing_ServiceAccounts.ServiceAccountName',
                    'Billing_ServiceAccounts.OldAccountNo',
                    'Billing_ServiceAccounts.AccountCount',
                    'Billing_ServiceAccounts.Purok',
                    'Billing_ServiceAccounts.AccountType',
                    'Billing_ServiceAccounts.AccountStatus',
                    'Billing_ServiceAccounts.AreaCode',
                    'Billing_ServiceAccounts.SequenceCode',
                    'Billing_ServiceAccounts.ForDistribution',
                    'Billing_ServiceAccounts.Organization',
                    'Billing_ServiceAccounts.Main',
                    'Billing_ServiceAccounts.GroupCode',
                    'Billing_ServiceAccounts.Multiplier',
                    'Billing_ServiceAccounts.Coreloss',
                    'Billing_ServiceAccounts.ConnectionDate',
                    'Billing_ServiceAccounts.ServiceConnectionId',
                    'Billing_ServiceAccounts.SeniorCitizen',
                    'Billing_ServiceAccounts.Evat5Percent',
                    'Billing_ServiceAccounts.Ewt2Percent',
                    'Billing_ServiceAccounts.Contestable',
                    'Billing_ServiceAccounts.NetMetered',
                    'Billing_ServiceAccounts.AccountRetention',
                    'Billing_ServiceAccounts.DurationInMonths',
                    'Billing_ServiceAccounts.AccountExpiration',
                    'CRM_Towns.Town',
                    'CRM_Barangays.Barangay')
            ->where('Billing_ServiceAccounts.id', $paidBillSingle->AccountNumber)
            ->first();
            $meter = BillingMeters::where('ServiceAccountId', $paidBillSingle->AccountNumber)
                ->orderByDesc('created_at')
                ->first();
        }

        $user = Auth::user();

        return view('/paid_bills/print_bill_payment', [
            'paidBill' => $paidBill,
            'paidBillSingle' => $paidBillSingle,
            'account' => $account,
            'meter' => $meter,
            'user' => $user,
        ]);
    }

    public function orCancellation() {
        return view('/paid_bills/or_cancellation', [

        ]);
    }

    public function searchOR(Request $request) {
        $regex = $request['query'];
        
        $results = DB::table('Cashier_PaidBills')
            ->leftJoin('Billing_ServiceAccounts', 'Cashier_PaidBills.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->whereNull('Cashier_PaidBills.Status')
            ->where(function($query) use ($regex) {
                $query->where('Billing_ServiceAccounts.ServiceAccountName', 'LIKE', '%' . $regex . '%')
                    ->orWhere('Cashier_PaidBills.AccountNumber', 'LIKE', '%' . $regex . '%')
                    ->orWhere('Billing_ServiceAccounts.OldAccountNo', 'LIKE', '%' . $regex . '%')
                    ->orWhere('Cashier_PaidBills.BillNumber', 'LIKE', '%' . $regex . '%')
                    ->orWhere('Cashier_PaidBills.ORNumber', 'LIKE', '%' . $regex . '%');
            })            
            ->select('Cashier_PaidBills.AccountNumber',
                'Cashier_PaidBills.ORNumber',
                'Cashier_PaidBills.ORDate',
                'Billing_ServiceAccounts.ServiceAccountName',)
            ->groupBy('Cashier_PaidBills.ORNumber',
                'Cashier_PaidBills.ORDate',
                'Billing_ServiceAccounts.ServiceAccountName',
                'Cashier_PaidBills.AccountNumber')
            ->get();

        $output = "";
        foreach($results as $item) {
            $output .= '<tr onclick=fetchDetails("' . $item->ORNumber . '")>
                            <td>' . $item->ORNumber . '</td>
                            <td>' . $item->AccountNumber . '</td>
                            <td>' . $item->ServiceAccountName . '</td>
                            <td>' . $item->ORDate . '</td>
                        </tr>';

        }

        return response()->json($output, 200);
    }

    public function fetchORDetails(Request $request) {
        $orNo = $request['orNo'];

        $paidBill = DB::table('Cashier_PaidBills')
            ->leftJoin('Billing_ServiceAccounts', 'Cashier_PaidBills.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->leftJoin('users', 'Cashier_PaidBills.Teller', '=', 'users.id')
            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
            ->where('Cashier_PaidBills.ORNumber', $orNo)
            ->whereNull('Cashier_PaidBills.Status')
            ->select('Cashier_PaidBills.id', 
                'Cashier_PaidBills.ORNumber',
                'Cashier_PaidBills.ORDate',
                'Cashier_PaidBills.PostingDate',
                'Cashier_PaidBills.PostingTime',
                'Cashier_PaidBills.KwhUsed',
                'Cashier_PaidBills.BillNumber',
                'Cashier_PaidBills.ServicePeriod',
                'Cashier_PaidBills.AdditionalCharges',
                'Cashier_PaidBills.Deductions',
                'Cashier_PaidBills.NetAmount',
                'Cashier_PaidBills.ObjectSourceId',
                'users.name',
                'Billing_ServiceAccounts.ServiceAccountName',
                'Billing_ServiceAccounts.id as AccountNumber',
                'CRM_Towns.Town',
                'CRM_Barangays.Barangay',
                'Billing_ServiceAccounts.Purok')
            ->get();
        
        $output = "";
        $total = 0;
        foreach($paidBill as $item) {
            $output .= '<tr>
                            <td>' . $item->BillNumber . '</td>
                            <td>' . $item->ServiceAccountName . '</td>
                            <td>' . date('M d, Y', strtotime($item->ServicePeriod)) . '</td>
                            <td>' . $item->ORNumber . '</td>                            
                            <td>' . $item->ORDate . '</td>                            
                            <td>' . number_format($item->NetAmount, 2) . '</td>
                        </tr>';

            
            $total += floatval($item->NetAmount);
        }

        $output .= '<tr>
                            <th>Total</th>
                            <th></th>
                            <th></th>                            
                            <th></th>                            
                            <th></th>                            
                            <th>' . number_format($total, 2) . '</th>
                        </tr>';

        return response()->json($output, 200);
    }

    public function requestCancelOR(Request $request) {
        $orNo = $request['orNo'];

        $paidBill = PaidBills::where('ORNumber', $orNo)->get();

        if (count($paidBill) > 0) {
            foreach ($paidBill as $item) {
                $item->Status = 'PENDING CANCEL';
                $item->FiledBy = Auth::id();
                $item->Notes = $request['Notes'];
                $item->save();

                // SAVE TO OR CANCELLATIONS
                $cancellation = new ORCancellations;
                $cancellation->id = IDGenerator::generateIDandRandString();
                $cancellation->ORNumber = $item->ORNumber;
                $cancellation->ORDate = $item->ORDate;
                $cancellation->From = 'PaidBills';
                $cancellation->ObjectId = $item->id;
                $cancellation->DateTimeFiled = date('Y-m-d H:i:s');
                $cancellation->save();
            }

            // ADD NOTIFICATION
            $notifier = new Notifiers;
            $notifier->id = IDGenerator::generateIDandRandString();
            $notifier->Notification = 'OR Cancellation requested by ' . Auth::user()->name . ' with OR Number ' . $orNo;
            $notifier->From = Auth::id();
            $notifier->To = env('APP_CASHIER_HEAD_ID');
            $notifier->Status = 'SENT';
            $notifier->Intent = "OR CANCELLATION"; 
            $notifier->IntentLink = ""; // change later to or cancellation confirmations
            $notifier->ObjectId = $orNo;
            $notifier->save();

            return response()->json('ok', 200);
        } else {
            return response()->json('paid bill not found', 404);
        }
    }

    public function requestBillsPaymentUnlock(Request $request) {
        $bill = Bills::find($request['id']);

        if ($bill != null) {
            $bill->IsUnlockedForPayment = 'Requested';
            $bill->UnlockedBy = Auth::id();
            $bill->save();

            // ADD NOTIFICATION
            $notifier = new Notifiers;
            $notifier->id = IDGenerator::generateIDandRandString();
            $notifier->Notification = 'Bill unlock requested for bill number ' . $bill->BillNumber;
            $notifier->From = Auth::id();
            $notifier->To = env('APP_BILLING_ANALYST_ID');
            $notifier->Status = 'SENT';
            $notifier->Intent = "BILL ARREAR PAYMENT UNLOCKING"; 
            $notifier->IntentLink = ""; // change later to or cancellation confirmations
            $notifier->ObjectId = $bill->id;
            $notifier->save();
        }

        return response()->json($bill, 200);
    }

    public function bapaPayments() {
        $towns = Towns::orderBy('id')->get();

        return view('/paid_bills/bapa_payments', [
            'towns' => $towns,
        ]);
    }

    public function searchBapa(Request $request) {
        $param = $request['BAPA'];
        $town = $request['Town'];

        if ($town == 'All') {
            $bapas = DB::table('Billing_ServiceAccounts AS sa')
            ->where('sa.OrganizationParentAccount', 'LIKE', '%' . $param . '%')
            ->select('sa.OrganizationParentAccount', 
                'sa.Town',
                DB::raw("COUNT(sa.id) AS NoOfAccounts"),
                DB::raw("(SELECT SUBSTRING((SELECT ',' + AreaCode AS 'data()' FROM Billing_ServiceAccounts WHERE OrganizationParentAccount=sa.OrganizationParentAccount GROUP BY AreaCode FOR XML PATH('')), 2 , 9999)) As Result"))
            ->groupBy('sa.OrganizationParentAccount', 
                'sa.Town')
            ->orderBy('sa.OrganizationParentAccount')
            ->get();
        } else {
            $bapas = DB::table('Billing_ServiceAccounts AS sa')
            ->where('sa.OrganizationParentAccount', 'LIKE', '%' . $param . '%')
            ->where('sa.Town', $town)
            ->select('sa.OrganizationParentAccount', 
                'sa.Town',
                DB::raw("COUNT(sa.id) AS NoOfAccounts"),
                DB::raw("(SELECT SUBSTRING((SELECT ',' + AreaCode AS 'data()' FROM Billing_ServiceAccounts WHERE OrganizationParentAccount=sa.OrganizationParentAccount GROUP BY AreaCode FOR XML PATH('')), 2 , 9999)) As Result"))
            ->groupBy('sa.OrganizationParentAccount', 
                'sa.Town')
            ->orderBy('sa.OrganizationParentAccount')
            ->get();
        }

        $output = "";
        foreach($bapas as $item) {
            if (strlen($item->OrganizationParentAccount) > 1) {
                $output .= '<tr>
                                <td><a href="' . route('paidBills.bapa-payment-console', [urlencode($item->OrganizationParentAccount)]) . '">' . $item->OrganizationParentAccount . '</a></td>
                                <td>' . $item->Town . '</td>
                                <td>' . number_format($item->NoOfAccounts) . '</td>
                                <td>' . $item->Result . '</td>
                            </tr>';
            }
            
        }

        return response()->json($output, 200);
    }

    public function bapaPaymentConsole($bapaName) {
        $bapaName = urldecode($bapaName);

        $orAssignedLast = ORAssigning::whereRaw("UserId='" . Auth::id() . "'")
            ->orderByDesc('created_at')
            ->first();

        return view('/paid_bills/bapa_payment_console', [
            'bapaName' => $bapaName,
            'orAssignedLast' => $orAssignedLast,
        ]);
    }

    // RE-DIRECTED TO BAPA ADJUSTMENTS
    public function getBillsFromBapa(Request $request) {
        $bapaName = $request['BAPAName'];
        $period = $request['Period'];

        $accounts = DB::table('Billing_Bills')
            ->leftJoin('Billing_ServiceAccounts', 'Billing_ServiceAccounts.id', '=', 'Billing_Bills.AccountNumber')
            ->whereRaw("Billing_Bills.ServicePeriod = '" . $period . "'")
            ->where('Billing_ServiceAccounts.OrganizationParentAccount', $bapaName)
            ->whereRaw("Billing_Bills.AccountNumber NOT IN (SELECT AccountNumber FROM Cashier_PaidBills WHERE ServicePeriod = '" . $period ."' AND Status IS NULL)")
            ->whereRaw("Billing_Bills.id NOT IN (SELECT BillId FROM Cashier_BAPAAdjustmentDetails WHERE BillId IS NOT NULL)")
            ->select('Billing_ServiceAccounts.id AS AccountNumber',
                'Billing_ServiceAccounts.ServiceAccountName',
                'Billing_ServiceAccounts.AccountStatus',
                'Billing_ServiceAccounts.OldAccountNo',
                'Billing_Bills.KwhUsed',
                'Billing_Bills.ServicePeriod',
                'Billing_Bills.id as BillId',
                'Billing_Bills.NetAmount',
                // DB::raw("(SELECT TOP 1 id FROM Billing_Bills WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod=Billing_Bills.ServicePeriod) AS BillId"),
                // DB::raw("(SELECT TOP 1 NetAmount FROM Billing_Bills WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod=Billing_Bills.ServicePeriod) AS NetAmount"),
                DB::raw("(SELECT TOP 1 ORNumber FROM Cashier_PaidBills WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod=Billing_Bills.ServicePeriod AND Status IS NULL) AS ORNumber"),)
            ->orderBy('Billing_ServiceAccounts.OldAccountNo')
            ->get();

        return response()->json($accounts, 200);
    }

    // THE NEW BAPA BILLS QUERY
    public function getAdjustedBapaBills(Request $request) {
        $bapaName = $request['BAPAName'];
        $period = $request['Period'];

        $accounts = DB::table('Cashier_BAPAAdjustmentDetails')
            ->leftJoin('Billing_Bills', 'Cashier_BAPAAdjustmentDetails.BillId', '=', 'Billing_Bills.id')
            ->leftJoin('Billing_ServiceAccounts', 'Billing_ServiceAccounts.id', '=', 'Billing_Bills.AccountNumber')
            ->where("Cashier_BAPAAdjustmentDetails.ServicePeriod", $period)
            ->where('Billing_ServiceAccounts.OrganizationParentAccount', $bapaName)
            ->select('Billing_ServiceAccounts.id AS AccountNumber',
                'Billing_ServiceAccounts.ServiceAccountName',
                'Billing_ServiceAccounts.AccountStatus',
                'Billing_ServiceAccounts.OldAccountNo',
                'Billing_Bills.KwhUsed',
                'Billing_Bills.ServicePeriod',
                'Billing_Bills.id',
                'Billing_Bills.BillNumber',
                'Billing_Bills.NetAmount',
                'Cashier_BAPAAdjustmentDetails.DiscountPercentage',
                'Cashier_BAPAAdjustmentDetails.DiscountAmount',
                DB::raw("(SELECT TOP 1 ORNumber FROM Cashier_PaidBills WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod=Billing_Bills.ServicePeriod AND Status IS NULL) AS ORNumber"),)
            ->orderBy('Billing_ServiceAccounts.AccountStatus')
            ->get();

        return response()->json($accounts, 200);
    }

    public function saveBapaPayments(Request $request) {
        $accounts = $request['AccountNumbers'];
        $len = count($accounts);
        $period = $request['Period'];

        $orStart = intval($request['ORNumber']) + 1;
        $dcrNum = 'BAPA-' . IDGenerator::generateID();

        // SAVE EACH TRANSACTION
        for($i=0; $i<$len; $i++) {
            $bill = Bills::find($accounts[$i]);
            $billAdjustment = BAPAAdjustmentDetails::where('BillId', $bill->id)->first();

            $account = ServiceAccounts::find($bill->AccountNumber);

            if ($bill != null) {
                // SAVE OR FIRST
                $saveORNew = new ORAssigning;
                $saveORNew->id = IDGenerator::generateIDandRandString();
                $saveORNew->ORNumber = $orStart;
                $saveORNew->UserId = Auth::id();
                $saveORNew->DateAssigned = date('Y-m-d');
                $saveORNew->TimeAssigned = date('H:i:s');
                $saveORNew->Office = env('APP_LOCATION');
                $saveORNew->save();

                $orStart += 1;

                $paidBill = new PaidBills;
                $paidBill->id = IDGenerator::generateIDandRandString();
                $paidBill->BillNumber = $bill->BillNumber;
                $paidBill->AccountNumber = $bill->AccountNumber;
                $paidBill->ServicePeriod = $bill->ServicePeriod;
                $paidBill->KwhUsed = $bill->KwhUsed;
                $paidBill->Teller = Auth::id();
                $paidBill->OfficeTransacted = env('APP_LOCATION');
                $paidBill->PostingDate = date('Y-m-d');
                $paidBill->PostingTime = date('H:i:s');
                $paidBill->DCRNumber = $dcrNum;
                
                $paidBill->PaymentUsed = $request['PaymentUsed'];
                // CHECK IF PAYMENT IS CASH, CHECK, OR CARD
                if ($request['PaymentUsed'] == 'Check') {
                    $paidBill->CheckNo = $request['CheckNo'];
                    $paidBill->Bank = $request['Bank'];
                }
                if (date('Y-m-d', strtotime($bill->DueDate)) < date('Y-m-d') && ($bill->ConsumerType != 'RESIDENTIAL' || $bill->ConsumerType != 'RURAL RESIDENTIAL')) {
                    $paidBill->Surcharge = Bills::getFinalPenalty($bill);
                } else {
                    $paidBill->Surcharge = "0";
                }            

                $paidBill->AdditionalCharges = $bill->AdditionalCharges;

                if ($billAdjustment != null) {
                    $paidBill->Deductions = round(floatval($bill->Deductions) + floatval($billAdjustment->DiscountAmount), 2);
                } else {
                    $paidBill->Deductions = round(floatval($bill->Deductions), 2);
                }
                
                $paidBill->NetAmount = round((floatval($bill->NetAmount) + floatval($paidBill->Surcharge)) - floatval($paidBill->Deductions), 2);

                $paidBill->Source = 'MONTHLY BILL';
                $paidBill->ObjectSourceId = $bill->id;
                $paidBill->ORNumber = $saveORNew->ORNumber;
                $paidBill->ORDate = date('Y-m-d');
                $paidBill->UserId = Auth::id();
                $paidBill->save();

                /**
                 * SAVE DCR AND SALES REPORT
                 */
                if ($account != null) {                    
                    if ($account->ForDistribution == 'Yes') {
                        // IF ACCOUNT IS MARKED AS FOR DISTRIBUTION
                        if ($account->DistributionAccountCode != null) {
                            // GET AR CONSUMERS
                            $dcrSum = new DCRSummaryTransactions;
                            $dcrSum->id = IDGenerator::generateIDandRandString();
                            $dcrSum->GLCode = $account->DistributionAccountCode;
                            $dcrSum->Amount = DCRSummaryTransactions::getARConsumersAmount($bill);
                            $dcrSum->Day = date('Y-m-d');
                            $dcrSum->Time = date('H:i:s');
                            $dcrSum->Teller = Auth::id();
                            $dcrSum->ORNumber = $request['ORNumber'];
                            $dcrSum->ReportDestination = 'BOTH';
                            $dcrSum->Office = env('APP_LOCATION');
                            $dcrSum->AccountNumber = $bill->AccountNumber;
                            $dcrSum->save();
                        }                        
                    } else {
                        // GET AR CONSUMERS
                        $dcrSum = new DCRSummaryTransactions;
                        $dcrSum->id = IDGenerator::generateIDandRandString();
                        $dcrSum->GLCode = DCRSummaryTransactions::getARConsumers($account->Town);
                        $dcrSum->Amount = DCRSummaryTransactions::getARConsumersAmount($bill);
                        $dcrSum->Day = date('Y-m-d');
                        $dcrSum->Time = date('H:i:s');
                        $dcrSum->Teller = Auth::id();
                        $dcrSum->ORNumber = $request['ORNumber'];
                        $dcrSum->ReportDestination = 'COLLECTION';
                        $dcrSum->Office = env('APP_LOCATION');
                        $dcrSum->AccountNumber = $bill->AccountNumber;
                        $dcrSum->save();

                        // GET RPT FOR DCR
                        $dcrSum = new DCRSummaryTransactions;
                        $dcrSum->id = IDGenerator::generateIDandRandString();
                        $dcrSum->GLCode = DCRSummaryTransactions::getARConsumersRPT($account->Town);
                        $dcrSum->Amount = $bill->RealPropertyTax;
                        $dcrSum->Day = date('Y-m-d');
                        $dcrSum->Time = date('H:i:s');
                        $dcrSum->Teller = Auth::id();
                        $dcrSum->ORNumber = $request['ORNumber'];
                        $dcrSum->ReportDestination = 'COLLECTION';
                        $dcrSum->Office = env('APP_LOCATION');
                        $dcrSum->AccountNumber = $bill->AccountNumber;
                        $dcrSum->save();

                        // GET RPT  FOR SALES
                        $dcrSum = new DCRSummaryTransactions;
                        $dcrSum->id = IDGenerator::generateIDandRandString();
                        $dcrSum->GLCode = '140-143-30';
                        $dcrSum->Amount = $bill->RealPropertyTax;
                        $dcrSum->Day = date('Y-m-d');
                        $dcrSum->Time = date('H:i:s');
                        $dcrSum->Teller = Auth::id();
                        $dcrSum->ORNumber = $request['ORNumber'];
                        $dcrSum->ReportDestination = 'SALES';
                        $dcrSum->Office = env('APP_LOCATION');
                        $dcrSum->AccountNumber = $bill->AccountNumber;
                        $dcrSum->save();

                        // GET SALES AR BY CONSUMER TYPE 
                        if ($account->OrganizationParentAccount != null) {
                            // GET BAPA
                            $dcrSum = new DCRSummaryTransactions;
                            $dcrSum->id = IDGenerator::generateIDandRandString();
                            $dcrSum->GLCode = '311-448-00';
                            $dcrSum->Amount = DCRSummaryTransactions::getARConsumersAmount($bill);
                            $dcrSum->Day = date('Y-m-d');
                            $dcrSum->Time = date('H:i:s');
                            $dcrSum->Teller = Auth::id();
                            $dcrSum->ORNumber = $request['ORNumber'];
                            $dcrSum->ReportDestination = 'SALES';
                            $dcrSum->Office = env('APP_LOCATION');
                            $dcrSum->AccountNumber = $bill->AccountNumber;
                            $dcrSum->save();
                        } else {
                            // GET NOT BAPA
                            if ($account->AccountType == 'RURAL RESIDENTIAL' || $account->AccountType == 'RESIDENTIAL') {
                                // GET RESIDENTIALS
                                $dcrSum = new DCRSummaryTransactions;
                                $dcrSum->id = IDGenerator::generateIDandRandString();
                                $dcrSum->GLCode = DCRSummaryTransactions::getARConsumers($account->Town);;
                                $dcrSum->Amount = DCRSummaryTransactions::getARConsumersAmount($bill);
                                $dcrSum->Day = date('Y-m-d');
                                $dcrSum->Time = date('H:i:s');
                                $dcrSum->Teller = Auth::id();
                                $dcrSum->ORNumber = $request['ORNumber'];
                                $dcrSum->ReportDestination = 'SALES';
                                $dcrSum->Office = env('APP_LOCATION');
                                $dcrSum->AccountNumber = $bill->AccountNumber;
                                $dcrSum->save();
                            } else {
                                // GET NOT RESIDENTIALS
                                $dcrSum = new DCRSummaryTransactions;
                                $dcrSum->id = IDGenerator::generateIDandRandString();
                                $dcrSum->GLCode = DCRSummaryTransactions::getGLCodePerAccountType($account->AccountType);;
                                $dcrSum->Amount = DCRSummaryTransactions::getARConsumersAmount($bill);
                                $dcrSum->Day = date('Y-m-d');
                                $dcrSum->Time = date('H:i:s');
                                $dcrSum->Teller = Auth::id();
                                $dcrSum->ORNumber = $request['ORNumber'];
                                $dcrSum->ReportDestination = 'SALES';
                                $dcrSum->Office = env('APP_LOCATION');
                                $dcrSum->AccountNumber = $bill->AccountNumber;
                                $dcrSum->save();
                            }
                        }
                    }

                    // GET TERMED PAYMENT BUNDLES
                    if ($bill->AdditionalCharges != null) {
                        // GET TERMED PAYMENT
                        $termedPayment = ArrearsLedgerDistribution::where('AccountNumber', $account->id)
                            ->where('ServicePeriod', $bill->ServicePeriod)
                            ->whereNull('IsPaid')
                            ->first();

                        if ($termedPayment != null) {
                            $dcrSum = new DCRSummaryTransactions;
                            $dcrSum->id = IDGenerator::generateIDandRandString();
                            $dcrSum->GLCode = DCRSummaryTransactions::getARConsumersTermedPayments($account->Town);
                            $dcrSum->Amount = $termedPayment->Amount;
                            $dcrSum->Day = date('Y-m-d');
                            $dcrSum->Time = date('H:i:s');
                            $dcrSum->Teller = Auth::id();
                            $dcrSum->ORNumber = $request['ORNumber'];
                            $dcrSum->ReportDestination = 'COLLECTION';
                            $dcrSum->Office = env('APP_LOCATION');
                            $dcrSum->AccountNumber = $bill->AccountNumber;
                            $dcrSum->save();

                            $termedPayment->IsPaid = 'Yes';
                            $termedPayment->save();
                        }
                    }
                }

                // GET UC-NPC Stranded Debt COLLECTION
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '140-142-87';
                $dcrSum->Amount = $bill->NPCStrandedDebt;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $saveORNew->ORNumber;
                $dcrSum->ReportDestination = 'COLLECTION';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET UC-NPC Stranded Debt Sales
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '230-232-65';
                $dcrSum->Amount = $bill->NPCStrandedDebt;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $saveORNew->ORNumber;
                $dcrSum->ReportDestination = 'SALES';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET STRANDED CONTRACT COST COLLECTION
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '140-142-92';
                $dcrSum->Amount = $bill->StrandedContractCosts;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $saveORNew->ORNumber;
                $dcrSum->ReportDestination = 'COLLECTION';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET STRANDED CONTRACT COST SALES
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '230-232-62';
                $dcrSum->Amount = $bill->StrandedContractCosts;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $saveORNew->ORNumber;
                $dcrSum->ReportDestination = 'SALES';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET FIT ALL COLLECTION
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '140-142-88';
                $dcrSum->Amount = $bill->FeedInTariffAllowance;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $saveORNew->ORNumber;
                $dcrSum->ReportDestination = 'COLLECTION';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET FIT ALL SALES
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '230-232-64';
                $dcrSum->Amount = $bill->FeedInTariffAllowance;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $saveORNew->ORNumber;
                $dcrSum->ReportDestination = 'SALES';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET UCME REDCI COLLECTION
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '140-142-89';
                $dcrSum->Amount = $bill->MissionaryElectrificationREDCI;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $saveORNew->ORNumber;
                $dcrSum->ReportDestination = 'COLLECTION';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET UCME REDCI SALES
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '230-232-63';
                $dcrSum->Amount = $bill->MissionaryElectrificationREDCI;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $saveORNew->ORNumber;
                $dcrSum->ReportDestination = 'SALES';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET GENCO
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '140-142-94';
                $dcrSum->Amount = $bill->GenerationVAT;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $saveORNew->ORNumber;
                $dcrSum->ReportDestination = 'COLLECTION';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET TRANSCO
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '140-142-95';
                $dcrSum->Amount = $bill->TransmissionVAT;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $saveORNew->ORNumber;
                $dcrSum->ReportDestination = 'COLLECTION';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET SYSLOSS VAT
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '140-142-96';
                $dcrSum->Amount = $bill->SystemLossVAT;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $saveORNew->ORNumber;
                $dcrSum->ReportDestination = 'COLLECTION';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET DIST/OTHERS VAT
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '140-142-97';
                $dcrSum->Amount = $bill->DistributionVAT;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $saveORNew->ORNumber;
                $dcrSum->ReportDestination = 'COLLECTION';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET GENVAT, TRANSVAT, SYSLOSSVAT SALES
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '170-184-40';
                $dcrSum->Amount = DCRSummaryTransactions::getSalesGenTransSysLossVatAmount($bill);
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $saveORNew->ORNumber;
                $dcrSum->ReportDestination = 'SALES';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET DIST AND OTHERS SALES
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '250-255-00';
                $dcrSum->Amount = DCRSummaryTransactions::getSalesDistOthersVatAmount($bill);
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $saveORNew->ORNumber;
                $dcrSum->ReportDestination = 'SALES';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET UCME COLLECTION
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '140-142-98';
                $dcrSum->Amount = $bill->MissionaryElectrificationCharge;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $saveORNew->ORNumber;
                $dcrSum->ReportDestination = 'COLLECTION';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET UCME SALES
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '230-232-60';
                $dcrSum->Amount = $bill->MissionaryElectrificationCharge;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $saveORNew->ORNumber;
                $dcrSum->ReportDestination = 'SALES';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET EWT 2%
                // $dcrSum = new DCRSummaryTransactions;
                // $dcrSum->id = IDGenerator::generateIDandRandString();
                // $dcrSum->GLCode = '140-160-00';
                // $dcrSum->Amount = $paidBill->Form2307TwoPercent;
                // $dcrSum->Day = date('Y-m-d');
                // $dcrSum->Time = date('H:i:s');
                // $dcrSum->Teller = Auth::id();
                // $dcrSum->ORNumber = $saveORNew->ORNumber;
                // $dcrSum->ReportDestination = 'COLLECTION';
                // $dcrSum->Office = env('APP_LOCATION');
                // $dcrSum->AccountNumber = $bill->AccountNumber;
                // $dcrSum->save();

                // GET EVAT 5%
                // $dcrSum = new DCRSummaryTransactions;
                // $dcrSum->id = IDGenerator::generateIDandRandString();
                // $dcrSum->GLCode = '140-170-00';
                // $dcrSum->Amount = $paidBill->Form2307FivePercent;
                // $dcrSum->Day = date('Y-m-d');
                // $dcrSum->Time = date('H:i:s');
                // $dcrSum->Teller = Auth::id();
                // $dcrSum->ORNumber = $saveORNew->ORNumber;
                // $dcrSum->ReportDestination = 'COLLECTION';
                // $dcrSum->Office = env('APP_LOCATION');
                // $dcrSum->AccountNumber = $bill->AccountNumber;
                // $dcrSum->save();

                // GET ENVIRONMENT CHARGE COLLECTION
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '140-142-99';
                $dcrSum->Amount = $bill->EnvironmentalCharge;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $saveORNew->ORNumber;
                $dcrSum->ReportDestination = 'COLLECTION';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET ENVIRONMENT CHARGE SALES
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '230-232-90';
                $dcrSum->Amount = $bill->EnvironmentalCharge;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $saveORNew->ORNumber;
                $dcrSum->ReportDestination = 'SALES';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET RFSC COLLECTION
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '140-142-93';
                $dcrSum->Amount = $bill->RFSC;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $saveORNew->ORNumber;
                $dcrSum->ReportDestination = 'COLLECTION';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET RFSC SALES
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '211-211-10';
                $dcrSum->Amount = $bill->RFSC;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $saveORNew->ORNumber;
                $dcrSum->ReportDestination = 'SALES';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();
            }
        }

        // SAVE PAYMENT TO BAPA Payments
        $bapaPayments = new BAPAPayments;
        $bapaPayments->id = IDGenerator::generateIDandRandString();
        $bapaPayments->BAPAName = urldecode($request['BAPAName']);
        $bapaPayments->ServicePeriod = $period;
        // $bapaPayments->ORNumber = $saveORNew->ORNumber;
        $bapaPayments->ORDate = date('Y-m-d');
        $bapaPayments->SubTotal = round(floatval($request['SubTotal']), 2);
        $bapaPayments->TwoPercentDiscount = round(floatval($request['DiscountAmount']), 2);
        // $bapaPayments->FivePercentDiscount = round(floatval($request['Discount5']), 2);
        $bapaPayments->Total = round(floatval($request['TotalAmountPaid']), 2);
        $bapaPayments->Teller = Auth::id();
        $bapaPayments->NoOfConsumersPaid = $len;
        $bapaPayments->save(); 

        return response()->json($dcrNum, 200);
    }

    public function printBapaPayments($dcrNum) {
        $paidBills = DB::table('Cashier_PaidBills')
            ->leftJoin('Billing_ServiceAccounts', 'Cashier_PaidBills.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
            ->where('Cashier_PaidBills.DCRNumber', $dcrNum)
            ->select('Cashier_PaidBills.*',
                'Billing_ServiceAccounts.ServiceAccountName',
                'Billing_ServiceAccounts.Purok',
                'Billing_ServiceAccounts.OldAccountNo',
                'Billing_ServiceAccounts.AccountType',
                'Billing_ServiceAccounts.AccountStatus',
                'CRM_Towns.Town',
                'CRM_Barangays.Barangay',
                DB::raw("(SELECT TOP 1 SerialNumber FROM Billing_Meters WHERE ServiceAccountId=Billing_ServiceAccounts.id AND created_at IS NOT NULL ORDER BY created_at DESC) AS MeterNumber"))
            ->get();

        return view('/paid_bills/print_bapa_payments', [
            'paidBills' => $paidBills
        ]);
    }

    public function billsCollection() {
        // $paidBills = $this->paidBillsRepository->all();
        $orAssignedLast = ORAssigning::whereRaw("UserId='" . Auth::id() . "'")
            ->orderByDesc('created_at')
            ->first();

        return view('paid_bills.index', [
            // 'paidBills' => $paidBills,
            'orAssignedLast' => $orAssignedLast,
        ]);
    }

    public function addCheckPayments(Request $request) {
        $paidBillDetails = new PaidBillsDetails;
        $paidBillDetails->id = IDGenerator::generateIDandRandString();
        $paidBillDetails->AccountNumber = $request['AccountNumber'];
        $paidBillDetails->ServicePeriod = $request['ServicePeriod'];
        $paidBillDetails->BillId = $request['Billid'];
        $paidBillDetails->ORNumber = $request['ORNumber'];
        $paidBillDetails->Amount = $request['Amount'];
        $paidBillDetails->PaymentUsed = 'Check';
        $paidBillDetails->CheckNo = $request['CheckNo'];
        $paidBillDetails->Bank = $request['Bank'];
        $paidBillDetails->CheckExpiration = $request['CheckExpiration'];
        $paidBillDetails->UserId = Auth::id();
        $paidBillDetails->save();

        return response()->json($paidBillDetails, 200);
    }

    public function deleteCheckPayment(Request $request) {
        $paidBillDetails = PaidBillsDetails::find($request['id']);

        $paidBillDetails->delete();

        return response()->json($paidBillDetails, 200);
    }

    public function fetchAccountByOldAccountNumber(Request $request) {
        $account = ServiceAccounts::where('OldAccountNo', $request['OldAccountNo'])->first();

        if ($account != null) {
            return response()->json($account, 200);
        } else {
            return response()->json('Account not found', 404);
        }
    }

    public function getORsFromRange(Request $request) {
        $from = $request['From'];
        $to = $request['To'];

        $paidBills = PaidBills::whereBetween('ORNumber', [$from, $to])
            ->get();

        return response()->json($paidBills, 200);
    }

    public function addDenomination(Request $request) {
        $acctNo = $request['AccountNumber'];
        $period = $request['ServicePeriod'];

        $denominations = Denominations::where('AccountNumber', $acctNo)
            ->where('ServicePeriod', $period)
            ->first();

        if ($denominations != null) {
            // update
            $denominations->OneThousand = $request['OneThousand'];
            $denominations->FiveHundred = $request['FiveHundred'];
            $denominations->OneHundred = $request['OneHundred'];
            $denominations->Fifty = $request['Fifty'];
            $denominations->Twenty = $request['Twenty'];
            $denominations->Ten = $request['Ten'];
            $denominations->Five = $request['Five'];
            $denominations->Peso = $request['Peso'];
            $denominations->Cents = $request['Cents'];
            $denominations->ORNumber = $request['ORNumber'];
            $denominations->ORDate = date('Y-m-d');
            $denominations->save();
        } else {
            // save
            $denominations = new Denominations;
            $denominations->id = IDGenerator::generateIDandRandString();
            $denominations->ServicePeriod = $period;
            $denominations->AccountNumber = $acctNo;
            $denominations->OneThousand = $request['OneThousand'];
            $denominations->FiveHundred = $request['FiveHundred'];
            $denominations->OneHundred = $request['OneHundred'];
            $denominations->Fifty = $request['Fifty'];
            $denominations->Twenty = $request['Twenty'];
            $denominations->Ten = $request['Ten'];
            $denominations->Five = $request['Five'];
            $denominations->Peso = $request['Peso'];
            $denominations->Cents = $request['Cents'];
            $denominations->ORNumber = $request['ORNumber'];
            $denominations->ORDate = date('Y-m-d');
            $denominations->save();
        }

        return response()->json($denominations, 200);
    }

    public function thirdPartyCollection(Request $request) {
        $date = $request['Day'] != null ? $request['Day'] : date('Y-m-d');

        $unposted = DB::table('Cashier_PaidBills')
            ->whereRaw("Status='PENDING POST' AND Source='THIRD-PARTY COLLECTION'")
            ->select('Notes',
                DB::raw("COUNT(id) AS NoOfPayments"))
            ->groupBy('Notes')
            ->orderByDesc('Notes')
            ->get();

        $transacted = DB::table('Cashier_PaidBills')
            ->whereRaw("Source='THIRD-PARTY COLLECTION' AND ORDate='" . $date . "'")
            ->select('ObjectSourceId',
                DB::raw("(SELECT COUNT(id) FROM Cashier_PaidBills WHERE Source='THIRD-PARTY COLLECTION' AND ORDate='" . $date . "' AND Status='DEPOSITED AS PRE-PAYMENT') AS DoublePayments"),
                DB::raw("(SELECT SUM(CAST(NetAmount AS Decimal(10,2))) FROM Cashier_PaidBills WHERE Source='THIRD-PARTY COLLECTION' AND ORDate='" . $date . "' AND Status='DEPOSITED AS PRE-PAYMENT') AS DoublePaymentsSum"),
                DB::raw("(SELECT COUNT(id) FROM Cashier_PaidBills WHERE Source='THIRD-PARTY COLLECTION' AND ORDate='" . $date . "' AND Status='PENDING POST') AS Pendings"),
                DB::raw("(SELECT SUM(CAST(NetAmount AS Decimal(10,2))) FROM Cashier_PaidBills WHERE Source='THIRD-PARTY COLLECTION' AND ORDate='" . $date . "' AND Status='PENDING POST') AS PendingsSum"),
                DB::raw("(SELECT COUNT(id) FROM Cashier_PaidBills WHERE Source='THIRD-PARTY COLLECTION' AND ORDate='" . $date . "' AND Status IS NULL) AS Posted"),
                DB::raw("(SELECT SUM(CAST(NetAmount AS Decimal(10,2))) FROM Cashier_PaidBills WHERE Source='THIRD-PARTY COLLECTION' AND ORDate='" . $date . "' AND Status IS NULL) AS PostedSum"),
                DB::raw("COUNT(id) AS TotalPayments"),
                DB::raw("SUM(CAST(NetAmount AS Decimal(10,2))) AS TotalPaymentsSum"),
            )
            ->groupBy('ObjectSourceId')
            ->orderByDesc('ObjectSourceId')
            ->get();

        return view('/paid_bills/third_party_collection', [
            'unposted' => $unposted,
            'transacted' => $transacted,
        ]);
    }

    public function uploadThirdPartyCollection() {
        return view('/paid_bills/upload_third_party_collection', [

        ]);
    }

    public function validateTpcUpload(Request $request) {
        if ($request->file('file') != null) {
            $file = $request->file('file');
            $userId = Auth::id();
            $seriesNo = IDGenerator::generateID();

            // IMPORT
            $tpc = new ThirdPartyPaidBills($userId, $seriesNo);
            Excel::import($tpc, $file);

            return redirect(route('paidBills.tcp-upload-validator', [$seriesNo]));
        } else {
            return abort(404, "No file imported!");
        }
    }

    public function tcpUploadValidator($seriesNo) {
        // QUERY IMPORTED
        $paidBills = DB::table('Cashier_PaidBills')
            ->leftJoin('Billing_ServiceAccounts', 'Cashier_PaidBills.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->where('Cashier_PaidBills.Notes', $seriesNo)
            ->where('Cashier_PaidBills.Status', 'PENDING POST')
            ->select('Cashier_PaidBills.*',
                'Billing_ServiceAccounts.OldAccountNo',
                'Billing_ServiceAccounts.ServiceAccountName',
                DB::raw("(SELECT TOP 1 KwhUsed FROM Billing_Bills WHERE AccountNumber=Cashier_PaidBills.AccountNumber AND ServicePeriod=Cashier_PaidBills.ServicePeriod) AS KwhUsed"),
                DB::raw("(SELECT TOP 1 BillNumber FROM Billing_Bills WHERE AccountNumber=Cashier_PaidBills.AccountNumber AND ServicePeriod=Cashier_PaidBills.ServicePeriod) AS BillNumber"),
                DB::raw("(SELECT TOP 1 NetAmount FROM Billing_Bills WHERE AccountNumber=Cashier_PaidBills.AccountNumber AND ServicePeriod=Cashier_PaidBills.ServicePeriod) AS BillAmount"),
                DB::raw("(SELECT COUNT(p.id) FROM Cashier_PaidBills p WHERE p.AccountNumber=Cashier_PaidBills.AccountNumber AND ServicePeriod=Cashier_PaidBills.ServicePeriod AND Status IS NULL) AS Duplicates")
            )
            ->orderBy('OldAccountNo')
            ->get();

        return view('/paid_bills/tcp_upload_validator', [
            'paidBills' => $paidBills,
            'seriesNo' => $seriesNo
        ]);
    }

    public function depositDoublePayments($seriesNo) {
        $paidBills = DB::table('Cashier_PaidBills')
            ->leftJoin('Billing_ServiceAccounts', 'Cashier_PaidBills.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->where('Cashier_PaidBills.Notes', $seriesNo)
            ->where('Cashier_PaidBills.Status', 'PENDING POST')
            ->whereRaw("Cashier_PaidBills.AccountNumber IN (SELECT AccountNumber FROM Cashier_PaidBills p WHERE p.AccountNumber=Cashier_PaidBills.AccountNumber AND ServicePeriod=Cashier_PaidBills.ServicePeriod AND Status IS NULL)")
            ->select('Cashier_PaidBills.*',
                'Billing_ServiceAccounts.OldAccountNo',
                'Billing_ServiceAccounts.ServiceAccountName',
            )
            ->orderBy('OldAccountNo')
            ->get();

        foreach($paidBills as $item) {
            $prePaymentBalance = PrePaymentBalance::where('AccountNumber', $item->AccountNumber)->first();

            if ($prePaymentBalance != null) {
                $oldAmount = floatval($prePaymentBalance->Balance);
                $newAmount = floatval($item->NetAmount) + $oldAmount;

                $prePaymentBalance->Balance = $newAmount;
                $prePaymentBalance->save();
            } else {
                $prePaymentBalance = new PrePaymentBalance();
                $prePaymentBalance->id = IDGenerator::generateIDandRandString();
                $prePaymentBalance->AccountNumber = $item->AccountNumber;
                $prePaymentBalance->Balance = $item->NetAmount;
                $prePaymentBalance->save();
            }

            // ADD TRANSACTION HISTORY
            $transHistory = new PrePaymentTransHistory;
            $transHistory->id = IDGenerator::generateIDandRandString();
            $transHistory->AccountNumber = $item->AccountNumber;
            $transHistory->Method = 'DEPOSIT';
            $transHistory->Amount = $item->NetAmount;
            $transHistory->UserId = Auth::id(); 
            $transHistory->Notes = 'Double Payment from Third Party Collection (' . $item->ObjectSourceId . ' - Ref. No: ' . $item->DCRNumber . ')';
            $transHistory->ORNumber = $item->ORNumber;
            $transHistory->save();

            // SAVE TRANSACTION
            $id = IDGenerator::generateID();

            $transactionIndex = new TransactionIndex;
            $transactionIndex->id = $id;
            $transactionIndex->TransactionNumber = env('APP_LOCATION') . '-' . $id;
            $transactionIndex->PaymentTitle = "Pre-payment Deposit for Account No. " . $item->AccountNumber;
            $transactionIndex->ORNumber = $item->ORNumber;
            $transactionIndex->ORDate = date('Y-m-d');
            $transactionIndex->Total = $item->NetAmount;
            $transactionIndex->Source = 'Pre-Payment Deposits';
            $transactionIndex->ObjectId = $transHistory->id;
            $transactionIndex->PaymentUsed = 'Cash';
            $transactionIndex->UserId = Auth::id();
            $transactionIndex->save();

            // SAVE TRANSACTION DETAILS
            $transactionDetails = new TransactionDetails;
            $transactionDetails->id = IDGenerator::generateIDandRandString();
            $transactionDetails->TransactionIndexId = $id;
            $transactionDetails->Particular = 'Pre-Payment Deposit';
            $transactionDetails->Amount = $item->NetAmount;
            $transactionDetails->Total = $item->NetAmount;
            $transactionDetails->save();

            // update paidbills
            $pb = PaidBills::find($item->id);
            if ($pb != null) {
                $pb->Status = 'DEPOSITED AS PRE-PAYMENT';
                $pb->save();
            }            
        }

        return redirect(route('paidBills.tcp-upload-validator', [$seriesNo]));
    }

    public function postPayments($seriesNo) {
        $paidBills = DB::table('Cashier_PaidBills')
            ->leftJoin('Billing_ServiceAccounts', 'Cashier_PaidBills.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->where('Cashier_PaidBills.Notes', $seriesNo)
            ->where('Cashier_PaidBills.Status', 'PENDING POST')
            ->whereRaw("Cashier_PaidBills.AccountNumber IN (SELECT AccountNumber FROM Billing_Bills WHERE ServicePeriod=Cashier_PaidBills.ServicePeriod)")
            ->whereRaw("Cashier_PaidBills.AccountNumber NOT IN (SELECT AccountNumber FROM Cashier_PaidBills p WHERE p.AccountNumber=Cashier_PaidBills.AccountNumber AND ServicePeriod=Cashier_PaidBills.ServicePeriod AND Status IS NULL)")
            ->select('Cashier_PaidBills.*')
            ->get();

        foreach($paidBills as $item) {
            $bill = Bills::where('ServicePeriod', $item->ServicePeriod)
                ->where('AccountNumber', $item->AccountNumber)
                ->first();

            if ($bill != null) {
                $account = ServiceAccounts::find($bill->AccountNumber);
                $paidBill = PaidBills::find($item->id);
                $paidBill->Status = null;
                $paidBill->PostingDate = date('Y-m-d');
                $paidBill->PostingTime = date('H:i:s');
                $paidBill->save();

                /**
                 * SAVE DCR AND SALES REPORT
                 */
                if ($account != null) {                    
                    if ($account->ForDistribution == 'Yes') {
                        // IF ACCOUNT IS MARKED AS FOR DISTRIBUTION
                        if ($account->DistributionAccountCode != null) {
                            // GET AR CONSUMERS
                            $dcrSum = new DCRSummaryTransactions;
                            $dcrSum->id = IDGenerator::generateIDandRandString();
                            $dcrSum->GLCode = $account->DistributionAccountCode;
                            $dcrSum->Amount = DCRSummaryTransactions::getARConsumersAmount($bill);
                            $dcrSum->Day = date('Y-m-d');
                            $dcrSum->Time = date('H:i:s');
                            $dcrSum->Teller = Auth::id();
                            $dcrSum->ORNumber = $item->ORNumber;
                            $dcrSum->ReportDestination = 'BOTH';
                            $dcrSum->Office = env('APP_LOCATION');
                            $dcrSum->AccountNumber = $bill->AccountNumber;
                            $dcrSum->save();
                        }                        
                    } else {
                        // GET AR CONSUMERS
                        $dcrSum = new DCRSummaryTransactions;
                        $dcrSum->id = IDGenerator::generateIDandRandString();
                        $dcrSum->GLCode = DCRSummaryTransactions::getARConsumers($account->Town);
                        $dcrSum->Amount = DCRSummaryTransactions::getARConsumersAmount($bill);
                        $dcrSum->Day = date('Y-m-d');
                        $dcrSum->Time = date('H:i:s');
                        $dcrSum->Teller = Auth::id();
                        $dcrSum->ORNumber = $item->ORNumber;
                        $dcrSum->ReportDestination = 'COLLECTION';
                        $dcrSum->Office = env('APP_LOCATION');
                        $dcrSum->AccountNumber = $bill->AccountNumber;
                        $dcrSum->save();

                        // GET RPT FOR DCR
                        $dcrSum = new DCRSummaryTransactions;
                        $dcrSum->id = IDGenerator::generateIDandRandString();
                        $dcrSum->GLCode = DCRSummaryTransactions::getARConsumersRPT($account->Town);
                        $dcrSum->Amount = $bill->RealPropertyTax;
                        $dcrSum->Day = date('Y-m-d');
                        $dcrSum->Time = date('H:i:s');
                        $dcrSum->Teller = Auth::id();
                        $dcrSum->ORNumber = $item->ORNumber;
                        $dcrSum->ReportDestination = 'COLLECTION';
                        $dcrSum->Office = env('APP_LOCATION');
                        $dcrSum->AccountNumber = $bill->AccountNumber;
                        $dcrSum->save();

                        // GET RPT  FOR SALES
                        $dcrSum = new DCRSummaryTransactions;
                        $dcrSum->id = IDGenerator::generateIDandRandString();
                        $dcrSum->GLCode = '140-143-30';
                        $dcrSum->Amount = $bill->RealPropertyTax;
                        $dcrSum->Day = date('Y-m-d');
                        $dcrSum->Time = date('H:i:s');
                        $dcrSum->Teller = Auth::id();
                        $dcrSum->ORNumber = $item->ORNumber;
                        $dcrSum->ReportDestination = 'SALES';
                        $dcrSum->Office = env('APP_LOCATION');
                        $dcrSum->AccountNumber = $bill->AccountNumber;
                        $dcrSum->save();

                        // GET SALES AR BY CONSUMER TYPE 
                        if ($account->OrganizationParentAccount != null) {
                            // GET BAPA
                            $dcrSum = new DCRSummaryTransactions;
                            $dcrSum->id = IDGenerator::generateIDandRandString();
                            $dcrSum->GLCode = '311-448-00';
                            $dcrSum->Amount = DCRSummaryTransactions::getARConsumersAmount($bill);
                            $dcrSum->Day = date('Y-m-d');
                            $dcrSum->Time = date('H:i:s');
                            $dcrSum->Teller = Auth::id();
                            $dcrSum->ORNumber = $item->ORNumber;
                            $dcrSum->ReportDestination = 'SALES';
                            $dcrSum->Office = env('APP_LOCATION');
                            $dcrSum->AccountNumber = $bill->AccountNumber;
                            $dcrSum->save();
                        } else {
                            // GET NOT BAPA
                            if ($account->AccountType == 'RURAL RESIDENTIAL' || $account->AccountType == 'RESIDENTIAL') {
                                // GET RESIDENTIALS
                                $dcrSum = new DCRSummaryTransactions;
                                $dcrSum->id = IDGenerator::generateIDandRandString();
                                $dcrSum->GLCode = DCRSummaryTransactions::getARConsumers($account->Town);;
                                $dcrSum->Amount = DCRSummaryTransactions::getARConsumersAmount($bill);
                                $dcrSum->Day = date('Y-m-d');
                                $dcrSum->Time = date('H:i:s');
                                $dcrSum->Teller = Auth::id();
                                $dcrSum->ORNumber = $item->ORNumber;
                                $dcrSum->ReportDestination = 'SALES';
                                $dcrSum->Office = env('APP_LOCATION');
                                $dcrSum->AccountNumber = $bill->AccountNumber;
                                $dcrSum->save();
                            } else {
                                // GET NOT RESIDENTIALS
                                $dcrSum = new DCRSummaryTransactions;
                                $dcrSum->id = IDGenerator::generateIDandRandString();
                                $dcrSum->GLCode = DCRSummaryTransactions::getGLCodePerAccountType($account->AccountType);;
                                $dcrSum->Amount = DCRSummaryTransactions::getARConsumersAmount($bill);
                                $dcrSum->Day = date('Y-m-d');
                                $dcrSum->Time = date('H:i:s');
                                $dcrSum->Teller = Auth::id();
                                $dcrSum->ORNumber = $item->ORNumber;
                                $dcrSum->ReportDestination = 'SALES';
                                $dcrSum->Office = env('APP_LOCATION');
                                $dcrSum->AccountNumber = $bill->AccountNumber;
                                $dcrSum->save();
                            }
                        }
                    }

                    // GET TERMED PAYMENT BUNDLES
                    if ($bill->AdditionalCharges != null) {
                        // GET TERMED PAYMENT
                        $termedPayment = ArrearsLedgerDistribution::where('AccountNumber', $account->id)
                            ->where('ServicePeriod', $bill->ServicePeriod)
                            ->whereNull('IsPaid')
                            ->first();

                        if ($termedPayment != null) {
                            $dcrSum = new DCRSummaryTransactions;
                            $dcrSum->id = IDGenerator::generateIDandRandString();
                            $dcrSum->GLCode = DCRSummaryTransactions::getARConsumersTermedPayments($account->Town);
                            $dcrSum->Amount = $termedPayment->Amount;
                            $dcrSum->Day = date('Y-m-d');
                            $dcrSum->Time = date('H:i:s');
                            $dcrSum->Teller = Auth::id();
                            $dcrSum->ORNumber = $item->ORNumber;
                            $dcrSum->ReportDestination = 'COLLECTION';
                            $dcrSum->Office = env('APP_LOCATION');
                            $dcrSum->AccountNumber = $bill->AccountNumber;
                            $dcrSum->save();

                            $termedPayment->IsPaid = 'Yes';
                            $termedPayment->save();
                        }
                    }
                }

                // GET UC-NPC Stranded Debt COLLECTION
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '140-142-87';
                $dcrSum->Amount = $bill->NPCStrandedDebt;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $item->ORNumber;
                $dcrSum->ReportDestination = 'COLLECTION';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET UC-NPC Stranded Debt Sales
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '230-232-65';
                $dcrSum->Amount = $bill->NPCStrandedDebt;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $item->ORNumber;
                $dcrSum->ReportDestination = 'SALES';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET STRANDED CONTRACT COST COLLECTION
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '140-142-92';
                $dcrSum->Amount = $bill->StrandedContractCosts;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $item->ORNumber;
                $dcrSum->ReportDestination = 'COLLECTION';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET STRANDED CONTRACT COST SALES
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '230-232-62';
                $dcrSum->Amount = $bill->StrandedContractCosts;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $item->ORNumber;
                $dcrSum->ReportDestination = 'SALES';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET FIT ALL COLLECTION
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '140-142-88';
                $dcrSum->Amount = $bill->FeedInTariffAllowance;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $item->ORNumber;
                $dcrSum->ReportDestination = 'COLLECTION';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET FIT ALL SALES
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '230-232-64';
                $dcrSum->Amount = $bill->FeedInTariffAllowance;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $item->ORNumber;
                $dcrSum->ReportDestination = 'SALES';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET UCME REDCI COLLECTION
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '140-142-89';
                $dcrSum->Amount = $bill->MissionaryElectrificationREDCI;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $item->ORNumber;
                $dcrSum->ReportDestination = 'COLLECTION';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET UCME REDCI SALES
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '230-232-63';
                $dcrSum->Amount = $bill->MissionaryElectrificationREDCI;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $item->ORNumber;
                $dcrSum->ReportDestination = 'SALES';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET GENCO
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '140-142-94';
                $dcrSum->Amount = $bill->GenerationVAT;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $item->ORNumber;
                $dcrSum->ReportDestination = 'COLLECTION';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET TRANSCO
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '140-142-95';
                $dcrSum->Amount = $bill->TransmissionVAT;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $item->ORNumber;
                $dcrSum->ReportDestination = 'COLLECTION';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET SYSLOSS VAT
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '140-142-96';
                $dcrSum->Amount = $bill->SystemLossVAT;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $item->ORNumber;
                $dcrSum->ReportDestination = 'COLLECTION';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET DIST/OTHERS VAT
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '140-142-97';
                $dcrSum->Amount = $bill->DistributionVAT;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $item->ORNumber;
                $dcrSum->ReportDestination = 'COLLECTION';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET GENVAT, TRANSVAT, SYSLOSSVAT SALES
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '170-184-40';
                $dcrSum->Amount = DCRSummaryTransactions::getSalesGenTransSysLossVatAmount($bill);
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $item->ORNumber;
                $dcrSum->ReportDestination = 'SALES';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET DIST AND OTHERS SALES
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '250-255-00';
                $dcrSum->Amount = DCRSummaryTransactions::getSalesDistOthersVatAmount($bill);
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $item->ORNumber;
                $dcrSum->ReportDestination = 'SALES';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET UCME COLLECTION
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '140-142-98';
                $dcrSum->Amount = $bill->MissionaryElectrificationCharge;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $item->ORNumber;
                $dcrSum->ReportDestination = 'COLLECTION';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET UCME SALES
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '230-232-60';
                $dcrSum->Amount = $bill->MissionaryElectrificationCharge;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $item->ORNumber;
                $dcrSum->ReportDestination = 'SALES';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET EWT 2%
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '140-160-00';
                $dcrSum->Amount = $paidBill->Form2307TwoPercent;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $item->ORNumber;
                $dcrSum->ReportDestination = 'COLLECTION';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET EVAT 5%
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '140-170-00';
                $dcrSum->Amount = $paidBill->Form2307FivePercent;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $item->ORNumber;
                $dcrSum->ReportDestination = 'COLLECTION';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET ENVIRONMENT CHARGE COLLECTION
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '140-142-99';
                $dcrSum->Amount = $bill->EnvironmentalCharge;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $item->ORNumber;
                $dcrSum->ReportDestination = 'COLLECTION';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET ENVIRONMENT CHARGE SALES
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '230-232-90';
                $dcrSum->Amount = $bill->EnvironmentalCharge;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $item->ORNumber;
                $dcrSum->ReportDestination = 'SALES';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET RFSC COLLECTION
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '140-142-93';
                $dcrSum->Amount = $bill->RFSC;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $item->ORNumber;
                $dcrSum->ReportDestination = 'COLLECTION';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // GET RFSC SALES
                $dcrSum = new DCRSummaryTransactions;
                $dcrSum->id = IDGenerator::generateIDandRandString();
                $dcrSum->GLCode = '211-211-10';
                $dcrSum->Amount = $bill->RFSC;
                $dcrSum->Day = date('Y-m-d');
                $dcrSum->Time = date('H:i:s');
                $dcrSum->Teller = Auth::id();
                $dcrSum->ORNumber = $item->ORNumber;
                $dcrSum->ReportDestination = 'SALES';
                $dcrSum->Office = env('APP_LOCATION');
                $dcrSum->AccountNumber = $bill->AccountNumber;
                $dcrSum->save();

                // SAVE PAID BILL DETAILS
                $paidBillDetails = new PaidBillsDetails;
                $paidBillDetails->id = IDGenerator::generateIDandRandString();
                $paidBillDetails->AccountNumber = $bill->AccountNumber;
                $paidBillDetails->ORNumber = $item->ORNumber;
                $paidBillDetails->Amount = $bill->NetAmount;
                $paidBillDetails->PaymentUsed = 'Cash';
                $paidBillDetails->UserId = Auth::id();
                $paidBillDetails->save();
            }
        }

        return redirect(route('paidBills.tcp-upload-validator', [$seriesNo]));
        // $paidBills = DB::table('Cashier_PaidBills')
        //     ->leftJoin('Billing_ServiceAccounts', 'Cashier_PaidBills.AccountNumber', '=', 'Billing_ServiceAccounts.id')
        //     ->where('Cashier_PaidBills.Notes', $seriesNo)
        //     ->where('Cashier_PaidBills.Status', 'PENDING POST')
        //     ->whereRaw("Cashier_PaidBills.AccountNumber IN (SELECT AccountNumber FROM Billing_Bills WHERE ServicePeriod=Cashier_PaidBills.ServicePeriod)")
        //     ->whereRaw("Cashier_PaidBills.AccountNumber NOT IN (SELECT AccountNumber FROM Cashier_PaidBills p WHERE p.AccountNumber=Cashier_PaidBills.AccountNumber AND ServicePeriod=Cashier_PaidBills.ServicePeriod AND Status IS NULL)")
        //     ->update(['Cashier_PaidBills.Status' => null, 'Cashier_PaidBills.PostingDate' => date('Y-m-d'), 'Cashier_PaidBills.PostingTime' => date('H:i:s')]);
    }
}

