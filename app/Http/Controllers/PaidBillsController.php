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
use App\Models\ORAssigning;
use App\Models\Notifiers;
use App\Models\ORCancellations;
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
        $paidBills = $this->paidBillsRepository->all();
        $orAssignedLast = ORAssigning::where('UserId', Auth::id())
            ->orderByDesc('created_at')
            ->first();

        return view('paid_bills.index', [
            'paidBills' => $paidBills,
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
            ->whereNotIn('Billing_Bills.id', DB::table('Cashier_PaidBills')->whereNull('Status')->pluck('Cashier_PaidBills.ObjectSourceId'))
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
                if (date('Y-m-d', strtotime($item->DueDate)) < date('Y-m-d')) {
                    // ARREARS

                    // IF ARREARS IS LONG DUE, IT SHOULD FIRST BE UNLOCKED BY ADMINS TO BE ABLE TO PAY
                    if ($item->IsUnlockedForPayment == 'Yes') {
                        $output .= '
                            <tr onclick=addToPayables("' . $item->id . '")>
                                <td>' . $item->BillNumber . '</td>
                                <td>' . date('F Y', strtotime($item->ServicePeriod)) . '</td>
                                <th class="text-danger">₱ ' . number_format($item->NetAmount, 2) . ' + ' . Bills::getFinalPenalty($item) . '</th>
                                <td class="text-right">
                                    <button id="' . $item->id . '" ischecked="false" additionalCharges="' . $item->AdditionalCharges . '" deductions="' . $item->Deductions . '" surcharge="' . Bills::getFinalPenalty($item) . '" amount="' . $item->NetAmount . '" class="btn btn-link text-muted" onclick=addToPayables("' . $item->id . '")><i class="fas fa-check-circle"></i></button>
                                </td>
                            </tr>
                        '; 
                    } elseif ($item->IsUnlockedForPayment == 'Requested') {
                        $output .= '
                            <tr>
                                <td>' . $item->BillNumber . '</td>
                                <td>' . date('F Y', strtotime($item->ServicePeriod)) . '</td>
                                <th class="text-danger">₱ ' . number_format($item->NetAmount, 2) . ' + ' . Bills::getFinalPenalty($item) . '</th>
                                <td class="text-right">
                                    <button id="' . $item->id . '" ischecked="false" additionalCharges="' . $item->AdditionalCharges . '" deductions="' . $item->Deductions . '" surcharge="' . Bills::getFinalPenalty($item) . '" amount="' . $item->NetAmount . '" class="btn btn-link text-muted"><i class="fas fa-exclamation-circle"></i></button>
                                </td>
                            </tr>
                        ';
                    }  else {
                        $output .= '
                            <tr>
                                <td>' . $item->BillNumber . '</td>
                                <td>' . date('F Y', strtotime($item->ServicePeriod)) . '</td>
                                <th class="text-danger">₱ ' . number_format($item->NetAmount, 2) . ' + ' . Bills::getFinalPenalty($item) . '</th>
                                <td class="text-right">
                                    <button id="' . $item->id . '" ischecked="false" additionalCharges="' . $item->AdditionalCharges . '" deductions="' . $item->Deductions . '" surcharge="' . Bills::getFinalPenalty($item) . '" amount="' . $item->NetAmount . '" onclick=requestUnlock("' . $item->id . '") class="btn btn-link text-muted"><i id="lock-' . $item->id . '" class="fas fa-lock"></i></button>
                                </td>
                            </tr>
                        ';
                    }                  
                } else {
                    $output .= '
                        <tr onclick=addToPayables("' . $item->id . '")>
                            <td>' . $item->BillNumber . '</td>
                            <td>' . date('F Y', strtotime($item->ServicePeriod)) . '</td>
                            <th>₱ ' . number_format($item->NetAmount, 2) . '</th>
                            <td class="text-right">
                                <button id="' . $item->id . '" ischecked="false" additionalCharges="' . $item->AdditionalCharges . '" deductions="' . $item->Deductions . '" surcharge="' . Bills::getFinalPenalty($item) . '" amount="' . $item->NetAmount . '" class="btn btn-link text-muted" onclick=addToPayables("' . $item->id . '")><i class="fas fa-check-circle"></i></button>
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
                if (date('Y-m-d', strtotime($bill->DueDate)) < date('Y-m-d')) {
                    $paidBill->Surcharge = Bills::getFinalPenalty($bill);
                } else {
                    $paidBill->Surcharge = "0";
                }
               
                $paidBill->AdditionalCharges = $bill->AdditionalCharges;
                $paidBill->Deductions = $bill->Deductions;
                $paidBill->NetAmount = round(floatval($bill->NetAmount) + floatval($paidBill->Surcharge), 2);
                $paidBill->Source = 'MONTHLY BILL';
                $paidBill->ObjectSourceId = $bill->id;
                $paidBill->ORNumber = $request['ORNumber'];
                $paidBill->ORDate = date('Y-m-d');
                $paidBill->UserId = Auth::id();
                $paidBill->save();
            }            
        }  
        
        // SAVE OR
        $saveOR = ORAssigning::where('ORNumber', $request['ORNumber'])
            ->where('UserId', Auth::id())
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

        $paidBillTmp = PaidBills::where('ORNumber', $paidBillId)->first();
    
        if ($paidBillTmp != null) {
            $account = ServiceAccounts::find($paidBillTmp->AccountNumber);
        }

        return view('/paid_bills/print_bill_payment', [
            'paidBill' => $paidBill,
            'paidBillSingle' => $paidBillTmp,
            'account' => $account,
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
}

