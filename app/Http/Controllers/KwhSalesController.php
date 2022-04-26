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
use App\Models\Bills;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\DistributionSystemLoss;
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
        $kwhSales = DistributionSystemLoss::orderByDesc('ServicePeriod')->get();

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

        $data = DB::table('Billing_Bills')
            ->select(
                DB::raw("(SELECT SUM(CAST(Billing_Bills.KwhUsed AS decimal(10,2))) FROM Billing_Bills LEFT JOIN Billing_ServiceAccounts ON Billing_Bills.AccountNumber=Billing_ServiceAccounts.id WHERE Billing_Bills.ServicePeriod='" . $period . "' AND Billing_Bills.AdjustmentType IS NULL) AS TotalKwhConsumption"),
                DB::raw("(SELECT SUM(CAST(Billing_Bills.KwhUsed AS decimal(10,2))) FROM Billing_Bills LEFT JOIN Billing_ServiceAccounts ON Billing_Bills.AccountNumber=Billing_ServiceAccounts.id WHERE Billing_Bills.ServicePeriod='" . $period . "' AND Billing_Bills.AdjustmentType IS NOT NULL) AS Adjustments"),)
            ->limit(1)
            ->first();

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

    public function viewSales($id) {
        $sales = DistributionSystemLoss::find($id);
        return view('/kwh_sales/view_sales', [
            'sales' => $sales,
        ]);
    }

    public function printReport($id) {
        $sales = DistributionSystemLoss::find($id);

        return view('/kwh_sales/print_report', [
            'sales' => $sales,
        ]);
    }

    public function salesDistribution() {
        $periods = DB::table('Billing_Bills')
            ->select('ServicePeriod')
            ->whereNotNull('ServicePeriod')
            ->groupBy('ServicePeriod')
            ->orderByDesc('ServicePeriod')
            ->get();

        return view('/kwh_sales/sales_distribution', [
            'periods' => $periods
        ]);
    }

    public function salesDistributionView($period) {
        $allData = DB::table('CRM_Towns AS t')
            ->select('t.Town',
                DB::raw("(SELECT COUNT(b.id) FROM Billing_Bills b LEFT JOIN Billing_ServiceAccounts sa ON b.AccountNumber=sa.id WHERE sa.Town=t.id AND b.ServicePeriod='" . $period . "') AS ConsumerCount"),
                DB::raw("(SELECT SUM(CAST(b.KwhUsed AS decimal(10,2))) FROM Billing_Bills b LEFT JOIN Billing_ServiceAccounts sa ON b.AccountNumber=sa.id WHERE sa.Town=t.id AND b.ServicePeriod='" . $period . "' AND b.ConsumerType IN ('RESIDENTIAL', 'RURAL RESIDENTIAL', 'BAPA')) AS Residentials"),
                DB::raw("(SELECT SUM(CAST(b.KwhUsed AS decimal(10,2))) FROM Billing_Bills b LEFT JOIN Billing_ServiceAccounts sa ON b.AccountNumber=sa.id WHERE sa.Town=t.id AND b.ServicePeriod='" . $period . "' AND b.ConsumerType IN ('COMMERCIAL', 'COMMERCIAL HIGH VOLTAGE')) AS Commercial"),
                DB::raw("(SELECT SUM(CAST(b.KwhUsed AS decimal(10,2))) FROM Billing_Bills b LEFT JOIN Billing_ServiceAccounts sa ON b.AccountNumber=sa.id WHERE sa.Town=t.id AND b.ServicePeriod='" . $period . "' AND b.ConsumerType IN ('IRRIGATION/WATER SYSTEMS')) AS WaterSystems"),
                DB::raw("(SELECT SUM(CAST(b.KwhUsed AS decimal(10,2))) FROM Billing_Bills b LEFT JOIN Billing_ServiceAccounts sa ON b.AccountNumber=sa.id WHERE sa.Town=t.id AND b.ServicePeriod='" . $period . "' AND b.ConsumerType IN ('INDUSTRIAL', 'INDUSTRIAL HIGH VOLTAGE')) AS Industrial"),
                DB::raw("(SELECT SUM(CAST(b.KwhUsed AS decimal(10,2))) FROM Billing_Bills b LEFT JOIN Billing_ServiceAccounts sa ON b.AccountNumber=sa.id WHERE sa.Town=t.id AND b.ServicePeriod='" . $period . "' AND b.ConsumerType IN ('PUBLIC BUILDING', 'PUBLIC BUILDING HIGH VOLTAGE')) AS PublicBldg"),
                DB::raw("(SELECT SUM(CAST(b.KwhUsed AS decimal(10,2))) FROM Billing_Bills b LEFT JOIN Billing_ServiceAccounts sa ON b.AccountNumber=sa.id WHERE sa.Town=t.id AND b.ServicePeriod='" . $period . "' AND b.ConsumerType IN ('STREET LIGHTS')) AS Streetlights"),
                DB::raw("(SELECT SUM(CAST(b.KwhUsed AS decimal(10,2))) FROM Billing_Bills b LEFT JOIN Billing_ServiceAccounts sa ON b.AccountNumber=sa.id WHERE sa.Town=t.id AND b.ServicePeriod='" . $period . "' AND b.ConsumerType IN ('COMMERCIAL HIGH VOLTAGE', 'INDUSTRIAL HIGH VOLTAGE', 'PUBLIC BUILDING HIGH VOLTAGE')) AS DemandKwh"),
                DB::raw("(SELECT SUM(CAST(b.KwhUsed AS decimal(10,2))) FROM Billing_Bills b LEFT JOIN Billing_ServiceAccounts sa ON b.AccountNumber=sa.id WHERE sa.Town=t.id AND b.ServicePeriod='" . $period . "' AND b.ConsumerType IN ('COMMERCIAL', 'INDUSTRIAL', 'PUBLIC BUILDING', 'IRRIGATION/WATER SYSTEMS')) AS LowVoltKwh"),
                DB::raw("(SELECT SUM(CAST(b.KwhUsed AS decimal(10,2))) FROM Billing_Bills b LEFT JOIN Billing_ServiceAccounts sa ON b.AccountNumber=sa.id WHERE sa.Town=t.id AND b.ServicePeriod='" . $period . "') AS KwhSold"),
                DB::raw("(SELECT SUM(CAST(b.NetAmount AS decimal(10,2))) FROM Billing_Bills b LEFT JOIN Billing_ServiceAccounts sa ON b.AccountNumber=sa.id WHERE sa.Town=t.id AND b.ServicePeriod='" . $period . "') AS TotalAmount"),
                DB::raw("(SELECT SUM(CAST(b.MissionaryElectrificationCharge AS decimal(10,2))) FROM Billing_Bills b LEFT JOIN Billing_ServiceAccounts sa ON b.AccountNumber=sa.id WHERE sa.Town=t.id AND b.ServicePeriod='" . $period . "') AS Missionary"),
                DB::raw("(SELECT SUM(CAST(b.EnvironmentalCharge AS decimal(10,2))) FROM Billing_Bills b LEFT JOIN Billing_ServiceAccounts sa ON b.AccountNumber=sa.id WHERE sa.Town=t.id AND b.ServicePeriod='" . $period . "') AS Environmental"),
                DB::raw("(SELECT SUM(CAST(b.NPCStrandedDebt AS decimal(10,2))) FROM Billing_Bills b LEFT JOIN Billing_ServiceAccounts sa ON b.AccountNumber=sa.id WHERE sa.Town=t.id AND b.ServicePeriod='" . $period . "') AS NPC"),
                DB::raw("(SELECT SUM(CAST(b.StrandedContractCosts AS decimal(10,2))) FROM Billing_Bills b LEFT JOIN Billing_ServiceAccounts sa ON b.AccountNumber=sa.id WHERE sa.Town=t.id AND b.ServicePeriod='" . $period . "') AS StrandedCC"),
                DB::raw("(SELECT SUM(CAST(b.MissionaryElectrificationREDCI AS decimal(10,2))) FROM Billing_Bills b LEFT JOIN Billing_ServiceAccounts sa ON b.AccountNumber=sa.id WHERE sa.Town=t.id AND b.ServicePeriod='" . $period . "') AS Redci"),
                DB::raw("(SELECT SUM(CAST(b.FeedInTariffAllowance AS decimal(10,2))) FROM Billing_Bills b LEFT JOIN Billing_ServiceAccounts sa ON b.AccountNumber=sa.id WHERE sa.Town=t.id AND b.ServicePeriod='" . $period . "') AS FITAll"),
                DB::raw("(SELECT SUM(CAST(b.RealPropertyTax AS decimal(10,2))) FROM Billing_Bills b LEFT JOIN Billing_ServiceAccounts sa ON b.AccountNumber=sa.id WHERE sa.Town=t.id AND b.ServicePeriod='" . $period . "') AS RPT"),
                DB::raw("(SELECT SUM(CAST(b.GenerationVAT AS decimal(10,2))) FROM Billing_Bills b LEFT JOIN Billing_ServiceAccounts sa ON b.AccountNumber=sa.id WHERE sa.Town=t.id AND b.ServicePeriod='" . $period . "') AS GenVat"),
                DB::raw("(SELECT SUM(CAST(b.TransmissionVAT AS decimal(10,2))) FROM Billing_Bills b LEFT JOIN Billing_ServiceAccounts sa ON b.AccountNumber=sa.id WHERE sa.Town=t.id AND b.ServicePeriod='" . $period . "') AS TransVat"),
                DB::raw("(SELECT SUM(CAST(b.SystemLossVAT AS decimal(10,2))) FROM Billing_Bills b LEFT JOIN Billing_ServiceAccounts sa ON b.AccountNumber=sa.id WHERE sa.Town=t.id AND b.ServicePeriod='" . $period . "') AS SysLossVat"),
                DB::raw("(SELECT SUM(CAST(b.DistributionVAT AS decimal(10,2))) FROM Billing_Bills b LEFT JOIN Billing_ServiceAccounts sa ON b.AccountNumber=sa.id WHERE sa.Town=t.id AND b.ServicePeriod='" . $period . "') AS DistVat"),
                DB::raw("(SELECT SUM(CAST(b.SeniorCitizenSubsidy AS decimal(10,2))) FROM Billing_Bills b LEFT JOIN Billing_ServiceAccounts sa ON b.AccountNumber=sa.id WHERE sa.Town=t.id AND b.ServicePeriod='" . $period . "' AND CAST(b.SeniorCitizenSubsidy AS decimal(10,2)) > 0) AS SCSubsidy"),
                DB::raw("(SELECT SUM(CAST(b.SeniorCitizenSubsidy AS decimal(10,2))) FROM Billing_Bills b LEFT JOIN Billing_ServiceAccounts sa ON b.AccountNumber=sa.id WHERE sa.Town=t.id AND b.ServicePeriod='" . $period . "' AND CAST(b.SeniorCitizenSubsidy AS decimal(10,2)) < 0) AS SCDsc")
            )
            ->orderBy("t.id")
            ->get();

        $sales = DistributionSystemLoss::where('ServicePeriod', $period)->first();

        return view('/kwh_sales/sales_distribution_view', [
            'period' => $period,
            'allData' => $allData,
            'sales' => $sales,
        ]);
    }

    public function consolidatedPerTown($period, Request $request) {
        $town = $request['Town'];
        $towns = Towns::orderBy('id')->get();

        return view('/kwh_sales/attach_consolidated_per_town', [
            'town' => $town,
            'period' => $period,
            'towns' => $towns,
        ]);
    }
}
