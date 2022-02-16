<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTransactionIndexRequest;
use App\Http\Requests\UpdateTransactionIndexRequest;
use App\Repositories\TransactionIndexRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\ServiceConnectionTotalPayments;
use App\Models\ServiceConnections;
use App\Models\TransactionDetails;
use App\Models\TransactionIndex;
use App\Models\ArrearsLedgerDistribution;
use App\Models\Collectibles;
use App\Models\ServiceAccounts;
use App\Models\IDGenerator;
use Flash;
use Response;

class TransactionIndexController extends AppBaseController
{
    /** @var  TransactionIndexRepository */
    private $transactionIndexRepository;

    public function __construct(TransactionIndexRepository $transactionIndexRepo)
    {
        $this->middleware('auth');
        $this->transactionIndexRepository = $transactionIndexRepo;
    }

    /**
     * Display a listing of the TransactionIndex.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $transactionIndices = $this->transactionIndexRepository->all();

        return view('transaction_indices.index')
            ->with('transactionIndices', $transactionIndices);
    }

    /**
     * Show the form for creating a new TransactionIndex.
     *
     * @return Response
     */
    public function create()
    {
        return view('transaction_indices.create');
    }

    /**
     * Store a newly created TransactionIndex in storage.
     *
     * @param CreateTransactionIndexRequest $request
     *
     * @return Response
     */
    public function store(CreateTransactionIndexRequest $request)
    {
        $input = $request->all();

        $transactionIndex = $this->transactionIndexRepository->create($input);

        Flash::success('Transaction Index saved successfully.');

        return redirect(route('transactionIndices.index'));
    }

    /**
     * Display the specified TransactionIndex.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $transactionIndex = $this->transactionIndexRepository->find($id);

        if (empty($transactionIndex)) {
            Flash::error('Transaction Index not found');

            return redirect(route('transactionIndices.index'));
        }

        return view('transaction_indices.show')->with('transactionIndex', $transactionIndex);
    }

    /**
     * Show the form for editing the specified TransactionIndex.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $transactionIndex = $this->transactionIndexRepository->find($id);

        if (empty($transactionIndex)) {
            Flash::error('Transaction Index not found');

            return redirect(route('transactionIndices.index'));
        }

        return view('transaction_indices.edit')->with('transactionIndex', $transactionIndex);
    }

    /**
     * Update the specified TransactionIndex in storage.
     *
     * @param int $id
     * @param UpdateTransactionIndexRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTransactionIndexRequest $request)
    {
        $transactionIndex = $this->transactionIndexRepository->find($id);

        if (empty($transactionIndex)) {
            Flash::error('Transaction Index not found');

            return redirect(route('transactionIndices.index'));
        }

        $transactionIndex = $this->transactionIndexRepository->update($request->all(), $id);

        Flash::success('Transaction Index updated successfully.');

        return redirect(route('transactionIndices.index'));
    }

    /**
     * Remove the specified TransactionIndex from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $transactionIndex = $this->transactionIndexRepository->find($id);

        if (empty($transactionIndex)) {
            Flash::error('Transaction Index not found');

            return redirect(route('transactionIndices.index'));
        }

        $this->transactionIndexRepository->delete($id);

        Flash::success('Transaction Index deleted successfully.');

        return redirect(route('transactionIndices.index'));
    }

    public function serviceConnectionCollection() {
        $applications = DB::table('CRM_ServiceConnections')
            ->where('Status', 'Approved')
            ->whereNull('ORNumber')
            ->whereNull('ORDate')
            ->get();
        return view('/transaction_indices/service_connection_collection', [
            'applications' => $applications,
        ]);
    }

    public function getPayableDetails(Request $request) {
        $particularPayments = DB::table('CRM_ServiceConnectionParticularPaymentsTransactions')
                    ->leftJoin('CRM_ServiceConnectionPaymentParticulars', 'CRM_ServiceConnectionParticularPaymentsTransactions.Particular', '=', 'CRM_ServiceConnectionPaymentParticulars.id')
                    ->select('CRM_ServiceConnectionParticularPaymentsTransactions.id',
                            'CRM_ServiceConnectionParticularPaymentsTransactions.Amount',
                            'CRM_ServiceConnectionParticularPaymentsTransactions.Vat',
                            'CRM_ServiceConnectionParticularPaymentsTransactions.Total',
                            'CRM_ServiceConnectionPaymentParticulars.Particular')
                    ->where('CRM_ServiceConnectionParticularPaymentsTransactions.ServiceConnectionId', $request['svcId'])
                    ->get();

        return response()->json($particularPayments, 200);
    }

    public function getPayableTotal(Request $request) {
        $totalTransactions = DB::table('CRM_ServiceConnectionTotalPayments')
            ->leftJoin('CRM_ServiceConnections', 'CRM_ServiceConnectionTotalPayments.ServiceConnectionId', '=', 'CRM_ServiceConnections.id')
            ->leftJoin('CRM_Barangays', 'CRM_ServiceConnections.Barangay', '=', 'CRM_Barangays.id')
            ->leftJoin('CRM_Towns', 'CRM_ServiceConnections.Town', '=', 'CRM_Towns.id')
            ->where('CRM_ServiceConnectionTotalPayments.ServiceConnectionId', $request['svcId'])
            ->select('CRM_ServiceConnectionTotalPayments.*',
                'CRM_ServiceConnections.ServiceAccountName',
                'CRM_Towns.Town as Town',
                'CRM_Barangays.Barangay as Barangay',)
            ->first();

        return response()->json($totalTransactions, 200);
    }

    public function saveAndPrintORServiceConnections(Request $request) {
        $particularPayments = DB::table('CRM_ServiceConnectionParticularPaymentsTransactions')
            ->leftJoin('CRM_ServiceConnectionPaymentParticulars', 'CRM_ServiceConnectionParticularPaymentsTransactions.Particular', '=', 'CRM_ServiceConnectionPaymentParticulars.id')
            ->select('CRM_ServiceConnectionParticularPaymentsTransactions.id',
                    'CRM_ServiceConnectionParticularPaymentsTransactions.Amount',
                    'CRM_ServiceConnectionParticularPaymentsTransactions.Vat',
                    'CRM_ServiceConnectionParticularPaymentsTransactions.Total',
                    'CRM_ServiceConnectionPaymentParticulars.Particular')
            ->where('CRM_ServiceConnectionParticularPaymentsTransactions.ServiceConnectionId', $request['svcId'])
            ->get();

        $totalTransactions = DB::table('CRM_ServiceConnectionTotalPayments')
            ->leftJoin('CRM_ServiceConnections', 'CRM_ServiceConnectionTotalPayments.ServiceConnectionId', '=', 'CRM_ServiceConnections.id')
            ->leftJoin('CRM_Barangays', 'CRM_ServiceConnections.Barangay', '=', 'CRM_Barangays.id')
            ->leftJoin('CRM_Towns', 'CRM_ServiceConnections.Town', '=', 'CRM_Towns.id')
            ->where('CRM_ServiceConnectionTotalPayments.ServiceConnectionId', $request['svcId'])
            ->select('CRM_ServiceConnectionTotalPayments.*',
                'CRM_ServiceConnections.ServiceAccountName',
                'CRM_Towns.Town as Town',
                'CRM_Barangays.Barangay as Barangay',)
            ->first();

        // SAVE TRANSACTION
        $id = IDGenerator::generateID();

        $transactionIndex = new TransactionIndex;
        $transactionIndex->id = $id;
        $transactionIndex->TransactionNumber = env('APP_LOCATION') . '-' . $id;
        $transactionIndex->PaymentTitle = "Service Connection Application Payment of " . $totalTransactions->ServiceAccountName;
        $transactionIndex->ORNumber = $request['ORNumber'];
        $transactionIndex->ORDate = date('Y-m-d');
        $transactionIndex->SubTotal = $totalTransactions->SubTotal;
        $transactionIndex->VAT = $totalTransactions->TotalVat;
        $transactionIndex->Total = $totalTransactions->Total;
        $transactionIndex->ServiceConnectionId = $request['svcId'];
        $transactionIndex->Source = "Service Connection Application";
        $transactionIndex->PaymentUsed = $request['PaymentUsed'];
        $transactionIndex->UserId = Auth::id();
        $transactionIndex->save();

        foreach($particularPayments as $item) {
            $transactionDetails = new TransactionDetails;
            $transactionDetails->id = IDGenerator::generateIDandRandString();
            $transactionDetails->TransactionIndexId = $id;
            $transactionDetails->Particular = $item->Particular;
            $transactionDetails->Amount = $item->Amount;
            $transactionDetails->VAT = $item->Vat;
            $transactionDetails->Total = $item->Total;
            $transactionDetails->save();
        }

        // UPDATE Service Connection OR
        $serviceConnection = ServiceConnections::find($request['svcId']);
        $serviceConnection->ORNumber = $transactionIndex->ORNumber;
        $serviceConnection->ORDate = $transactionIndex->ORDate;
        $serviceConnection->save();

        return response()->json(['id' => $id], 200);
    }

    public function printORServiceConnections($transactionIndexId) {
        $transactionIndex = TransactionIndex::find($transactionIndexId);
        $transactionDetails = TransactionDetails::where('TransactionIndexId', $transactionIndexId)->get();

        return view('/transaction_indices/print_or_service_connections', [
            'transactionIndex' => $transactionIndex,
            'transactionDetails' => $transactionDetails,
        ]);
    }

    public function uncollectedArrears() {
        return view('/transaction_indices/uncollected_arrears');
    }

    public function searchArrearCollectibles(Request $request) {
        $results = DB::table('Billing_Collectibles')
            ->leftJoin('Billing_ServiceAccounts', 'Billing_Collectibles.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->where('Billing_ServiceAccounts.ServiceAccountName', 'LIKE', '%' . $request['query'] . '%')
            ->orWhere('Billing_ServiceAccounts.id', 'LIKE', '%' . $request['query'] . '%')
            ->orWhere('Billing_ServiceAccounts.OldAccountNo', 'LIKE', '%' . $request['query'] . '%')
            ->where('Billing_Collectibles.Balance', '!=', '0')
            ->select('Billing_ServiceAccounts.id as AccountNumber',
                    'Billing_ServiceAccounts.ServiceAccountName',
                    'Billing_ServiceAccounts.OldAccountNo',
                    'Billing_ServiceAccounts.AccountCount',
                    'Billing_ServiceAccounts.AccountType',
                    'Billing_ServiceAccounts.AccountStatus',
                    'Billing_ServiceAccounts.AreaCode',
                    'Billing_Collectibles.Balance',
                    'Billing_Collectibles.id')
            ->orderBy('Billing_ServiceAccounts.ServiceAccountName')
            ->get();

        $output = "";

        if (count($results) > 0) {
            foreach($results as $item) {
                $ledger = ArrearsLedgerDistribution::where('AccountNumber', $item->AccountNumber)
                    ->whereNull('IsPaid')
                    ->get();

                if (count($ledger) > 0) {
                    $output .= '
                        <tr>
                            <td>' . $item->AccountNumber . '</td>
                            <td>' . $item->ServiceAccountName . '</td>
                            <td>' . number_format($item->Balance, 2) . '</td>
                            <td class="text-right">
                                <a class="btn btn-link text-primary" href="' . route('transactionIndices.ledger-arrears-collection', [$item->AccountNumber]) . '"><i class="fas fa-forward"></i></a>
                            </td>
                        </tr>
                    ';
                } else {
                    $output .= '
                        <tr>
                            <td>' . $item->AccountNumber . '</td>
                            <td>' . $item->ServiceAccountName . '</td>
                            <td>' . number_format($item->Balance, 2) . '</td>
                            <td class="text-right">
                                <button class="btn btn-link text-primary" onclick=fetchDetails("' . $item->AccountNumber . '")><i class="fas fa-forward"></i></button>
                            </td>
                        </tr>
                    ';
                }                
            }

            return response()->json($output, 200);
        } else {
            return response()->json(['res' => 'No results found'], 200);
        }       
    }

    public function fetchArrearDetails(Request $request) {
        $collectibles = Collectibles::where('AccountNumber', $request['AccountNumber'])->first();

        if ($collectibles != null) {
            return response()->json($collectibles, 200);
        } else {
            return response()->json([], 200);
        }
    }

    public function saveArrearTransaction(Request $request) {
        $account = ServiceAccounts::find($request['AccountNumber']);

        $total = floatval($request['AmountPaid']);

        // SAVE TRANSACTION
        $id = IDGenerator::generateID();

        $transactionIndex = new TransactionIndex;
        $transactionIndex->id = $id;
        $transactionIndex->TransactionNumber = env('APP_LOCATION') . '-' . $id;
        $transactionIndex->PaymentTitle = "Partial Payment to the arrears of " . $account->ServiceAccountName;
        $transactionIndex->ORNumber = $request['ORNumber'];
        $transactionIndex->ORDate = date('Y-m-d');
        $transactionIndex->SubTotal = round($total, 2);
        // $transactionIndex->VAT = 0; // TO BE ADDED LATER
        $transactionIndex->Total = round($total, 2);
        $transactionIndex->Source = "Arrears Collectible";
        $transactionIndex->ObjectId = $request['AccountNumber']; // ACCOUNT NUMBER
        $transactionIndex->PaymentUsed = $request['PaymentUsed'];
        $transactionIndex->UserId = Auth::id();
        $transactionIndex->save();

        // SAVE TRANSACTION DETAILS
        $transactionDetails = new TransactionDetails;
        $transactionDetails->id = IDGenerator::generateIDandRandString();
        $transactionDetails->TransactionIndexId = $id;
        $transactionDetails->Particular = "Uncollected Arrear Partial Payment";
        $transactionDetails->Amount = round($total, 2);
        // $transactionDetails->VAT = $item->Vat; // TO BE ADDED LATER
        $transactionDetails->Total = round($total, 2);
        $transactionDetails->save();

        // DEDUCT BALANCE
        $collectibles = Collectibles::where('AccountNumber', $request['AccountNumber'])->first();

        if ($collectibles != null) {
            $collectibles->Balance = floatval($request['RemainingBalance']);
            $collectibles->save();
        }

        return response()->json($transactionIndex, 200);
    }

    public function ledgerArrearsCollection($accountNo) {
        $account = ServiceAccounts::find($accountNo);
        $collectibles = Collectibles::where('AccountNumber', $accountNo)->first();
        $ledger = ArrearsLedgerDistribution::where('AccountNumber', $accountNo)->orderBy('ServicePeriod')->get();

        return view('/transaction_indices/ledger_arrears_collection', [
            'account' => $account,
            'collectibles' => $collectibles,
            'ledger' => $ledger,
        ]);
    }

    public function saveLedgerArrearTransaction(Request $request) {
        $ledgerIds = $request['LedgerIds'];
        $len = count($ledgerIds);
        $account = ServiceAccounts::find($request['AccountNumber']);
        $collectibles = Collectibles::where('AccountNumber', $request['AccountNumber'])->first();

        // SAVE TRANSACTION
        $total = floatval($request['TotalPayment']);

        // SAVE TRANSACTION
        $id = IDGenerator::generateID();

        $transactionIndex = new TransactionIndex;
        $transactionIndex->id = $id;
        $transactionIndex->TransactionNumber = env('APP_LOCATION') . '-' . $id;
        $transactionIndex->PaymentTitle = "Partial Payment to the arrears of " . $account->ServiceAccountName;
        $transactionIndex->ORNumber = $request['ORNumber'];
        $transactionIndex->ORDate = date('Y-m-d');
        $transactionIndex->SubTotal = round($total, 2);
        // $transactionIndex->VAT = 0; // TO BE ADDED LATER
        $transactionIndex->Total = round($total, 2);
        $transactionIndex->Source = "Arrears Termed Ledger";
        $transactionIndex->ObjectId = $request['AccountNumber']; // ACCOUNT NUMBER
        $transactionIndex->PaymentUsed = $request['PaymentUsed'];
        $transactionIndex->UserId = Auth::id();
        $transactionIndex->save();

        for ($i=0; $i<$len; $i++) {
            // LEDGERS
            $ledgers = ArrearsLedgerDistribution::find($ledgerIds[$i]);

            if ($ledgers != null) {
                // SAVE TRANSACTION DETAILS
                $transactionDetails = new TransactionDetails;
                $transactionDetails->id = IDGenerator::generateIDandRandString();
                $transactionDetails->TransactionIndexId = $id;
                $transactionDetails->Particular = "Termed arrear for " . date('F Y', strtotime($ledgers->ServicePeriod));
                $transactionDetails->Amount = round(floatval($ledgers->Amount), 2);
                // $transactionDetails->VAT = $item->Vat; // TO BE ADDED LATER
                $transactionDetails->Total = round(floatval($ledgers->Amount), 2);
                $transactionDetails->save();

                // UPDATE LEDGER STATUS
                $ledgers->IsPaid = 'Yes';
                $ledgers->save();
            }
        }

        // DEDUCT BALANCE
        if ($collectibles != null) {
            $collectibles->Balance = round((floatval($collectibles->Balance) - $total), 2);
            $collectibles->save();
        }

        return response()->json($transactionIndex, 200);
    }

    public function printORTermedLedgerArrears($transactionIndexId) {
        $transactionIndex = TransactionIndex::find($transactionIndexId);
        $transactionDetails = TransactionDetails::where('TransactionIndexId', $transactionIndexId)->get();

        return view('/transaction_indices/print_or_termed_ledger_arrears', [
            'transactionIndex' => $transactionIndex,
            'transactionDetails' => $transactionDetails,
        ]);
    }
}
