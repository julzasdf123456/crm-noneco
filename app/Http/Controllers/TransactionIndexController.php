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
        $transactionIndex->TransactionNumber = $id;
        $transactionIndex->PaymentTitle = "Service Connection Application Payment of " . $totalTransactions->ServiceAccountName;
        $transactionIndex->ORNumber = $request['ORNumber'];
        $transactionIndex->ORDate = date('Y-m-d');
        $transactionIndex->SubTotal = $totalTransactions->SubTotal;
        $transactionIndex->VAT = $totalTransactions->TotalVat;
        $transactionIndex->Total = $totalTransactions->Total;
        $transactionIndex->ServiceConnectionId = $request['svcId'];
        $transactionIndex->Source = "Service Connection Application";
        $transactionIndex->PaymentUsed = $request['PaymentUsed'];
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
}
