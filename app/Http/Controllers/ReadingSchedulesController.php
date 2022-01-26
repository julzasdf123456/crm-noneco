<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateReadingSchedulesRequest;
use App\Http\Requests\UpdateReadingSchedulesRequest;
use App\Repositories\ReadingSchedulesRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Users;
use App\Models\Towns;
use App\Models\ReadingSchedules;
use Illuminate\Support\Facades\DB;
use Flash;
use Response;

class ReadingSchedulesController extends AppBaseController
{
    /** @var  ReadingSchedulesRepository */
    private $readingSchedulesRepository;

    public function __construct(ReadingSchedulesRepository $readingSchedulesRepo)
    {
        $this->middleware('auth');
        $this->readingSchedulesRepository = $readingSchedulesRepo;
    }

    /**
     * Display a listing of the ReadingSchedules.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        // $readingSchedules = $this->readingSchedulesRepository->all();
        $meterReaders = User::role('Meter Reader')->get();

        return view('reading_schedules.index', [
            'meterReaders' => $meterReaders
        ]);
    }

    /**
     * Show the form for creating a new ReadingSchedules.
     *
     * @return Response
     */
    public function create()
    {
        return view('reading_schedules.create');
    }

    /**
     * Store a newly created ReadingSchedules in storage.
     *
     * @param CreateReadingSchedulesRequest $request
     *
     * @return Response
     */
    public function store(CreateReadingSchedulesRequest $request)
    {
        $input = $request->all();

        $readingSchedules = $this->readingSchedulesRepository->create($input);

        Flash::success('Reading Schedules saved successfully.');

        return redirect(route('readingSchedules.index'));
    }

    /**
     * Display the specified ReadingSchedules.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $readingSchedules = $this->readingSchedulesRepository->find($id);

        if (empty($readingSchedules)) {
            Flash::error('Reading Schedules not found');

            return redirect(route('readingSchedules.index'));
        }

        return view('reading_schedules.show')->with('readingSchedules', $readingSchedules);
    }

    /**
     * Show the form for editing the specified ReadingSchedules.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $readingSchedules = $this->readingSchedulesRepository->find($id);
        $towns = Towns::all();
        $user = Users::find($readingSchedules->MeterReader);

        if (empty($readingSchedules)) {
            Flash::error('Reading Schedules not found');

            return redirect(route('readingSchedules.index'));
        }

        return view('reading_schedules.edit', [
            'readingSchedules' => $readingSchedules,
            'towns' => $towns,
            'user' => $user,
        ]);
    }

    /**
     * Update the specified ReadingSchedules in storage.
     *
     * @param int $id
     * @param UpdateReadingSchedulesRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateReadingSchedulesRequest $request)
    {
        $readingSchedules = $this->readingSchedulesRepository->find($id);

        if (empty($readingSchedules)) {
            Flash::error('Reading Schedules not found');

            return redirect(route('readingSchedules.index'));
        }

        $readingSchedules = $this->readingSchedulesRepository->update($request->all(), $id);

        Flash::success('Reading Schedules updated successfully.');

        return redirect(route('readingSchedules.view-schedule', [$readingSchedules->MeterReader]));
    }

    /**
     * Remove the specified ReadingSchedules from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $readingSchedules = $this->readingSchedulesRepository->find($id);

        if (empty($readingSchedules)) {
            Flash::error('Reading Schedules not found');

            return redirect(route('readingSchedules.index'));
        }

        $this->readingSchedulesRepository->delete($id);

        Flash::success('Reading Schedules deleted successfully.');

        return redirect(route('readingSchedules.index'));
    }

    public function updateSchedule($userId) {
        $user = Users::find($userId);
        $towns = Towns::all();

        return view('/reading_schedules/update_schedule', [
            'user' => $user,
            'towns' => $towns,
        ]);
    }

    public function viewSchedule($userId) {
        $readingSchedules = ReadingSchedules::where('MeterReader', $userId)
            ->where('ScheduledDate', '>=', date('Y-m-d'))
            ->get();
        $user = Users::find($userId);

        return view('/reading_schedules/view_schedule', [
            'readingSchedules' => $readingSchedules,
            'user' => $user,
        ]);
    }

    public function getLatestSchedule(Request $request) {
        $readingSchedules = ReadingSchedules::where('MeterReader', $request['id'])
            ->limit(50)
            ->orderByDesc('ScheduledDate')
            ->get();

        return response()->json($readingSchedules, 200);
    }
}
