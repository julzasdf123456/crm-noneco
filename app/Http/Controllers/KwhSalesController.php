<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateKwhSalesRequest;
use App\Http\Requests\UpdateKwhSalesRequest;
use App\Repositories\KwhSalesRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use App\Models\Towns;
use App\Models\IDGenerator;
use App\Models\KwhSales;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Flash;
use Response;

class KwhSalesController extends AppBaseController
{
    /** @var  KwhSalesRepository */
    private $kwhSalesRepository;

    public function __construct(KwhSalesRepository $kwhSalesRepo)
    {
        $this->middleware('auth');
        $this->kwhSalesRepository = $kwhSalesRepo;
    }

    /**
     * Display a listing of the KwhSales.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $kwhSales = DB::table('Billing_KwhSales')
            ->select('ServicePeriod',
                DB::raw('SUM(CAST(NoOfConsumers AS DECIMAL(10,2))) AS TotalConsumers'),
                DB::raw('SUM(CAST(ConsumedKwh AS DECIMAL(10,2))) AS ConsumedKwh'),
                DB::raw('SUM(CAST(BilledKwh AS DECIMAL(10,2))) AS BilledKwh'))
            ->groupBy('ServicePeriod')
            ->orderByDesc('ServicePeriod')
            ->get();

        return view('kwh_sales.index', [
            'kwhSales' => $kwhSales,
        ]);
    }

    /**
     * Show the form for creating a new KwhSales.
     *
     * @return Response
     */
    public function create()
    {
        return view('kwh_sales.create');
    }

    /**
     * Store a newly created KwhSales in storage.
     *
     * @param CreateKwhSalesRequest $request
     *
     * @return Response
     */
    public function store(CreateKwhSalesRequest $request)
    {
        $input = $request->all();

        $kwhSales = $this->kwhSalesRepository->create($input);

        Flash::success('Kwh Sales saved successfully.');

        return redirect(route('kwhSales.index'));
    }

    /**
     * Display the specified KwhSales.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $kwhSales = $this->kwhSalesRepository->find($id);

        if (empty($kwhSales)) {
            Flash::error('Kwh Sales not found');

            return redirect(route('kwhSales.index'));
        }

        return view('kwh_sales.show')->with('kwhSales', $kwhSales);
    }

    /**
     * Show the form for editing the specified KwhSales.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $kwhSales = $this->kwhSalesRepository->find($id);

        if (empty($kwhSales)) {
            Flash::error('Kwh Sales not found');

            return redirect(route('kwhSales.index'));
        }

        return view('kwh_sales.edit')->with('kwhSales', $kwhSales);
    }

    /**
     * Update the specified KwhSales in storage.
     *
     * @param int $id
     * @param UpdateKwhSalesRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateKwhSalesRequest $request)
    {
        $kwhSales = $this->kwhSalesRepository->find($id);

        if (empty($kwhSales)) {
            Flash::error('Kwh Sales not found');

            return redirect(route('kwhSales.index'));
        }

        $kwhSales = $this->kwhSalesRepository->update($request->all(), $id);

        Flash::success('Kwh Sales updated successfully.');

        return redirect(route('kwhSales.index'));
    }

    /**
     * Remove the specified KwhSales from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $kwhSales = $this->kwhSalesRepository->find($id);

        if (empty($kwhSales)) {
            Flash::error('Kwh Sales not found');

            return redirect(route('kwhSales.index'));
        }

        $this->kwhSalesRepository->delete($id);

        Flash::success('Kwh Sales deleted successfully.');

        return redirect(route('kwhSales.index'));
    }

    public function generateNew(Request $request) {
        $period = $request['ServicePeriod'];

        $data = DB::table('CRM_Towns')
            ->select('CRM_Towns.id',
                'CRM_Towns.Town',
                DB::raw("(SELECT COUNT(Billing_Bills.id) FROM Billing_Bills LEFT JOIN Billing_ServiceAccounts ON Billing_Bills.AccountNumber=Billing_ServiceAccounts.id WHERE Billing_Bills.ServicePeriod='" . $period . "' AND Billing_ServiceAccounts.id LIKE CONCAT(CRM_Towns.id, '-%')) AS ConsumerCount"),
                DB::raw("(SELECT SUM(CAST(Billing_Bills.KwhUsed AS decimal(10,2))) FROM Billing_Bills LEFT JOIN Billing_ServiceAccounts ON Billing_Bills.AccountNumber=Billing_ServiceAccounts.id WHERE Billing_Bills.ServicePeriod='" . $period . "' AND Billing_ServiceAccounts.id LIKE CONCAT(CRM_Towns.id, '-%')) AS TotalKwhConsumption"))
            ->get();

        return view('/kwh_sales/generate_new', [
            'data' => $data,
            'period' => $period,
        ]);
    }

    public function saveSalesReport(Request $request) {
        $periods = $request->ServicePeriod;
        foreach($periods as $key => $value) {
            $kwhSales = new KwhSales;
            $kwhSales->id = IDGenerator::generateIDandRandString();
            $kwhSales->ServicePeriod = $value;
            $kwhSales->Town = $request->Town[$key];
            $kwhSales->NoOfConsumers = $request->NoOfConsumers[$key];
            $kwhSales->ConsumedKwh = $request->ConsumedKwh[$key];
            $kwhSales->BilledKwh = $request->BilledKwh[$key];
            $kwhSales->save();
        }

        return redirect(route('kwhSales.index'));
    }
}
