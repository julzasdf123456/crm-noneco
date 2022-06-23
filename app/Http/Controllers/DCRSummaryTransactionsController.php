<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDCRSummaryTransactionsRequest;
use App\Http\Requests\UpdateDCRSummaryTransactionsRequest;
use App\Repositories\DCRSummaryTransactionsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Flash;
use Response;

class DCRSummaryTransactionsController extends AppBaseController
{
    /** @var  DCRSummaryTransactionsRepository */
    private $dCRSummaryTransactionsRepository;

    public function __construct(DCRSummaryTransactionsRepository $dCRSummaryTransactionsRepo)
    {
        $this->middleware('auth');
        $this->dCRSummaryTransactionsRepository = $dCRSummaryTransactionsRepo;
    }

    /**
     * Display a listing of the DCRSummaryTransactions.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {        
        if ($request['Day'] != null) {
            $data = DB::table('Cashier_DCRSummaryTransactions')
                ->where('Day', $request['Day'])
                ->where('Teller', $request['Teller'])
                ->where('ReportDestination', 'COLLECTION')
                ->select('GLCode',
                    DB::raw("(SELECT Notes FROM Cashier_AccountGLCodes WHERE AccountCode=Cashier_DCRSummaryTransactions.GLCode) AS Description"),
                    DB::raw("SUM(CAST(Amount AS DECIMAL(10,2))) AS Amount")
                )
                ->groupBy('GLCode')
                ->orderBy('GLCode')
                ->get();
        } else {
            $data = DB::table('Cashier_DCRSummaryTransactions')
                ->where('Day', date('Y-m-d'))
                ->where('Teller', $request['Teller'])
                ->where('ReportDestination', 'COLLECTION')
                ->select('GLCode',
                    DB::raw("(SELECT Notes FROM Cashier_AccountGLCodes WHERE AccountCode=Cashier_DCRSummaryTransactions.GLCode) AS Description"),
                    DB::raw("SUM(CAST(Amount AS DECIMAL(10,2))) AS Amount")
                )
                ->groupBy('GLCode')
                ->orderBy('GLCode')
                ->get();
        }

        $powerBills = DB::table('Cashier_PaidBills')
            ->leftJoin('Billing_ServiceAccounts', 'Cashier_PaidBills.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->where('Cashier_PaidBills.PostingDate', $request['Day'] != null ? $request['Day'] : date('Y-m-d'))
            ->where('Cashier_PaidBills.Teller', $request['Teller'])
            ->whereNull('Cashier_PaidBills.Status')
            ->select('Cashier_PaidBills.*', 'Billing_ServiceAccounts.ServiceAccountName', 'Billing_ServiceAccounts.OldAccountNo')
            ->get();

        $nonPowerBills = DB::table('Cashier_TransactionDetails')
            ->leftJoin('Cashier_TransactionIndex', 'Cashier_TransactionDetails.TransactionIndexId', '=', 'Cashier_TransactionIndex.id')
            ->where('Cashier_TransactionIndex.ORDate', $request['Day'] != null ? $request['Day'] : date('Y-m-d'))
            ->where('Cashier_TransactionIndex.UserId', $request['Teller'])
            ->whereNull('Cashier_TransactionIndex.Status')
            ->select('Cashier_TransactionIndex.ORNumber',
                'Cashier_TransactionIndex.Total',
                'Cashier_TransactionIndex.AccountNumber',
                'Cashier_TransactionIndex.PayeeName',
                'Cashier_TransactionDetails.AccountCode',
                'Cashier_TransactionIndex.CheckNo',
                'Cashier_TransactionIndex.Bank',
                'Cashier_TransactionIndex.PayeeName')
            ->orderBy('Cashier_TransactionDetails.TransactionIndexId')
            ->get();

        $powerBillsCheck = DB::table('Cashier_PaidBills')
            ->leftJoin('Billing_ServiceAccounts', 'Cashier_PaidBills.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->where('Cashier_PaidBills.PostingDate', $request['Day'] != null ? $request['Day'] : date('Y-m-d'))
            ->where('Cashier_PaidBills.Teller', $request['Teller'])
            ->whereRaw("Cashier_PaidBills.PaymentUsed LIKE '%Check%'")
            ->whereNull('Cashier_PaidBills.Status')
            ->select('Cashier_PaidBills.*', 'Billing_ServiceAccounts.ServiceAccountName', 'Billing_ServiceAccounts.OldAccountNo')
            ->get();

        $nonPowerBillsCheck = DB::table('Cashier_TransactionDetails')
            ->leftJoin('Cashier_TransactionIndex', 'Cashier_TransactionDetails.TransactionIndexId', '=', 'Cashier_TransactionIndex.id')
            ->where('Cashier_TransactionIndex.ORDate', $request['Day'] != null ? $request['Day'] : date('Y-m-d'))
            ->where('Cashier_TransactionIndex.UserId', $request['Teller'])
            ->whereRaw("Cashier_TransactionIndex.PaymentUsed LIKE '%Check%'")
            ->whereNull('Cashier_TransactionIndex.Status')
            ->select('Cashier_TransactionIndex.ORNumber',
                'Cashier_TransactionIndex.Total',
                'Cashier_TransactionIndex.AccountNumber',
                'Cashier_TransactionIndex.PayeeName',
                'Cashier_TransactionDetails.AccountCode',
                'Cashier_TransactionIndex.CheckNo',
                'Cashier_TransactionIndex.Bank',
                'Cashier_TransactionIndex.PayeeName')
            ->orderBy('Cashier_TransactionDetails.TransactionIndexId')
            ->get();

        $powerBillsCancelled = DB::table('Cashier_PaidBills')
            ->leftJoin('Billing_ServiceAccounts', 'Cashier_PaidBills.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->where('Cashier_PaidBills.PostingDate', $request['Day'] != null ? $request['Day'] : date('Y-m-d'))
            ->where('Cashier_PaidBills.Teller', $request['Teller'])
            ->where('Cashier_PaidBills.Status', 'CANCELLED')
            ->select('Cashier_PaidBills.*', 'Billing_ServiceAccounts.ServiceAccountName', 'Billing_ServiceAccounts.OldAccountNo')
            ->get();

        $nonPowerBillsCancelled = DB::table('Cashier_TransactionIndex')
            ->where('Cashier_TransactionIndex.ORDate', $request['Day'] != null ? $request['Day'] : date('Y-m-d'))
            ->where('Cashier_TransactionIndex.UserId', $request['Teller'])
            ->where('Cashier_TransactionIndex.Status', 'CANCELLED')
            ->select('Cashier_TransactionIndex.ORNumber',
                'Cashier_TransactionIndex.Total',
                'Cashier_TransactionIndex.AccountNumber',
                'Cashier_TransactionIndex.PayeeName',
                'Cashier_TransactionIndex.CheckNo',
                'Cashier_TransactionIndex.Bank',
                'Cashier_TransactionIndex.PayeeName')
            ->get();

        return view('d_c_r_summary_transactions.index', [
            'data' => $data,
            'day' => $request['Day'] != null ? $request['Day'] : date('Y-m-d'),
            'powerBills' => $powerBills,
            'nonPowerBills' => $nonPowerBills,
            'powerBillsCheck' => $powerBillsCheck,
            'nonPowerBillsCheck' => $nonPowerBillsCheck,
            'powerBillsCancelled' => $powerBillsCancelled,
            'nonPowerBillsCancelled' => $nonPowerBillsCancelled,
        ]);
    }

    public function printDcr($teller, $day) {
        if ($day != null) {
            $data = DB::table('Cashier_DCRSummaryTransactions')
                ->where('Day', $day)
                ->where('Teller', $teller)
                ->where('ReportDestination', 'COLLECTION')
                ->select('GLCode',
                    DB::raw("(SELECT Notes FROM Cashier_AccountGLCodes WHERE AccountCode=Cashier_DCRSummaryTransactions.GLCode) AS Description"),
                    DB::raw("SUM(CAST(Amount AS DECIMAL(10,2))) AS Amount")
                )
                ->groupBy('GLCode')
                ->orderBy('GLCode')
                ->get();
        } else {
            $data = DB::table('Cashier_DCRSummaryTransactions')
                ->where('Day', date('Y-m-d'))
                ->where('Teller', $teller)
                ->where('ReportDestination', 'COLLECTION')
                ->select('GLCode',
                    DB::raw("(SELECT Notes FROM Cashier_AccountGLCodes WHERE AccountCode=Cashier_DCRSummaryTransactions.GLCode) AS Description"),
                    DB::raw("SUM(CAST(Amount AS DECIMAL(10,2))) AS Amount")
                )
                ->groupBy('GLCode')
                ->orderBy('GLCode')
                ->get();
        }

        $powerBills = DB::table('Cashier_PaidBills')
            ->leftJoin('Billing_ServiceAccounts', 'Cashier_PaidBills.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->where('Cashier_PaidBills.PostingDate', $day != null ? $day : date('Y-m-d'))
            ->where('Cashier_PaidBills.Teller', $teller)
            ->whereNull('Cashier_PaidBills.Status')
            ->select('Cashier_PaidBills.*', 'Billing_ServiceAccounts.ServiceAccountName', 'Billing_ServiceAccounts.OldAccountNo',
                DB::raw("(SELECT TOP 1 BillNumber FROM Billing_Bills WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod=Cashier_PaidBills.ServicePeriod) AS BillNumber"))
            ->get();

        $nonPowerBills = DB::table('Cashier_TransactionDetails')
            ->leftJoin('Cashier_TransactionIndex', 'Cashier_TransactionDetails.TransactionIndexId', '=', 'Cashier_TransactionIndex.id')
            ->where('Cashier_TransactionIndex.ORDate', $day != null ? $day : date('Y-m-d'))
            ->where('Cashier_TransactionIndex.UserId', $teller)
            ->whereNull('Cashier_TransactionIndex.Status')
            ->select('Cashier_TransactionIndex.ORNumber',
                'Cashier_TransactionDetails.Total',
                'Cashier_TransactionIndex.AccountNumber',
                'Cashier_TransactionIndex.PayeeName',
                'Cashier_TransactionDetails.AccountCode',
                'Cashier_TransactionIndex.CheckNo',
                'Cashier_TransactionIndex.Bank',
                'Cashier_TransactionIndex.PaymentUsed',
                DB::raw("(SELECT TOP 1 OldAccountNo FROM Billing_ServiceAccounts WHERE id=Cashier_TransactionIndex.AccountNumber) AS OldAccountNo"),
                DB::raw("(SELECT TOP 1 ServiceAccountName FROM Billing_ServiceAccounts WHERE id=Cashier_TransactionIndex.AccountNumber) AS ServiceAccountName"),
                'Cashier_TransactionDetails.Particular',
                'Cashier_TransactionIndex.PayeeName')
            ->orderBy('Cashier_TransactionDetails.TransactionIndexId')
            ->get();

        $powerBillsCheck = DB::table('Cashier_PaidBills')
            ->leftJoin('Billing_ServiceAccounts', 'Cashier_PaidBills.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->leftJoin('Cashier_PaidBillsDetails', 'Cashier_PaidBills.ORNumber', '=', 'Cashier_PaidBillsDetails.ORNumber')
            ->where('Cashier_PaidBills.PostingDate', $day != null ? $day : date('Y-m-d'))
            ->where('Cashier_PaidBills.Teller', $teller)
            ->whereRaw("Cashier_PaidBillsDetails.PaymentUsed='Check'")
            ->whereNull('Cashier_PaidBills.Status')
            ->select('Cashier_PaidBills.ORNumber', 
                'Billing_ServiceAccounts.ServiceAccountName', 
                'Billing_ServiceAccounts.OldAccountNo',
                'Cashier_PaidBillsDetails.CheckNo',
                'Cashier_PaidBillsDetails.Bank',
                'Cashier_PaidBillsDetails.Amount AS Total');

        $allCheck = DB::table('Cashier_TransactionDetails')
            ->leftJoin('Cashier_TransactionIndex', 'Cashier_TransactionDetails.TransactionIndexId', '=', 'Cashier_TransactionIndex.id')
            ->leftJoin('Cashier_TransactionPaymentDetails', 'Cashier_TransactionPaymentDetails.ORNumber', '=', 'Cashier_TransactionIndex.ORNumber')
            ->where('Cashier_TransactionIndex.ORDate', $day != null ? $day : date('Y-m-d'))
            ->where('Cashier_TransactionIndex.UserId', $teller)
            ->whereRaw("Cashier_TransactionPaymentDetails.PaymentUsed = 'Check'")
            ->whereNull('Cashier_TransactionIndex.Status')
            ->select('Cashier_TransactionIndex.ORNumber',
                'Cashier_TransactionIndex.PaymentTitle as ServiceAccountName',
                DB::raw("(SELECT TOP 1 OldAccountNo FROM Billing_ServiceAccounts WHERE id=Cashier_TransactionIndex.AccountNumber) AS OldAccountNo"),
                'Cashier_TransactionPaymentDetails.CheckNo',
                'Cashier_TransactionPaymentDetails.Bank',
                'Cashier_TransactionPaymentDetails.Amount AS Total')
            ->union($powerBillsCheck)
            ->get();

        $powerBillsCancelled = DB::table('Cashier_PaidBills')
            ->leftJoin('Billing_ServiceAccounts', 'Cashier_PaidBills.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->where('Cashier_PaidBills.PostingDate', $day != null ? $day : date('Y-m-d'))
            ->where('Cashier_PaidBills.Teller', $teller)
            ->where('Cashier_PaidBills.Status', 'CANCELLED')
            ->select('Cashier_PaidBills.ORNumber', 
                'Billing_ServiceAccounts.ServiceAccountName', 
                'Billing_ServiceAccounts.OldAccountNo',
                'Cashier_PaidBills.ServicePeriod', 
                DB::raw("(SELECT TOP 1 BillNumber FROM Billing_Bills WHERE AccountNumber=Billing_ServiceAccounts.id AND ServicePeriod=Cashier_PaidBills.ServicePeriod) AS BillNumber"),
                'Cashier_PaidBills.NetAmount');

        $allCancelled = DB::table('Cashier_TransactionIndex')
            ->where('Cashier_TransactionIndex.ORDate', $day != null ? $day : date('Y-m-d'))
            ->where('Cashier_TransactionIndex.UserId', $teller)
            ->where('Cashier_TransactionIndex.Status', 'CANCELLED')
            ->select('Cashier_TransactionIndex.ORNumber',
                DB::raw("(SELECT TOP 1 ServiceAccountName FROM Billing_ServiceAccounts WHERE id=Cashier_TransactionIndex.AccountNumber) AS ServiceAccountName"),
                DB::raw("(SELECT TOP 1 OldAccountNo FROM Billing_ServiceAccounts WHERE id=Cashier_TransactionIndex.AccountNumber) AS OldAccountNo"),
                DB::raw("NULL AS ServicePeriod"),
                DB::raw("'' AS BillNumber"),
                'Cashier_TransactionIndex.Total')
            ->union($powerBillsCancelled)
            ->get();

        return view('/d_c_r_summary_transactions/print_dcr', [
            'data' => $data,
            'day' => $day != null ? $day : date('Y-m-d'),
            'powerBills' => $powerBills,
            'nonPowerBills' => $nonPowerBills,
            'allCheck' => $allCheck,
            // 'nonPowerBillsCheck' => $nonPowerBillsCheck,
            // 'powerBillsCancelled' => $powerBillsCancelled,
            'allCancelled' => $allCancelled,
        ]);
    }

    /**
     * Show the form for creating a new DCRSummaryTransactions.
     *
     * @return Response
     */
    public function create()
    {
        return view('d_c_r_summary_transactions.create');
    }

    /**
     * Store a newly created DCRSummaryTransactions in storage.
     *
     * @param CreateDCRSummaryTransactionsRequest $request
     *
     * @return Response
     */
    public function store(CreateDCRSummaryTransactionsRequest $request)
    {
        $input = $request->all();

        $dCRSummaryTransactions = $this->dCRSummaryTransactionsRepository->create($input);

        Flash::success('D C R Summary Transactions saved successfully.');

        return redirect(route('dCRSummaryTransactions.index'));
    }

    /**
     * Display the specified DCRSummaryTransactions.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $dCRSummaryTransactions = $this->dCRSummaryTransactionsRepository->find($id);

        if (empty($dCRSummaryTransactions)) {
            Flash::error('D C R Summary Transactions not found');

            return redirect(route('dCRSummaryTransactions.index'));
        }

        return view('d_c_r_summary_transactions.show')->with('dCRSummaryTransactions', $dCRSummaryTransactions);
    }

    /**
     * Show the form for editing the specified DCRSummaryTransactions.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $dCRSummaryTransactions = $this->dCRSummaryTransactionsRepository->find($id);

        if (empty($dCRSummaryTransactions)) {
            Flash::error('D C R Summary Transactions not found');

            return redirect(route('dCRSummaryTransactions.index'));
        }

        return view('d_c_r_summary_transactions.edit')->with('dCRSummaryTransactions', $dCRSummaryTransactions);
    }

    /**
     * Update the specified DCRSummaryTransactions in storage.
     *
     * @param int $id
     * @param UpdateDCRSummaryTransactionsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDCRSummaryTransactionsRequest $request)
    {
        $dCRSummaryTransactions = $this->dCRSummaryTransactionsRepository->find($id);

        if (empty($dCRSummaryTransactions)) {
            Flash::error('D C R Summary Transactions not found');

            return redirect(route('dCRSummaryTransactions.index'));
        }

        $dCRSummaryTransactions = $this->dCRSummaryTransactionsRepository->update($request->all(), $id);

        Flash::success('D C R Summary Transactions updated successfully.');

        return redirect(route('dCRSummaryTransactions.index'));
    }

    /**
     * Remove the specified DCRSummaryTransactions from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $dCRSummaryTransactions = $this->dCRSummaryTransactionsRepository->find($id);

        if (empty($dCRSummaryTransactions)) {
            Flash::error('D C R Summary Transactions not found');

            return redirect(route('dCRSummaryTransactions.index'));
        }

        $this->dCRSummaryTransactionsRepository->delete($id);

        Flash::success('D C R Summary Transactions deleted successfully.');

        return redirect(route('dCRSummaryTransactions.index'));
    }

    public function salesDcrMonitor(Request $request) {        
        $tellers = User::permission('teller create')->get();
        $offices = DB::table('Cashier_DCRSummaryTransactions')
            ->select('Office')
            ->groupBy('Office')
            ->orderBy('Office')
            ->get();

        if ($request['From'] != null || $request['To'] != null) {
            if ($request['Teller'] == 'All') {
                if ($request['Office'] == 'All') {
                        $collection = DB::table('Cashier_DCRSummaryTransactions')
                            ->whereBetween('Day', [$request['From'], $request['To']])
                            // ->where('Teller', $request['Teller'])
                            ->where('ReportDestination', 'COLLECTION')
                            ->select('GLCode',
                                DB::raw("(SELECT Notes FROM Cashier_AccountGLCodes WHERE AccountCode=Cashier_DCRSummaryTransactions.GLCode) AS Description"),
                                DB::raw("SUM(CAST(Amount AS DECIMAL(10,2))) AS Amount")
                            )
                            ->groupBy('GLCode')
                            ->orderBy('GLCode')
                            ->get();

                        $sales = DB::table('Cashier_DCRSummaryTransactions')
                            ->whereBetween('Day', [$request['From'], $request['To']])
                            // ->where('Teller', $request['Teller'])
                            ->where('ReportDestination', 'SALES')
                            ->select('GLCode',
                                DB::raw("(SELECT Notes FROM Cashier_AccountGLCodes WHERE AccountCode=Cashier_DCRSummaryTransactions.GLCode) AS Description"),
                                DB::raw("SUM(CAST(Amount AS DECIMAL(10,2))) AS Amount")
                            )
                            ->groupBy('GLCode')
                            ->orderBy('GLCode')
                            ->get();
                } else {
                    $collection = DB::table('Cashier_DCRSummaryTransactions')
                        ->whereBetween('Day', [$request['From'], $request['To']])
                        // ->where('Teller', $request['Teller'])
                        ->where('ReportDestination', 'COLLECTION')
                        ->where('Office', $request['Office'])
                        ->select('GLCode',
                            DB::raw("(SELECT Notes FROM Cashier_AccountGLCodes WHERE AccountCode=Cashier_DCRSummaryTransactions.GLCode) AS Description"),
                            DB::raw("SUM(CAST(Amount AS DECIMAL(10,2))) AS Amount")
                        )
                        ->groupBy('GLCode')
                        ->orderBy('GLCode')
                        ->get();

                    $sales = DB::table('Cashier_DCRSummaryTransactions')
                        ->whereBetween('Day', [$request['From'], $request['To']])
                        // ->where('Teller', $request['Teller'])
                        ->where('ReportDestination', 'SALES')
                        ->where('Office', $request['Office'])
                        ->select('GLCode',
                            DB::raw("(SELECT Notes FROM Cashier_AccountGLCodes WHERE AccountCode=Cashier_DCRSummaryTransactions.GLCode) AS Description"),
                            DB::raw("SUM(CAST(Amount AS DECIMAL(10,2))) AS Amount")
                        )
                        ->groupBy('GLCode')
                        ->orderBy('GLCode')
                        ->get();
                }                
            } else {
                if ($request['Office'] == 'All') {
                    $collection = DB::table('Cashier_DCRSummaryTransactions')
                        ->whereBetween('Day', [$request['From'], $request['To']])
                        ->where('Teller', $request['Teller'])
                        ->where('ReportDestination', 'COLLECTION')
                        ->select('GLCode',
                            DB::raw("(SELECT Notes FROM Cashier_AccountGLCodes WHERE AccountCode=Cashier_DCRSummaryTransactions.GLCode) AS Description"),
                            DB::raw("SUM(CAST(Amount AS DECIMAL(10,2))) AS Amount")
                        )
                        ->groupBy('GLCode')
                        ->orderBy('GLCode')
                        ->get();

                    $sales = DB::table('Cashier_DCRSummaryTransactions')
                        ->whereBetween('Day', [$request['From'], $request['To']])
                        ->where('Teller', $request['Teller'])
                        ->where('ReportDestination', 'SALES')
                        ->select('GLCode',
                            DB::raw("(SELECT Notes FROM Cashier_AccountGLCodes WHERE AccountCode=Cashier_DCRSummaryTransactions.GLCode) AS Description"),
                            DB::raw("SUM(CAST(Amount AS DECIMAL(10,2))) AS Amount")
                        )
                        ->groupBy('GLCode')
                        ->orderBy('GLCode')
                        ->get();
                } else {
                    $collection = DB::table('Cashier_DCRSummaryTransactions')
                        ->whereBetween('Day', [$request['From'], $request['To']])
                        ->where('Teller', $request['Teller'])
                        ->where('ReportDestination', 'COLLECTION')
                        ->where('Office', $request['Office'])
                        ->select('GLCode',
                            DB::raw("(SELECT Notes FROM Cashier_AccountGLCodes WHERE AccountCode=Cashier_DCRSummaryTransactions.GLCode) AS Description"),
                            DB::raw("SUM(CAST(Amount AS DECIMAL(10,2))) AS Amount")
                        )
                        ->groupBy('GLCode')
                        ->orderBy('GLCode')
                        ->get();

                    $sales = DB::table('Cashier_DCRSummaryTransactions')
                        ->whereBetween('Day', [$request['From'], $request['To']])
                        ->where('Teller', $request['Teller'])
                        ->where('ReportDestination', 'SALES')
                        ->where('Office', $request['Office'])
                        ->select('GLCode',
                            DB::raw("(SELECT Notes FROM Cashier_AccountGLCodes WHERE AccountCode=Cashier_DCRSummaryTransactions.GLCode) AS Description"),
                            DB::raw("SUM(CAST(Amount AS DECIMAL(10,2))) AS Amount")
                        )
                        ->groupBy('GLCode')
                        ->orderBy('GLCode')
                        ->get();
                }                
            }            
        } else {
            $collection = [];
            $sales = [];
        }

        return view('/d_c_r_summary_transactions/sales_dcr_monitor', [
            'collection' => $collection,
            'sales' => $sales,
            'tellers' => $tellers,
            'tellerSelect' => $request['Teller'],
            'from' => $request['From'] != null ? $request['From'] : date('Y-m-d'),
            'to' => $request['To'] != null ? $request['To'] : date('Y-m-d', strtotime('+1 day')),
            'offices' => $offices,
            'officeSelect' => $request['Office'],
        ]);
    }
}
