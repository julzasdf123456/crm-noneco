<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePaidBillsRequest;
use App\Http\Requests\UpdatePaidBillsRequest;
use App\Repositories\PaidBillsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;    
use App\Models\ServiceAccounts;
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

        return view('paid_bills.index')
            ->with('paidBills', $paidBills);
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
                        <tr>
                            <td>' . $item->id . '</td>
                            <td>' . $item->ServiceAccountName . '</td>
                            <td>' . ServiceAccounts::getAddress($item) . '</td>
                            <td>' . $item->AccountStatus . '</td>
                        </tr>
                    '; 
            }

            return response()->json($output, 200);
        } else {
            return response()->json([], 200);
        }        
    }
}
