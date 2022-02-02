<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBillsRequest;
use App\Http\Requests\UpdateBillsRequest;
use App\Repositories\BillsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Flash;
use Response;

class BillsController extends AppBaseController
{
    /** @var  BillsRepository */
    private $billsRepository;

    public function __construct(BillsRepository $billsRepo)
    {
        $this->middleware('auth');
        $this->billsRepository = $billsRepo;
    }

    /**
     * Display a listing of the Bills.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $bills = $this->billsRepository->all();

        return view('bills.index')
            ->with('bills', $bills);
    }

    /**
     * Show the form for creating a new Bills.
     *
     * @return Response
     */
    public function create()
    {
        return view('bills.create');
    }

    /**
     * Store a newly created Bills in storage.
     *
     * @param CreateBillsRequest $request
     *
     * @return Response
     */
    public function store(CreateBillsRequest $request)
    {
        $input = $request->all();

        $bills = $this->billsRepository->create($input);

        Flash::success('Bills saved successfully.');

        return redirect(route('bills.index'));
    }

    /**
     * Display the specified Bills.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $bills = $this->billsRepository->find($id);

        if (empty($bills)) {
            Flash::error('Bills not found');

            return redirect(route('bills.index'));
        }

        return view('bills.show')->with('bills', $bills);
    }

    /**
     * Show the form for editing the specified Bills.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $bills = $this->billsRepository->find($id);

        if (empty($bills)) {
            Flash::error('Bills not found');

            return redirect(route('bills.index'));
        }

        return view('bills.edit')->with('bills', $bills);
    }

    /**
     * Update the specified Bills in storage.
     *
     * @param int $id
     * @param UpdateBillsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBillsRequest $request)
    {
        $bills = $this->billsRepository->find($id);

        if (empty($bills)) {
            Flash::error('Bills not found');

            return redirect(route('bills.index'));
        }

        $bills = $this->billsRepository->update($request->all(), $id);

        Flash::success('Bills updated successfully.');

        return redirect(route('bills.index'));
    }

    /**
     * Remove the specified Bills from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $bills = $this->billsRepository->find($id);

        if (empty($bills)) {
            Flash::error('Bills not found');

            return redirect(route('bills.index'));
        }

        $this->billsRepository->delete($id);

        Flash::success('Bills deleted successfully.');

        return redirect(route('bills.index'));
    }

    public function unbilledReadings() {
        $stats = DB::table('Billing_Bills')
            ->select('ServicePeriod',
                DB::raw("(SELECT COUNT(id) FROM Billing_Bills WHERE ServicePeriod=Billing_Bills.ServicePeriod) as 'Bills'"),
                DB::raw("(SELECT COUNT(id) FROM Billing_Readings WHERE ServicePeriod=Billing_Bills.ServicePeriod) as 'Readings'"))
            ->orderByDesc('ServicePeriod')
            ->groupBy('ServicePeriod')
            ->get();
        return view('/bills/unbilled_readings', [
            'stats' => $stats
        ]);
    }

    public function unbilledReadingsConsole($servicePeriod) {
        
        return view('/bills/unbilled_readings_console', [
            'servicePeriod' => $servicePeriod,
        ]);
    }
}
