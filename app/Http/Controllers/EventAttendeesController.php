<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateEventAttendeesRequest;
use App\Http\Requests\UpdateEventAttendeesRequest;
use App\Repositories\EventAttendeesRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use App\Models\Events;
use App\Models\EventAttendees;
use App\Models\ServiceAccounts;
use App\Models\IDGenerator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Flash;
use Response;

class EventAttendeesController extends AppBaseController
{
    /** @var  EventAttendeesRepository */
    private $eventAttendeesRepository;

    public function __construct(EventAttendeesRepository $eventAttendeesRepo)
    {
        $this->middleware('auth');
        $this->eventAttendeesRepository = $eventAttendeesRepo;
    }

    /**
     * Display a listing of the EventAttendees.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $eventAttendees = $this->eventAttendeesRepository->all();

        return view('event_attendees.index')
            ->with('eventAttendees', $eventAttendees);
    }

    /**
     * Show the form for creating a new EventAttendees.
     *
     * @return Response
     */
    public function create()
    {
        return view('event_attendees.create');
    }

    /**
     * Store a newly created EventAttendees in storage.
     *
     * @param CreateEventAttendeesRequest $request
     *
     * @return Response
     */
    public function store(CreateEventAttendeesRequest $request)
    {
        $input = $request->all();

        $eventAttendees = $this->eventAttendeesRepository->create($input);

        Flash::success('Event Attendees saved successfully.');

        return redirect(route('eventAttendees.index'));
    }

    /**
     * Display the specified EventAttendees.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $eventAttendees = $this->eventAttendeesRepository->find($id);

        if (empty($eventAttendees)) {
            Flash::error('Event Attendees not found');

            return redirect(route('eventAttendees.index'));
        }

        return view('event_attendees.show')->with('eventAttendees', $eventAttendees);
    }

    /**
     * Show the form for editing the specified EventAttendees.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $eventAttendees = $this->eventAttendeesRepository->find($id);

        if (empty($eventAttendees)) {
            Flash::error('Event Attendees not found');

            return redirect(route('eventAttendees.index'));
        }

        return view('event_attendees.edit')->with('eventAttendees', $eventAttendees);
    }

    /**
     * Update the specified EventAttendees in storage.
     *
     * @param int $id
     * @param UpdateEventAttendeesRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateEventAttendeesRequest $request)
    {
        $eventAttendees = $this->eventAttendeesRepository->find($id);

        if (empty($eventAttendees)) {
            Flash::error('Event Attendees not found');

            return redirect(route('eventAttendees.index'));
        }

        $eventAttendees = $this->eventAttendeesRepository->update($request->all(), $id);

        Flash::success('Event Attendees updated successfully.');

        return redirect(route('eventAttendees.index'));
    }

    /**
     * Remove the specified EventAttendees from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $eventAttendees = $this->eventAttendeesRepository->find($id);

        if (empty($eventAttendees)) {
            Flash::error('Event Attendees not found');

            return redirect(route('eventAttendees.index'));
        }

        $this->eventAttendeesRepository->delete($id);

        // Flash::success('Event Attendees deleted successfully.');

        // return redirect(route('eventAttendees.index'));
        return response()->json($eventAttendees, 200);
    }

    public function addAttendees($eventId) {
        $events = Events::find($eventId);

        return view('/event_attendees/add_attendees', [
            'events' => $events,
        ]);
    }

    public function searchAccountForAttendees(Request $request) {
        $search = $request['Search'];

        $serviceAccounts = DB::table('Billing_ServiceAccounts')
            ->leftJoin('users', 'Billing_ServiceAccounts.MeterReader', '=', 'users.id')
            ->select('Billing_ServiceAccounts.id',
                    'Billing_ServiceAccounts.ServiceAccountName',
                    'users.name',
                    'Billing_ServiceAccounts.OldAccountNo',)
            ->whereRaw("ServiceAccountName LIKE '%" . $search . "%' OR OldAccountNo LIKE '%" . $search . "%' OR Billing_ServiceAccounts.id LIKE '%" . $search . "%'")
            ->get();

        $output = "";
        foreach ($serviceAccounts as $item) {
            $output .= "<tr>" .
                            "<td>" . $item->OldAccountNo . "</td>" .
                            "<td>" . $item->ServiceAccountName . "</td>" .
                            "<td class='text-right'><button onclick=addToAttendance('" . $item->id . "') class='btn btn-xs btn-success'>Add</button></td>" .
                        "</tr>";
        }

        return response()->json($output, 200);
    }

    public function addAttendance(Request $request) {
        $id = $request['id'];
        $eventId = $request['EventId'];

        $account = DB::table('Billing_ServiceAccounts')
            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
            ->select('Billing_ServiceAccounts.id',
                    'Billing_ServiceAccounts.ServiceAccountName',
                    'CRM_Towns.Town',
                    'CRM_Barangays.Barangay',
                    'Billing_ServiceAccounts.OldAccountNo',
                    'Billing_ServiceAccounts.Purok',
                    )
            ->where('Billing_ServiceAccounts.id', $id)
            ->first();


        $output = "";

        if ($account != null) {
            $attendance = EventAttendees::where('AccountNumber', $account->id)
                ->where('EventId', $eventId)
                ->first();

            if ($attendance == null) {
                $attendance = new EventAttendees;
                $attendance->id = IDGenerator::generateIDandRandString();
                $attendance->EventId = $eventId;
                $attendance->AccountNumber = $account->id;
                $attendance->Name = $account->ServiceAccountName;
                $attendance->Address = ServiceAccounts::getAddress($account);
                $attendance->RegisteredAt = env('APP_LOCATION');
                $attendance->RegistationMedium = 'WEB';
                $attendance->UserId = Auth::id();
                $attendance->save();

                $output = "ok";
            } else {
                $output = 'exist';
            }
        }

        return response()->json($output, 200);
    }

    public function getAttendees(Request $request) {
        $eventId = $request['id'];

        $attendees = EventAttendees::where('EventId', $eventId)->get();

        $output = "";
        foreach($attendees as $item) {
            $output .= "<tr>
                            <td>" . $item->AccountNumber . "</td>
                            <td>" . $item->Name . "</td>
                            <td>" . $item->Address . "</td>
                            <td>" . date('M d, Y h:i A', strtotime($item->created_at)) . "</td>
                            <td class='text-right'><button onclick=deleteAttendee('" . $item->id . "') class='btn btn-sm btn-link text-danger'><i class='fas fa-trash'></i></button></td>
                        </tr>";
        }

        return response()->json($output, 200);
    }

    public function delete(Request $request) {
        $event = EventAttendees::find($request['id']);

        if ($event != null) {
            $event->delete();
        }

        return response()->json('ok', 200);
    }

    public function addWalkin(Request $request) {
        $name = $request['Name'];
        $address = $request['Address'];
        $eventId = $request['EventId'];

        $attendance = EventAttendees::where('Name', $name)
            ->where('Address', $address)
            ->first();

        $output = "";
        if ($attendance == null) {
            $attendance = new EventAttendees;
            $attendance->id = IDGenerator::generateIDandRandString();
            $attendance->EventId = $eventId;
            $attendance->Name = $name;
            $attendance->Address = $address;
            $attendance->RegisteredAt = env('APP_LOCATION');
            $attendance->RegistationMedium = 'WEB';
            $attendance->UserId = Auth::id();
            $attendance->save();

            $output = "ok";
        } else {
            $output = 'exist';
        }

        return response()->json($output, 200);
    }
}
