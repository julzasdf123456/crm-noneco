<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Auth;
use App\Models\Tickets;
use App\Models\TicketLogs;
use App\Models\IDGenerator;
use App\Models\ServiceConnectionCrew;
use Illuminate\Support\Facades\DB;
use Validator;

class TicketsController extends Controller {

    public $successStatus = 200;

    public function getDownloadableTickets(Request $request) {
        $tickets = Tickets::where('CrewAssigned', $request['CrewAssigned'])
            ->where('Status', 'Received')
            ->where(function ($query) {
                $query->where('Trash', 'No')
                    ->orWhereNull('Trash');
            })
            ->get();

        if ($tickets) {
            return response()->json($tickets, $this->successStatus);
        } else {
            return response()->json(['response' => 'Error fetching tickets', 401]);
        }
    }

    public function updateDownloadedTicketsStatus(Request $request) {
        $tickets = Tickets::where('CrewAssigned', $request['CrewAssigned'])
            ->where('Status', 'Received')
            ->where(function ($query) {
                $query->where('Trash', 'No')
                    ->orWhereNull('Trash');
            })
            ->get();

        $crew = ServiceConnectionCrew::find($request['CrewAssigned']);

        $dateTimeDownloaded = date('Y-m-d H:i:s');

        foreach($tickets as $item) {
            // CREATE LOG
            $ticketLog = new TicketLogs;
            $ticketLog->id = IDGenerator::generateRandString();
            $ticketLog->TicketId = $item->id;
            $ticketLog->Log = "Ticket downloaded by lineman";
            $ticketLog->LogDetails = "Downloaded by " . ($crew != null ? $crew->StationName : "-") . " at " . $dateTimeDownloaded;
            $ticketLog->UserId = $request['UserId'];
            $ticketLog->save();
        }

        DB::table('CRM_Tickets')
            ->where('CrewAssigned', $request['CrewAssigned'])
            ->where('Status', 'Received')
            ->where(function ($query) {
                $query->where('Trash', 'No')
                    ->orWhereNull('Trash');
            })
            ->update(['Status' => 'Downloaded by Crew', 'DateTimeDownloaded' => $dateTimeDownloaded]);

        return response()->json(['response' => 'ok'], $this->successStatus);
    }

    public function uploadTickets(Request $request) {
        $tickets = Tickets::find($request['id']);

        if ($tickets != null) {
            $tickets->DateTimeLinemanArrived = $request['DateTimeLinemanArrived'];
            $tickets->DateTimeLinemanExecuted = $request['DateTimeLinemanExecuted'];
            $tickets->Status = $request['Status'];
            $tickets->Notes = $request['Notes'];
            $tickets->CurrentMeterReading = $request['CurrentMeterReading'];
            $tickets->NewMeterBrand = $request['NewMeterBrand'];
            $tickets->NewMeterNo = $request['NewMeterNo'];
            $tickets->NewMeterReading = $request['NewMeterReading'];
            $tickets->save();

            // CREATE LOG
            $ticketLog = new TicketLogs;
            $ticketLog->id = IDGenerator::generateIDandRandString();
            $ticketLog->TicketId = $request['id'];
            $ticketLog->Log = "Ticket uploadd by crew";
            $ticketLog->LogDetails = $tickets->Notes;
            $ticketLog->UserId = $request['UserId'];
            $ticketLog->save();
        }

        return response()->json($tickets, $this->successStatus);
    }
}