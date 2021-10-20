<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTicketsRequest;
use App\Http\Requests\UpdateTicketsRequest;
use App\Repositories\TicketsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Towns;
use App\Models\Barangays;
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

        return redirect(route('tickets.index'));
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
        $tickets = $this->ticketsRepository->find($id);

        if (empty($tickets)) {
            Flash::error('Tickets not found');

            return redirect(route('tickets.index'));
        }

        return view('tickets.show')->with('tickets', $tickets);
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

        if (empty($tickets)) {
            Flash::error('Tickets not found');

            return redirect(route('tickets.index'));
        }

        return view('tickets.edit')->with('tickets', $tickets);
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

        $this->ticketsRepository->delete($id);

        Flash::success('Tickets deleted successfully.');

        return redirect(route('tickets.index'));
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

        return view('tickets.create',   [
            'serviceAccount' => $serviceAccount,
            'towns' => $towns,
        ]);
    }
}
