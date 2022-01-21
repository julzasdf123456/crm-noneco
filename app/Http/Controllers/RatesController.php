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
        $rates = $this->ratesRepository->all();

        return view('rates.index')
            ->with('rates', $rates);
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
            $district = $request['RateFor'];
            $file = $request->file('file');
            $userId = Auth::id();

            // RESIDENTIAL
            $residentialRates = new ResidentialRate($period, $userId, $district);
            Excel::import($residentialRates, $file);

            // COMMERCIAL
            $commercialRates = new CommercialRate($period, $userId, $district);
            Excel::import($commercialRates, $file);

            // INDUSTRIAL
            $industrialRates = new IndustrialRate($period, $userId, $district);
            Excel::import($industrialRates, $file);

            // WATER SYSTEMS
            $waterSystemsRates = new WaterSystemsRate($period, $userId, $district);
            Excel::import($waterSystemsRates, $file);

            // PUBLIC BUILDING
            $publicBuildingRates = new PublicBuildingRate($period, $userId, $district);
            Excel::import($publicBuildingRates, $file);

            // STREETLIGHTS
            $streetlightsRates = new StreetlightsRate($period, $userId, $district);
            Excel::import($streetlightsRates, $file);

            // INDUSTRIAL HIGH VOLTAGE
            $industrialHvRates = new IndustrialHVRate($period, $userId, $district);
            Excel::import($industrialHvRates, $file);

            // COMMERCIAL HIGH VOLTAGE
            $commercialHvRates = new CommercialHVRate($period, $userId, $district);
            Excel::import($commercialHvRates, $file);

            // PUBLIC BUILDING
            $publicBuildingHVRates = new PublicBuildingHVRate($period, $userId, $district);
            Excel::import($publicBuildingHVRates, $file);

            Flash::success('Rates for ' . $district . ' uploaded successfully.');

            return redirect(route('rates.upload-rate'));
        } else {
            return abort(404, "No file specified!");
        }
    }
}
