<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTicketsRequest;
use App\Http\Requests\UpdateTicketsRequest;
use App\Repositories\TicketsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Towns;
use App\Models\ServiceConnectionCrew;
use App\Models\Barangays;
use App\Models\Tickets;
use App\Models\TicketLogs;
use App\Models\IDGenerator;
use Illuminate\Support\Facades\Auth;
use Flash;
use Response;

class TicketsController extends AppBaseController
{
    /** @var  TicketsRepository */
    private $ticketsRepository;

    public function __construct(TicketsRepository $ticketsRepo)
    {
        $this->middleware('auth');
        $this->ticketsRepository = $ticketsRepo;
    }

    /**
     * Display a listing of the Tickets.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $tickets = $this->ticketsRepository->all();

        return view('tickets.index')
            ->with('tickets', $tickets);
    }

    /**
     * Show the form for creating a new Tickets.
     *
     * @return Response
     */
    public function create()
    {
        return view('tickets.create');
    }

    /**
     * Store a newly created Tickets in storage.
     *
     * @param CreateTicketsRequest $request
     *
     * @return Response
     */
    public function store(CreateTicketsRequest $request)
    {
        $input = $request->all();

        $tickets = $this->ticketsRepository->create($input);

        Flash::success('Tickets saved successfully.');

        // CREATE LOG
        $ticketLog = new TicketLogs;
        $ticketLog->id = IDGenerator::generateID();
        $ticketLog->TicketId = $tickets->id;
        $ticketLog->Log = "Received";
        $ticketLog->UserId = Auth::id();
        $ticketLog->save();

        return redirect(route('tickets.print-ticket', [$tickets->id]));
    }

    /**
     * Display the specified Tickets.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $tickets = DB::table('CRM_Tickets')
                ->leftJoin('CRM_Barangays', 'CRM_Tickets.Barangay', '=', 'CRM_Barangays.id')
                ->leftJoin('CRM_Towns', 'CRM_Tickets.Town', '=', 'CRM_Towns.id')
                ->leftJoin('CRM_TicketsRepository', 'CRM_Tickets.Ticket', '=', 'CRM_TicketsRepository.id')
                ->leftJoin('CRM_ServiceConnectionCrew', 'CRM_Tickets.CrewAssigned', '=', 'CRM_ServiceConnectionCrew.id')
                ->where('CRM_Tickets.id', $id)
                ->select('CRM_Tickets.id',
                    'CRM_Tickets.AccountNumber',
                    'CRM_Tickets.ConsumerName',
                    'CRM_Towns.Town',
                    'CRM_Barangays.Barangay',
                    'CRM_Tickets.Sitio',
                    'CRM_TicketsRepository.ParentTicket',
                    'CRM_TicketsRepository.Name as Ticket',
                    'CRM_TicketsRepository.Type as TicketType',
                    'CRM_Tickets.Reason',
                    'CRM_Tickets.ContactNumber',
                    'CRM_Tickets.ReportedBy',
                    'CRM_Tickets.ORNumber',
                    'CRM_Tickets.ORDate',
                    'CRM_Tickets.GeoLocation',
                    'CRM_Tickets.Neighbor1',
                    'CRM_Tickets.Neighbor2',
                    'CRM_Tickets.Notes',
                    'CRM_Tickets.Status',
                    'CRM_Tickets.DateTimeDownloaded',
                    'CRM_Tickets.DateTimeLinemanArrived',
                    'CRM_Tickets.DateTimeLinemanExecuted',
                    'CRM_Tickets.UserId',
                    'CRM_Tickets.Office',  
                    'CRM_ServiceConnectionCrew.StationName',
                    'CRM_Tickets.created_at',
                    'CRM_Tickets.updated_at',
                    'CRM_Tickets.Trash')
                ->first();

        $ticketLogs = DB::table('CRM_TicketLogs')
            ->leftJoin('users', 'CRM_TicketLogs.UserId', '=', 'users.id')
            ->where('TicketId', $id)
            ->select('CRM_TicketLogs.*', 'users.name')
            ->orderByDesc('created_at')
            ->get();

        if ($tickets->AccountNumber != null) {
            $history = DB::table('CRM_Tickets')
                ->leftJoin('CRM_Barangays', 'CRM_Tickets.Barangay', '=', 'CRM_Barangays.id')
                ->leftJoin('CRM_Towns', 'CRM_Tickets.Town', '=', 'CRM_Towns.id')
                ->leftJoin('CRM_TicketsRepository', 'CRM_Tickets.Ticket', '=', 'CRM_TicketsRepository.id')
                ->leftJoin('CRM_ServiceConnectionCrew', 'CRM_Tickets.CrewAssigned', '=', 'CRM_ServiceConnectionCrew.id')
                ->where('CRM_Tickets.AccountNumber', $tickets->AccountNumber)
                ->where('CRM_Tickets.id', '!=', $id)
                ->where(function ($query) {
                        $query->where('CRM_Tickets.Trash', 'No')
                            ->orWhereNull('CRM_Tickets.Trash');
                    })
                ->select('CRM_Tickets.id',
                    'CRM_Tickets.AccountNumber',
                    'CRM_Tickets.ConsumerName',
                    'CRM_Towns.Town',
                    'CRM_Barangays.Barangay',
                    'CRM_Tickets.Sitio',
                    'CRM_TicketsRepository.ParentTicket',
                    'CRM_TicketsRepository.Name as Ticket',
                    'CRM_TicketsRepository.Type as TicketType',
                    'CRM_Tickets.Reason',
                    'CRM_Tickets.ContactNumber',
                    'CRM_Tickets.ReportedBy',
                    'CRM_Tickets.ORNumber',
                    'CRM_Tickets.ORDate',
                    'CRM_Tickets.GeoLocation',
                    'CRM_Tickets.Neighbor1',
                    'CRM_Tickets.Neighbor2',
                    'CRM_Tickets.Notes',
                    'CRM_Tickets.Status',
                    'CRM_Tickets.DateTimeDownloaded',
                    'CRM_Tickets.DateTimeLinemanArrived',
                    'CRM_Tickets.DateTimeLinemanExecuted',
                    'CRM_Tickets.UserId',
                    'CRM_ServiceConnectionCrew.StationName',
                    'CRM_Tickets.created_at',
                    'CRM_Tickets.updated_at',
                    'CRM_Tickets.Trash')
                ->orderByDesc('CRM_Tickets.created_at')
                ->get();
        } else {
            $history = null;
        }

        if (empty($tickets)) {
            Flash::error('Tickets not found');

            return redirect(route('tickets.index'));
        }

        return view('tickets.show', [
            'tickets' => $tickets, 
            'ticketLogs' => $ticketLogs,
            'history' => $history,
        ]);
    }

    /**
     * Show the form for editing the specified Tickets.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $tickets = $this->ticketsRepository->find($id);
        $cond = 'edit';

        $towns = Towns::orderBy('Town')->pluck('Town', 'id');

        // TICKETS MATRIX
        $parentTickets = DB::table('CRM_TicketsRepository')->whereNull('ParentTicket')->orderBy('Name')->get();

        $crew = ServiceConnectionCrew::orderBy('StationName')->pluck('StationName', 'id');

        if (empty($tickets)) {
            Flash::error('Tickets not found');

            return redirect(route('tickets.index'));
        }

        return view('tickets.edit', [
            'tickets' => $tickets, 
            'towns' => $towns,
            'parentTickets' => $parentTickets,
            'crew' => $crew,
            'cond' => $cond,
        ]);
    }

    /**
     * Update the specified Tickets in storage.
     *
     * @param int $id
     * @param UpdateTicketsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTicketsRequest $request)
    {
        $tickets = $this->ticketsRepository->find($id);

        if (empty($tickets)) {
            Flash::error('Tickets not found');

            return redirect(route('tickets.index'));
        }

        $tickets = $this->ticketsRepository->update($request->all(), $id);

        $ticketLog = new TicketLogs;
        $ticketLog->id = IDGenerator::generateID();
        $ticketLog->TicketId = $id;
        $ticketLog->Log = "Ticket Updated";
        $ticketLog->UserId = Auth::id();
        $ticketLog->save();

        Flash::success('Tickets updated successfully.');

        return redirect(route('tickets.index'));
    }

    /**
     * Remove the specified Tickets from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $tickets = $this->ticketsRepository->find($id);

        if (empty($tickets)) {
            Flash::error('Tickets not found');

            return redirect(route('tickets.index'));
        }

        $tickets->Trash = 'Yes';
        $tickets->UserId = Auth::id();
        $tickets->save();
        // $this->ticketsRepository->delete($id);

        // CREATE LOG
        $ticketLog = new TicketLogs;
        $ticketLog->id = IDGenerator::generateID();
        $ticketLog->TicketId = $id;
        $ticketLog->Log = "Ticket Moved to Trash";
        $ticketLog->UserId = Auth::id();
        $ticketLog->save();

        Flash::success('Tickets deleted successfully.');

        return redirect(route('tickets.index'));
    }

    public function fetchTickets(Request $request) {
        if ($request->ajax()) {
            $query = $request->get('query');
            
            if ($query != '' ) {
                $data = DB::table('CRM_Tickets')
                    ->leftJoin('CRM_Barangays', 'CRM_Tickets.Barangay', '=', 'CRM_Barangays.id')                    
                    ->leftJoin('CRM_Towns', 'CRM_Tickets.Town', '=', 'CRM_Towns.id')                
                    ->leftJoin('CRM_TicketsRepository', 'CRM_Tickets.Ticket', '=', 'CRM_TicketsRepository.id')
                    ->select('CRM_Tickets.id as id',
                                    'CRM_Tickets.AccountNumber',
                                    'CRM_Tickets.ConsumerName',
                                    'CRM_TicketsRepository.Name as Ticket', 
                                    'CRM_Tickets.Status',  
                                    'CRM_Tickets.Sitio as Sitio', 
                                    'CRM_Tickets.created_at', 
                                    'CRM_Towns.Town as Town',
                                    'CRM_Tickets.Office',  
                                    'CRM_Barangays.Barangay as Barangay')
                    ->where(function ($query) {
                                        $query->where('CRM_Tickets.Trash', 'No')
                                            ->orWhereNull('CRM_Tickets.Trash');
                                    })
                    ->where('CRM_Tickets.id', 'LIKE', '%' . $query . '%')
                    ->orWhere('CRM_Tickets.ConsumerName', 'LIKE', '%' . $query . '%')
                    ->orWhere('CRM_Tickets.AccountNumber', 'LIKE', '%' . $query . '%')                    
                    ->orderBy('CRM_Tickets.ConsumerName')
                    ->get();
            } else {
                $data = DB::table('CRM_Tickets')
                    ->leftJoin('CRM_Barangays', 'CRM_Tickets.Barangay', '=', 'CRM_Barangays.id')                    
                    ->leftJoin('CRM_Towns', 'CRM_Tickets.Town', '=', 'CRM_Towns.id')                
                    ->leftJoin('CRM_TicketsRepository', 'CRM_Tickets.Ticket', '=', 'CRM_TicketsRepository.id')
                    ->select('CRM_Tickets.id as id',
                                    'CRM_Tickets.AccountNumber',
                                    'CRM_Tickets.ConsumerName',
                                    'CRM_TicketsRepository.Name as Ticket', 
                                    'CRM_Tickets.Status',  
                                    'CRM_Tickets.Sitio as Sitio', 
                                    'CRM_Tickets.created_at', 
                                    'CRM_Towns.Town as Town',
                                    'CRM_Tickets.Office',  
                                    'CRM_Barangays.Barangay as Barangay')
                    ->where(function ($query) {
                                        $query->where('CRM_Tickets.Trash', 'No')
                                            ->orWhereNull('CRM_Tickets.Trash');
                                    })
                    ->orderByDesc('CRM_Tickets.created_at')
                    ->take(15)
                    ->get();
            }

            $total_row = $data->count();
            if ($total_row > 0) {
                $output = '';
                foreach ($data as $row) {

                    $output .= '
                        <div class="col-md-10 offset-md-1 col-lg-10 offset-lg-1" style="margin-top: 10px;">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 col-lg-6">
                                            <div>
                                                <h4>' .$row->ConsumerName . '</h4>
                                                <p class="text-muted" style="margin-bottom: 0;">Acount Number: ' . $row->AccountNumber . '</p>
                                                <p class="text-muted" style="margin-bottom: 0;">' . $row->Barangay . ', ' . $row->Town  . '</p>
                                                <a href="' . route('tickets.show', [$row->id]) . '" class="text-primary" style="margin-top: 5px; padding: 8px;" title="View"><i class="fas fa-eye"></i></a>
                                                <a href="' . route('tickets.edit', [$row->id]) . '" class="text-warning" style="margin-top: 5px; padding: 8px;" title="Edit"><i class="fas fa-pen"></i></a>
                                            </div>     
                                        </div> 

                                        <div class="col-md-6 col-lg-6 d-sm-none d-md-block d-none d-sm-block" style="border-left: 2px solid #007bff; padding-left: 15px;">
                                            <div>
                                                <p class="text-muted" style="margin-bottom: 0;">Ticket: <strong>' . $row->Ticket . '</strong></p>
                                                <p class="text-muted" style="margin-bottom: 0;">Ticket Filed at: <strong>' . date('F d, Y', strtotime($row->created_at)) . '</strong></p>
                                                <p class="text-muted" style="margin-bottom: 0;">Status: <strong>' . $row->Status . '</strong></p>
                                                <p class="text-muted" style="margin-bottom: 0;">Office: <strong>' . $row->Office . '</strong></p>
                                            </div>     
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>   
                    ';
                }                
            } else {
                $output = '<p class="text-center">No data found.</p>';
            }

            $data = [
                'table_data' => $output
            ];

            echo json_encode($data);
        }
    } 

    public function createSelect() {
        return view('/tickets/create_select');
    }

    public function getCreateAjax(Request $request) {
        if ($request->ajax()) {
            if ($request['params'] == null) {
                $serviceAccounts = DB::table('Billing_ServiceAccounts')
                            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
                            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
                            ->select('Billing_ServiceAccounts.ServiceAccountName', 'Billing_ServiceAccounts.id', 'CRM_Towns.Town', 'CRM_Barangays.Barangay')
                            ->orderBy('Billing_ServiceAccounts.ServiceAccountName')
                            ->take(25)
                            ->get();
            } else {
                $serviceAccounts = DB::table('Billing_ServiceAccounts')
                            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
                            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
                            ->select('Billing_ServiceAccounts.ServiceAccountName', 'Billing_ServiceAccounts.id', 'CRM_Towns.Town', 'CRM_Barangays.Barangay')
                            ->where('Billing_ServiceAccounts.ServiceAccountName', 'LIKE', '%' . $request['params'] . '%')
                            ->orWhere('Billing_ServiceAccounts.id', 'LIKE', '%' . $request['params'] . '%')
                            ->orderBy('Billing_ServiceAccounts.ServiceAccountName')
                            ->get();
            }

            $output = "";

            foreach($serviceAccounts as $item) {
                $output .= '<tr>' .
                        '<td>' . $item->id . '</td>' .
                        '<td>' . $item->ServiceAccountName . '</td>' .
                        '<td>' . $item->Barangay . ', ' . $item->Town . '</td>' .
                        '<td>' . 
                            '<a href="' . route("tickets.create-new", [$item->id]) . '"><i class="fas fa-arrow-alt-circle-right"></i></a>' .
                        '</td>' .
                    '</tr>';
            }
            
            return response()->json($output, 200);
        }
    }

    public function createNew($id) { // id is account number
        if ($id != null) {
            $serviceAccount = DB::table('Billing_ServiceAccounts')
                ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
                ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
                ->select('Billing_ServiceAccounts.ServiceAccountName', 
                    'Billing_ServiceAccounts.id', 
                    'CRM_Towns.Town', 
                    'CRM_Barangays.Barangay', 
                    'Billing_ServiceAccounts.Town as TownId',
                    'Billing_ServiceAccounts.Barangay as BarangayId',
                    'Billing_ServiceAccounts.Purok')
                ->where('Billing_ServiceAccounts.id', $id)
                ->first();
        } else {
            $serviceAccount = null;
        }

        $towns = Towns::orderBy('Town')->pluck('Town', 'id');

        // TICKETS MATRIX
        $parentTickets = DB::table('CRM_TicketsRepository')->whereNull('ParentTicket')->orderBy('Name   ')->get();

        $crew = ServiceConnectionCrew::orderBy('StationName')->pluck('StationName', 'id');

        $history = DB::table('CRM_Tickets')
                        ->leftJoin('CRM_TicketsRepository', 'CRM_Tickets.Ticket', '=', 'CRM_TicketsRepository.id')
                        ->leftJoin('CRM_Towns', 'CRM_Tickets.Town', '=', 'CRM_Towns.id')
                        ->leftJoin('CRM_Barangays', 'CRM_Tickets.Barangay', '=', 'CRM_Barangays.id')
                        ->where('CRM_Tickets.AccountNumber', $id)
                        ->select('CRM_Tickets.ConsumerName', 
                            'CRM_Tickets.id',
                            'CRM_Towns.Town',
                            'CRM_Barangays.Barangay',
                            'CRM_TicketsRepository.Name',
                            'CRM_TicketsRepository.ParentTicket',
                            'CRM_Tickets.created_at',
                            'CRM_Tickets.Reason',
                            'CRM_Tickets.Status',)
                        ->orderByDesc('CRM_Tickets.created_at')
                        ->get();

        $cond = 'new';

        return view('tickets.create',   [
            'serviceAccount' => $serviceAccount,
            'towns' => $towns,
            'parentTickets' => $parentTickets,
            'crew' => $crew,
            'history' => $history,
            'cond' => $cond,
        ]);
    }

    public function printTicket($id) {
        $tickets = DB::table('CRM_Tickets')
                ->leftJoin('CRM_Barangays', 'CRM_Tickets.Barangay', '=', 'CRM_Barangays.id')
                ->leftJoin('CRM_Towns', 'CRM_Tickets.Town', '=', 'CRM_Towns.id')
                ->leftJoin('CRM_TicketsRepository', 'CRM_Tickets.Ticket', '=', 'CRM_TicketsRepository.id')
                ->leftJoin('CRM_ServiceConnectionCrew', 'CRM_Tickets.CrewAssigned', '=', 'CRM_ServiceConnectionCrew.id')
                ->where('CRM_Tickets.id', $id)
                ->select('CRM_Tickets.id',
                    'CRM_Tickets.AccountNumber',
                    'CRM_Tickets.ConsumerName',
                    'CRM_Towns.Town',
                    'CRM_Barangays.Barangay',
                    'CRM_Tickets.Sitio',
                    'CRM_TicketsRepository.ParentTicket',
                    'CRM_TicketsRepository.Name as Ticket',
                    'CRM_TicketsRepository.Type as TicketType',
                    'CRM_Tickets.Reason',
                    'CRM_Tickets.ContactNumber',
                    'CRM_Tickets.ReportedBy',
                    'CRM_Tickets.ORNumber',
                    'CRM_Tickets.ORDate',
                    'CRM_Tickets.GeoLocation',
                    'CRM_Tickets.Neighbor1',
                    'CRM_Tickets.Neighbor2',
                    'CRM_Tickets.Notes',
                    'CRM_Tickets.Status',
                    'CRM_Tickets.DateTimeDownloaded',
                    'CRM_Tickets.DateTimeLinemanArrived',
                    'CRM_Tickets.DateTimeLinemanExecuted',
                    'CRM_Tickets.UserId',
                    'CRM_ServiceConnectionCrew.StationName',
                    'CRM_Tickets.created_at',
                    'CRM_Tickets.updated_at',
                    'CRM_Tickets.Trash')
                ->first();

        if (empty($tickets)) {
            Flash::error('Tickets not found');

            return redirect(route('tickets.index'));
        }

        return view('/tickets/print_ticket', [
            'tickets' => $tickets
        ]);
    }

    public function trash() {
        $tickets = DB::table('CRM_Tickets')
            ->leftJoin('CRM_Barangays', 'CRM_Tickets.Barangay', '=', 'CRM_Barangays.id')
            ->leftJoin('CRM_Towns', 'CRM_Tickets.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_TicketsRepository', 'CRM_Tickets.Ticket', '=', 'CRM_TicketsRepository.id')
            ->leftJoin('CRM_ServiceConnectionCrew', 'CRM_Tickets.CrewAssigned', '=', 'CRM_ServiceConnectionCrew.id')
            ->where('CRM_Tickets.Trash', 'Yes')
            ->select('CRM_Tickets.id',
                'CRM_Tickets.AccountNumber',
                'CRM_Tickets.ConsumerName',
                'CRM_Towns.Town',
                'CRM_Barangays.Barangay',
                'CRM_Tickets.Sitio',
                'CRM_TicketsRepository.ParentTicket',
                'CRM_TicketsRepository.Name as Ticket',
                'CRM_TicketsRepository.Type as TicketType',
                'CRM_Tickets.Reason',
                'CRM_Tickets.ContactNumber',
                'CRM_Tickets.ReportedBy',
                'CRM_Tickets.ORNumber',
                'CRM_Tickets.ORDate',
                'CRM_Tickets.GeoLocation',
                'CRM_Tickets.Neighbor1',
                'CRM_Tickets.Neighbor2',
                'CRM_Tickets.Notes',
                'CRM_Tickets.Status',
                'CRM_Tickets.DateTimeDownloaded',
                'CRM_Tickets.DateTimeLinemanArrived',
                'CRM_Tickets.DateTimeLinemanExecuted',
                'CRM_Tickets.UserId',
                'CRM_ServiceConnectionCrew.StationName',
                'CRM_Tickets.created_at',
                'CRM_Tickets.updated_at',
                'CRM_Tickets.Trash')
            ->get();
        return view('/tickets/trash', ['tickets' => $tickets]);
    }

    public function restoreTicket($id) {
        $tickets = Tickets::find($id);
        $tickets->Trash = null;
        $tickets->UserId = Auth::id();
        $tickets->save();

        // CREATE LOG
        $ticketLog = new TicketLogs;
        $ticketLog->id = IDGenerator::generateID();
        $ticketLog->TicketId = $id;
        $ticketLog->Log = "Ticket Restored";
        $ticketLog->UserId = Auth::id();
        $ticketLog->save();

        return redirect(route('tickets.show', [$id]));
    }

    public function updateDateFiled(Request $request) {
        if ($request->ajax()) {
            $ticket = Tickets::find($request['id']);
            $ticket->created_at = date('Y-m-d H:i:s', strtotime($request['created_at']));
            $ticket->save();

            // CREATE LOG
            $ticketLog = new TicketLogs;
            $ticketLog->id = IDGenerator::generateID();
            $ticketLog->TicketId = $request['id'];
            $ticketLog->Log = "Date Filed Updated";
            $ticketLog->LogDetails = "Date filed changed from " . $ticket->created_at . " to " . $request['created_at'];
            $ticketLog->UserId = Auth::id();
            $ticketLog->save();

            return response()->json(['response' => 'ok'], 200);
        }
    }

    public function updateDateDownloaded(Request $request) {
        if ($request->ajax()) {
            $ticket = Tickets::find($request['id']);
            $ticket->DateTimeDownloaded = date('Y-m-d H:i:s', strtotime($request['DateTimeDownloaded']));
            $ticket->Status = "Forwarded to Crew";
            $ticket->save();

            // CREATE LOG
            $ticketLog = new TicketLogs;
            $ticketLog->id = IDGenerator::generateID();
            $ticketLog->TicketId = $request['id'];
            $ticketLog->Log = "Ticket sent to lineman";
            $ticketLog->LogDetails = "Ticket sent to lineman at " . $request['DateTimeDownloaded'];
            $ticketLog->UserId = Auth::id();
            $ticketLog->save();

            return response()->json(['response' => 'ok'], 200);
        }
    }

    public function updateDateArrival(Request $request) {
        if ($request->ajax()) {
            $ticket = Tickets::find($request['id']);
            $ticket->Status = "Crew Arrived on Site";
            $ticket->DateTimeLinemanArrived = date('Y-m-d H:i:s', strtotime($request['DateTimeLinemanArrived']));
            $ticket->save();

            // CREATE LOG
            $ticketLog = new TicketLogs;
            $ticketLog->id = IDGenerator::generateID();
            $ticketLog->TicketId = $request['id'];
            $ticketLog->Log = "Lineman site arrival";
            $ticketLog->LogDetails = "Lineman arrived on site at " . $request['DateTimeLinemanArrived'];
            $ticketLog->UserId = Auth::id();
            $ticketLog->save();

            return response()->json(['response' => 'ok'], 200);
        }
    }

    public function updateExecution(Request $request) {
        if ($request->ajax()) {
            $ticket = Tickets::find($request['id']);
            $ticket->Status = $request['Status'];
            $ticket->Notes = $request['Notes'];
            $ticket->DateTimeLinemanExecuted = date('Y-m-d H:i:s', strtotime($request['DateTimeLinemanExecuted']));
            $ticket->save();

            // CREATE LOG
            $ticketLog = new TicketLogs;
            $ticketLog->id = IDGenerator::generateID();
            $ticketLog->TicketId = $request['id'];
            $ticketLog->Log = $request['Status'];
            if($request['Status'] == 'Executed') {
                $ticketLog->LogDetails = "Lineman performed action at " . $request['DateTimeLinemanExecuted'];
            } else {
                $ticketLog->LogDetails = $request['Notes'];
            }            
            $ticketLog->UserId = Auth::id();
            $ticketLog->save();

            return response()->json(['response' => 'ok'], 200);
        }
    }
}
