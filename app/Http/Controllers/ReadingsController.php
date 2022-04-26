<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateReadingsRequest;
use App\Http\Requests\UpdateReadingsRequest;
use App\Repositories\ReadingsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use App\Models\Bills;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Readings;
use App\Models\Towns;
use Flash;
use Response;

class ReadingsController extends AppBaseController
{
    /** @var  ReadingsRepository */
    private $readingsRepository;

    public function __construct(ReadingsRepository $readingsRepo)
    {
        $this->middleware('auth');
        $this->readingsRepository = $readingsRepo;
    }

    /**
     * Display a listing of the Readings.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $readings = $this->readingsRepository->all();

        return view('readings.index')
            ->with('readings', $readings);
    }

    /**
     * Show the form for creating a new Readings.
     *
     * @return Response
     */
    public function create()
    {
        return view('readings.create');
    }

    /**
     * Store a newly created Readings in storage.
     *
     * @param CreateReadingsRequest $request
     *
     * @return Response
     */
    public function store(CreateReadingsRequest $request)
    {
        $input = $request->all();

        $readings = $this->readingsRepository->create($input);

        Flash::success('Readings saved successfully.');

        return redirect(route('readings.index'));
    }

    /**
     * Display the specified Readings.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $readings = $this->readingsRepository->find($id);

        if (empty($readings)) {
            Flash::error('Readings not found');

            return redirect(route('readings.index'));
        }

        return view('readings.show')->with('readings', $readings);
    }

    /**
     * Show the form for editing the specified Readings.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $readings = $this->readingsRepository->find($id);

        if (empty($readings)) {
            Flash::error('Readings not found');

            return redirect(route('readings.index'));
        }

        return view('readings.edit')->with('readings', $readings);
    }

    /**
     * Update the specified Readings in storage.
     *
     * @param int $id
     * @param UpdateReadingsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateReadingsRequest $request)
    {
        $readings = $this->readingsRepository->find($id);

        if (empty($readings)) {
            Flash::error('Readings not found');

            return redirect(route('readings.index'));
        }

        $readings = $this->readingsRepository->update($request->all(), $id);

        Flash::success('Readings updated successfully.');

        return redirect(route('readings.index'));
    }

    /**
     * Remove the specified Readings from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $readings = $this->readingsRepository->find($id);

        if (empty($readings)) {
            Flash::error('Readings not found');

            return redirect(route('readings.index'));
        }

        $this->readingsRepository->delete($id);

        Flash::success('Readings deleted successfully.');

        return redirect(route('readings.index'));
    }

    public function readingMonitor() {
        $servicePeriods = DB::table('Billing_Readings')
            ->select('ServicePeriod')
            ->groupBy('ServicePeriod')
            ->orderByDesc('ServicePeriod')
            ->limit(30)
            ->get();

        return view('/readings/reading_monitor', [
            'servicePeriods' => $servicePeriods,
        ]);
    }

    public function readingMonitorView($servicePeriod) {
        $meterReaders = User::role('Meter Reader')->orderBy('name')->get();
        $towns = Towns::orderBy('id')->get();

        return view('/readings/reading_monitor_view', [
            'meterReaders' => $meterReaders,
            'servicePeriod' => $servicePeriod,
            'towns' => $towns,
        ]);
    }

    public function getReadingsFromMeterReader(Request $request) {
        $readings = DB::table('Billing_Readings')
            ->leftJoin('Billing_ServiceAccounts', 'Billing_Readings.AccountNumber', '=', 'Billing_ServiceAccounts.id')
            ->where('Billing_Readings.MeterReader', $request['MeterReader'])
            ->where('Billing_Readings.ServicePeriod', $request['ServicePeriod'])
            ->where('Billing_ServiceAccounts.GroupCode', $request['Day'])
            ->where('Billing_ServiceAccounts.Town', $request['Town'])
            ->select('Billing_Readings.*',
                'Billing_ServiceAccounts.ServiceAccountName',
                'Billing_ServiceAccounts.SequenceCode')
            ->orderBy('Billing_Readings.ReadingTimestamp')
            ->get();

        return response()->json($readings, 200);
    }
}
