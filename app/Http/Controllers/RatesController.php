<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRatesRequest;
use App\Http\Requests\UpdateRatesRequest;
use App\Repositories\RatesRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ResidentialRate;
use App\Imports\CommercialRate;
use App\Imports\IndustrialRate;
use App\Imports\WaterSystemsRate;
use App\Imports\PublicBuildingRate;
use App\Imports\StreetlightsRate;
use App\Imports\IndustrialHVRate;
use App\Imports\CommercialHVRate;
use App\Imports\PublicBuildingHVRate;
use App\Models\Rates;
use App\Models\RateUploadHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Flash;
use Response;

class RatesController extends AppBaseController
{
    /** @var  RatesRepository */
    private $ratesRepository;

    public function __construct(RatesRepository $ratesRepo)
    {
        $this->middleware('auth');
        $this->ratesRepository = $ratesRepo;
    }

    /**
     * Display a listing of the Rates.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $rates = DB::table('Billing_Rates')
            ->select('ServicePeriod')
            ->groupBy('ServicePeriod')
            ->orderByDesc('ServicePeriod')
            ->get();

        return view('rates.index', [
            'rates' => $rates,
        ]);
    }

    /**
     * Show the form for creating a new Rates.
     *
     * @return Response
     */
    public function create()
    {
        return view('rates.create');
    }

    /**
     * Store a newly created Rates in storage.
     *
     * @param CreateRatesRequest $request
     *
     * @return Response
     */
    public function store(CreateRatesRequest $request)
    {
        $input = $request->all();

        $rates = $this->ratesRepository->create($input);

        Flash::success('Rates saved successfully.');

        return redirect(route('rates.index'));
    }

    /**
     * Display the specified Rates.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $rates = $this->ratesRepository->find($id);

        if (empty($rates)) {
            Flash::error('Rates not found');

            return redirect(route('rates.index'));
        }

        return view('rates.show')->with('rates', $rates);
    }

    /**
     * Show the form for editing the specified Rates.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $rates = $this->ratesRepository->find($id);

        if (empty($rates)) {
            Flash::error('Rates not found');

            return redirect(route('rates.index'));
        }

        return view('rates.edit')->with('rates', $rates);
    }

    /**
     * Update the specified Rates in storage.
     *
     * @param int $id
     * @param UpdateRatesRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateRatesRequest $request)
    {
        $rates = $this->ratesRepository->find($id);

        if (empty($rates)) {
            Flash::error('Rates not found');

            return redirect(route('rates.index'));
        }

        $rates = $this->ratesRepository->update($request->all(), $id);

        Flash::success('Rates updated successfully.');

        return redirect(route('rates.index'));
    }

    /**
     * Remove the specified Rates from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $rates = $this->ratesRepository->find($id);

        if (empty($rates)) {
            Flash::error('Rates not found');

            return redirect(route('rates.index'));
        }

        $this->ratesRepository->delete($id);

        Flash::success('Rates deleted successfully.');

        return redirect(route('rates.index'));
    }

    public function uploadRate() {
        return view('/rates/upload_rate');
    }

    public function validateRateUpload(Request $request) {
        if ($request->file('file') != null) {

            $period = $request['ServicePeriod'];
            $file = $request->file('file');
            $userId = Auth::id();

            $districts = [
                new RateUploadHelper('02', 'E.B. MAGALONA', 63),
                new RateUploadHelper('04', 'VICTORIAS', 118),
                new RateUploadHelper('03', 'MANAPLA', 173),
                new RateUploadHelper('01', 'CADIZ', 228),
                new RateUploadHelper('06', 'SAGAY', 283),
                new RateUploadHelper('07', 'ESCALANTE', 338),
                new RateUploadHelper('09', 'TOBOSO', 393),
                new RateUploadHelper('08', 'CALATRAVA', 448),
                new RateUploadHelper('05', 'SAN CARLOS', 503),
            ];

            foreach($districts as $item) {
                // RESIDENTIAL
                $residentialRates = new ResidentialRate($period, $userId, $item->districtName, $item->area, $item->startingCell, 0);
                Excel::import($residentialRates, $file);

                // COMMERCIAL
                $commercialRates = new CommercialRate($period, $userId, $item->districtName, $item->area, $item->startingCell, 0);
                Excel::import($commercialRates, $file);

                 // INDUSTRIAL
                $industrialRates = new IndustrialRate($period, $userId, $item->districtName, $item->area, $item->startingCell, 0);
                Excel::import($industrialRates, $file);

                // WATER SYSTEMS
                $waterSystemsRates = new WaterSystemsRate($period, $userId, $item->districtName, $item->area, $item->startingCell, 0);
                Excel::import($waterSystemsRates, $file);

                // PUBLIC BUILDING
                $publicBuildingRates = new PublicBuildingRate($period, $userId, $item->districtName, $item->area, $item->startingCell, 0);
                Excel::import($publicBuildingRates, $file);

                // STREETLIGHTS
                $streetlightsRates = new StreetlightsRate($period, $userId, $item->districtName, $item->area, $item->startingCell, 0);
                Excel::import($streetlightsRates, $file);

                // INDUSTRIAL HIGH VOLTAGE
                $industrialHvRates = new IndustrialHVRate($period, $userId, $item->districtName, $item->area, $item->startingCell, 0);
                Excel::import($industrialHvRates, $file);

                // COMMERCIAL HIGH VOLTAGE
                $commercialHvRates = new CommercialHVRate($period, $userId, $item->districtName, $item->area, $item->startingCell, 0);
                Excel::import($commercialHvRates, $file);

                // PUBLIC BUILDING
                $publicBuildingHVRates = new PublicBuildingHVRate($period, $userId, $item->districtName, $item->area, $item->startingCell, 0);
                Excel::import($publicBuildingHVRates, $file);
            }

            Flash::success('Rates for ' . date('F Y', strtotime($period)) . ' uploaded successfully.');

            return redirect(route('rates.index'));
        } else {
            return abort(404, "No file specified!");
        }
    }

    public function viewRates($servicePeriod) {
        $categories = DB::table('Billing_Rates')
            ->select('RateFor')
            ->where('ServicePeriod', $servicePeriod)
            ->groupBy('RateFor')
            ->get();
        
        $rates = DB::table('Billing_Rates')
            ->where('ServicePeriod', $servicePeriod)
            ->orderBy('created_at')
            ->get();

        return view('rates.view_rates', [
            'categories' => $categories,
            'servicePeriod' => $servicePeriod,
            'rates' => $rates,
        ]);
    }

    public function deleteRates($servicePeriod) {
        Rates::where('ServicePeriod', $servicePeriod)->delete();

        Flash::success('Rates for ' . date('F Y', strtotime($servicePeriod)) . ' deleted.');

        return redirect(route('rates.index'));
    }

    public function getRate(Request $request) {
        $rates = Rates::where('ServicePeriod', $request['ServicePeriod'])
            ->where('ConsumerType', $request['ConsumerType'])
            ->where('AreaCode', $request['AreaCode'])
            ->first();

        return response()->json($rates, 200);
    }
}

