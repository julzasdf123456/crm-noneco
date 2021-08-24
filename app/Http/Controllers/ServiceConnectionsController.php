<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateServiceConnectionsRequest;
use App\Http\Requests\UpdateServiceConnectionsRequest;
use App\Repositories\ServiceConnectionsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use App\Models\MemberConsumers;
use App\Models\Towns;
use App\Models\ServiceConnectionAccountTypes;
use App\Models\ServiceConnections;
use App\Models\ServiceConnectionInspections;
use App\Models\ServiceConnectionMtrTrnsfrmr;
use App\Models\ServiceConnectionPayTransaction;
use App\Models\ServiceConnectionTotalPayments;
use App\Models\ServiceConnectionTimeframes;
use App\Models\IDGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Flash;
use Response;

class ServiceConnectionsController extends AppBaseController
{
    /** @var  ServiceConnectionsRepository */
    private $serviceConnectionsRepository;

    public function __construct(ServiceConnectionsRepository $serviceConnectionsRepo)
    {
        $this->middleware('auth');
        $this->serviceConnectionsRepository = $serviceConnectionsRepo;
    }

    /**
     * Display a listing of the ServiceConnections.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $serviceConnections = $this->serviceConnectionsRepository->all();

        return view('service_connections.index')
            ->with('serviceConnections', $serviceConnections);
    }

    /**
     * Show the form for creating a new ServiceConnections.
     *
     * @return Response
     */
    public function create()
    {
        return view('service_connections.create');
    }

    /**
     * Store a newly created ServiceConnections in storage.
     *
     * @param CreateServiceConnectionsRequest $request
     *
     * @return Response
     */
    public function store(CreateServiceConnectionsRequest $request)
    {
        $input = $request->all();

        $serviceConnections = $this->serviceConnectionsRepository->create($input);

        // CREATE Timeframes
        $timeFrame = new ServiceConnectionTimeframes;
        $timeFrame->id = IDGenerator::generateID();
        $timeFrame->ServiceConnectionId = $input['id'];
        $timeFrame->UserId = Auth::id();
        $timeFrame->Status = 'Received';
        $timeFrame->save();

        Flash::success('Service Connections saved successfully.');

        return redirect(route('serviceConnectionInspections.create-step-two', [$input['id']]));
    }

    /**
     * Display the specified ServiceConnections.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $serviceConnections = DB::table('CRM_ServiceConnections')
        ->join('CRM_Barangays', 'CRM_ServiceConnections.Barangay', '=', 'CRM_Barangays.id')
        ->join('CRM_Towns', 'CRM_ServiceConnections.Town', '=', 'CRM_Towns.id')
        ->join('CRM_ServiceConnectionAccountTypes', 'CRM_ServiceConnections.AccountType', '=', 'CRM_ServiceConnectionAccountTypes.id')
        ->select('CRM_ServiceConnections.id as id',
                        'CRM_ServiceConnections.AccountCount as AccountCount', 
                        'CRM_ServiceConnections.ServiceAccountName as ServiceAccountName',
                        'CRM_ServiceConnections.DateOfApplication as DateOfApplication', 
                        'CRM_ServiceConnections.ContactNumber as ContactNumber', 
                        'CRM_ServiceConnections.EmailAddress as EmailAddress',  
                        'CRM_ServiceConnections.AccountApplicationType as AccountApplicationType', 
                        'CRM_ServiceConnections.AccountOrganization as AccountOrganization', 
                        'CRM_ServiceConnections.AccountApplicationType as AccountApplicationType', 
                        'CRM_ServiceConnections.ConnectionApplicationType as ConnectionApplicationType',
                        'CRM_ServiceConnections.MemberConsumerId as MemberConsumerId',
                        'CRM_ServiceConnections.Status as Status',  
                        'CRM_ServiceConnections.Notes as Notes', 
                        'CRM_ServiceConnections.Sitio as Sitio', 
                        'CRM_Towns.Town as Town',
                        'CRM_Barangays.Barangay as Barangay',
                        'CRM_ServiceConnectionAccountTypes.AccountType as AccountType')
        ->where('CRM_ServiceConnections.id', $id)
        ->first(); 

        $serviceConnectionInspections = ServiceConnectionInspections::where('ServiceConnectionId', $id)->first();

        $serviceConnectionMeter = ServiceConnectionMtrTrnsfrmr::where('ServiceConnectionId', $id)->first();

        $serviceConnectionTransactions = ServiceConnectionPayTransaction::where('ServiceConnectionId', $id)->first();

        $materialPayments = DB::table('CRM_ServiceConnectionMaterialPayments')
                    ->join('CRM_ServiceConnectionMaterialPayables', 'CRM_ServiceConnectionMaterialPayments.Material', '=', 'CRM_ServiceConnectionMaterialPayables.id')
                    ->select('CRM_ServiceConnectionMaterialPayments.id',
                            'CRM_ServiceConnectionMaterialPayments.Quantity',
                            'CRM_ServiceConnectionMaterialPayments.Vat',
                            'CRM_ServiceConnectionMaterialPayments.Total',
                            'CRM_ServiceConnectionMaterialPayables.Material',
                            'CRM_ServiceConnectionMaterialPayables.Rate',)
                    ->where('CRM_ServiceConnectionMaterialPayments.ServiceConnectionId', $id)
                    ->get();

        $particularPayments = DB::table('CRM_ServiceConnectionParticularPaymentsTransactions')
                    ->join('CRM_ServiceConnectionPaymentParticulars', 'CRM_ServiceConnectionParticularPaymentsTransactions.Particular', '=', 'CRM_ServiceConnectionPaymentParticulars.id')
                    ->select('CRM_ServiceConnectionParticularPaymentsTransactions.id',
                            'CRM_ServiceConnectionParticularPaymentsTransactions.Amount',
                            'CRM_ServiceConnectionParticularPaymentsTransactions.Vat',
                            'CRM_ServiceConnectionParticularPaymentsTransactions.Total',
                            'CRM_ServiceConnectionPaymentParticulars.Particular')
                    ->where('CRM_ServiceConnectionParticularPaymentsTransactions.ServiceConnectionId', $id)
                    ->get();

        $totalTransactions = ServiceConnectionTotalPayments::where('ServiceConnectionId', $id)->first();

        $timeFrame = DB::table('CRM_ServiceConnectionTimeframes')
                ->join('users', 'CRM_ServiceConnectionTimeframes.UserId', '=', 'users.id')
                ->select('CRM_ServiceConnectionTimeframes.id',
                        'CRM_ServiceConnectionTimeframes.Status',
                        'CRM_ServiceConnectionTimeframes.created_at',
                        'CRM_ServiceConnectionTimeframes.ServiceConnectionId',
                        'CRM_ServiceConnectionTimeframes.UserId',
                        'users.name')
                ->where('CRM_ServiceConnectionTimeframes.ServiceConnectionId', $id)
                ->orderBy('created_at')
                ->get();

        if (empty($serviceConnections)) {
            Flash::error('Service Connections not found');

            return redirect(route('serviceConnections.index'));
        }

        return view('service_connections.show', ['serviceConnections' => $serviceConnections, 
                                                'serviceConnectionInspections' => $serviceConnectionInspections, 
                                                'serviceConnectionMeter' => $serviceConnectionMeter, 
                                                'serviceConnectionTransactions' => $serviceConnectionTransactions,
                                                'materialPayments' => $materialPayments,
                                                'particularPayments' => $particularPayments,
                                                'totalTransactions' => $totalTransactions,
                                                'timeFrame' => $timeFrame]);
    }

    /**
     * Show the form for editing the specified ServiceConnections.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $serviceConnections = $this->serviceConnectionsRepository->find($id);

        $cond = 'edit';

        $towns = Towns::orderBy('Town')->pluck('Town', 'id');

        $memberConsumer = null;

        $accountTypes = ServiceConnectionAccountTypes::orderBy('id')->pluck('AccountType', 'id');

        if (empty($serviceConnections)) {
            Flash::error('Service Connections not found');

            return redirect(route('serviceConnections.index'));
        }

        return view('service_connections.edit', ['serviceConnections' => $serviceConnections, 'cond' => $cond, 'towns' => $towns, 'memberConsumer' => $memberConsumer, 'accountTypes' => $accountTypes]);
    }

    /**
     * Update the specified ServiceConnections in storage.
     *
     * @param int $id
     * @param UpdateServiceConnectionsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateServiceConnectionsRequest $request)
    {
        $serviceConnections = $this->serviceConnectionsRepository->find($id);

        if (empty($serviceConnections)) {
            Flash::error('Service Connections not found');

            return redirect(route('serviceConnections.index'));
        }

        $serviceConnections = $this->serviceConnectionsRepository->update($request->all(), $id);

        Flash::success('Service Connections updated successfully.');

        // return redirect(route('serviceConnections.index'));
        return redirect()->action([ServiceConnectionsController::class, 'show'], [$id]);
    }

    /**
     * Remove the specified ServiceConnections from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $serviceConnections = $this->serviceConnectionsRepository->find($id);

        if (empty($serviceConnections)) {
            Flash::error('Service Connections not found');

            return redirect(route('serviceConnections.index'));
        }

        $this->serviceConnectionsRepository->delete($id);

        Flash::success('Service Connections deleted successfully.');

        return redirect(route('serviceConnections.index'));
    }

    public function selectMembership() {
        return view('/service_connections/selectmembership');
    }

    public function fetchmemberconsumer(Request $request) {
        if ($request->ajax()) {
            $query = $request->get('query');
            
            if ($query != '' ) {
                $data = DB::table('CRM_MemberConsumers')
                    ->join('CRM_MemberConsumerTypes', 'CRM_MemberConsumers.MembershipType', '=', 'CRM_MemberConsumerTypes.Id')
                    ->join('CRM_Barangays', 'CRM_MemberConsumers.Barangay', '=', 'CRM_Barangays.id')
                    ->join('CRM_Towns', 'CRM_MemberConsumers.Town', '=', 'CRM_Towns.id')
                    ->select('CRM_MemberConsumers.Id as ConsumerId',
                                    'CRM_MemberConsumers.MembershipType as MembershipType', 
                                    'CRM_MemberConsumers.FirstName as FirstName', 
                                    'CRM_MemberConsumers.MiddleName as MiddleName', 
                                    'CRM_MemberConsumers.LastName as LastName', 
                                    'CRM_MemberConsumers.OrganizationName as OrganizationName', 
                                    'CRM_MemberConsumers.Suffix as Suffix', 
                                    'CRM_MemberConsumers.Birthdate as Birthdate', 
                                    'CRM_MemberConsumers.Barangay as Barangay', 
                                    'CRM_MemberConsumers.ApplicationStatus as ApplicationStatus',
                                    'CRM_MemberConsumers.DateApplied as DateApplied', 
                                    'CRM_MemberConsumers.CivilStatus as CivilStatus', 
                                    'CRM_MemberConsumers.DateApproved as DateApproved', 
                                    'CRM_MemberConsumers.ContactNumbers as ContactNumbers', 
                                    'CRM_MemberConsumers.EmailAddress as EmailAddress',  
                                    'CRM_MemberConsumers.Notes as Notes', 
                                    'CRM_MemberConsumers.Gender as Gender', 
                                    'CRM_MemberConsumers.Sitio as Sitio', 
                                    'CRM_MemberConsumerTypes.*',
                                    'CRM_Towns.Town as Town',
                                    'CRM_Barangays.Barangay as Barangay')
                    ->where('CRM_MemberConsumers.LastName', 'LIKE', '%' . $query . '%')
                    ->orWhere('CRM_MemberConsumers.Id', 'LIKE', '%' . $query . '%')
                    ->orWhere('CRM_MemberConsumers.MiddleName', 'LIKE', '%' . $query . '%')
                    ->orWhere('CRM_MemberConsumers.FirstName', 'LIKE', '%' . $query . '%')
                    ->orderBy('CRM_MemberConsumers.FirstName')
                    ->get();
            } else {
                $data = DB::table('CRM_MemberConsumers')
                    ->join('CRM_MemberConsumerTypes', 'CRM_MemberConsumers.MembershipType', '=', 'CRM_MemberConsumerTypes.Id')
                    ->join('CRM_Barangays', 'CRM_MemberConsumers.Barangay', '=', 'CRM_Barangays.id')
                    ->join('CRM_Towns', 'CRM_MemberConsumers.Town', '=', 'CRM_Towns.id')
                    ->select('CRM_MemberConsumers.Id as ConsumerId',
                                    'CRM_MemberConsumers.MembershipType as MembershipType', 
                                    'CRM_MemberConsumers.FirstName as FirstName', 
                                    'CRM_MemberConsumers.MiddleName as MiddleName', 
                                    'CRM_MemberConsumers.LastName as LastName', 
                                    'CRM_MemberConsumers.OrganizationName as OrganizationName', 
                                    'CRM_MemberConsumers.Suffix as Suffix', 
                                    'CRM_MemberConsumers.Birthdate as Birthdate', 
                                    'CRM_MemberConsumers.Barangay as Barangay', 
                                    'CRM_MemberConsumers.ApplicationStatus as ApplicationStatus',
                                    'CRM_MemberConsumers.DateApplied as DateApplied', 
                                    'CRM_MemberConsumers.CivilStatus as CivilStatus', 
                                    'CRM_MemberConsumers.DateApproved as DateApproved', 
                                    'CRM_MemberConsumers.ContactNumbers as ContactNumbers', 
                                    'CRM_MemberConsumers.EmailAddress as EmailAddress',  
                                    'CRM_MemberConsumers.Notes as Notes', 
                                    'CRM_MemberConsumers.Gender as Gender', 
                                    'CRM_MemberConsumers.Sitio as Sitio', 
                                    'CRM_MemberConsumerTypes.*',
                                    'CRM_Towns.Town as Town',
                                    'CRM_Barangays.Barangay as Barangay')
                    ->orderByDesc('CRM_MemberConsumers.created_at')
                    ->take(10)
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
                                                <h4>' . MemberConsumers::serializeMemberName($row) . '</h4>
                                                <p class="text-muted" style="margin-bottom: 0;">ID: ' . $row->ConsumerId . '</p>
                                                <p class="text-muted" style="margin-bottom: 0;">' . $row->Barangay . ', ' . $row->Town  . '</p>
                                                <a href="' . route('serviceConnections.create_new', [$row->ConsumerId]) . '" class="btn btn-sm btn-primary" style="margin-top: 5px;">Proceed</a>
                                            </div>     
                                        </div> 

                                        <div class="col-md-6 col-lg-6 d-sm-none d-md-block d-none d-sm-block" style="border-left: 2px solid #007bff; padding-left: 15px;">
                                            <div>
                                                <p class="text-muted" style="margin-bottom: 0;">Birthdate: <strong>' . date('F d, Y', strtotime($row->Birthdate)) . '</strong></p>
                                                <p class="text-muted" style="margin-bottom: 0;">Contact No: <strong>' . $row->ContactNumbers . '</strong></p>
                                                <p class="text-muted" style="margin-bottom: 0;">Email Add: <strong>' . $row->EmailAddress . '</strong></p>
                                                <p class="text-muted" style="margin-bottom: 0;">Membership Type: <strong>' . $row->Type . '</strong></p>
                                            </div>     
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>   
                    ';
                }                
            } else {
                $output = '
                    <p class="text-center">No data found.</p>';
            }

            $data = [
                'table_data' => $output
            ];

            echo json_encode($data);
        }
    }

    public function createNew($consumerId) {
        $towns = Towns::orderBy('Town')->pluck('Town', 'id');

        $memberConsumer = DB::table('CRM_MemberConsumers')
                            ->join('CRM_MemberConsumerTypes', 'CRM_MemberConsumers.MembershipType', '=', 'CRM_MemberConsumerTypes.Id')
                            ->join('CRM_Barangays', 'CRM_MemberConsumers.Barangay', '=', 'CRM_Barangays.id')
                            ->join('CRM_Towns', 'CRM_MemberConsumers.Town', '=', 'CRM_Towns.id')
                            ->select('CRM_MemberConsumers.Id as ConsumerId',
                                    'CRM_MemberConsumers.MembershipType as MembershipType', 
                                    'CRM_MemberConsumers.FirstName as FirstName', 
                                    'CRM_MemberConsumers.MiddleName as MiddleName', 
                                    'CRM_MemberConsumers.LastName as LastName', 
                                    'CRM_MemberConsumers.OrganizationName as OrganizationName', 
                                    'CRM_MemberConsumers.Suffix as Suffix', 
                                    'CRM_MemberConsumers.Birthdate as Birthdate', 
                                    'CRM_MemberConsumers.Barangay as Barangay', 
                                    'CRM_MemberConsumers.ApplicationStatus as ApplicationStatus',
                                    'CRM_MemberConsumers.DateApplied as DateApplied', 
                                    'CRM_MemberConsumers.CivilStatus as CivilStatus', 
                                    'CRM_MemberConsumers.DateApproved as DateApproved', 
                                    'CRM_MemberConsumers.ContactNumbers as ContactNumbers', 
                                    'CRM_MemberConsumers.EmailAddress as EmailAddress',  
                                    'CRM_MemberConsumers.Notes as Notes', 
                                    'CRM_MemberConsumers.Gender as Gender', 
                                    'CRM_MemberConsumers.Sitio as Sitio',
                                    'CRM_Towns.Town as Town',
                                    'CRM_Towns.id as TownId',
                                    'CRM_Barangays.Barangay as Barangay',
                                    'CRM_Barangays.id as BarangayId')
                            ->where('CRM_MemberConsumers.Id', $consumerId)
                            ->first();

        $cond = 'new';

        $accountTypes = ServiceConnectionAccountTypes::orderBy('id')->pluck('AccountType', 'id');

        return view('/service_connections/create_new', ['memberConsumer' => $memberConsumer, 'cond' => $cond, 'towns' => $towns, 'accountTypes' => $accountTypes]);
    }

    public function fetchserviceconnections(Request $request) {
        if ($request->ajax()) {
            $query = $request->get('query');
            
            if ($query != '' ) {
                $data = DB::table('CRM_ServiceConnections')
                    ->join('CRM_Barangays', 'CRM_ServiceConnections.Barangay', '=', 'CRM_Barangays.id')                    
                    ->join('CRM_Towns', 'CRM_ServiceConnections.Town', '=', 'CRM_Towns.id')
                    ->join('CRM_ServiceConnectionAccountTypes', 'CRM_ServiceConnections.AccountType', '=', 'CRM_ServiceConnectionAccountTypes.id')
                    ->select('CRM_ServiceConnections.id as ConsumerId',
                                    'CRM_ServiceConnections.ServiceAccountName as ServiceAccountName',
                                    'CRM_ServiceConnections.Status as Status',
                                    'CRM_ServiceConnections.DateOfApplication as DateOfApplication', 
                                    'CRM_ServiceConnections.ContactNumber as ContactNumber', 
                                    'CRM_ServiceConnections.EmailAddress as EmailAddress',  
                                    'CRM_ServiceConnections.AccountCount as AccountCount',  
                                    'CRM_ServiceConnections.Sitio as Sitio', 
                                    'CRM_Towns.Town as Town',
                                    'CRM_ServiceConnectionAccountTypes.AccountType as AccountType',
                                    'CRM_Barangays.Barangay as Barangay')
                    ->where('CRM_ServiceConnections.ServiceAccountName', 'LIKE', '%' . $query . '%')
                    ->orWhere('CRM_ServiceConnections.Id', 'LIKE', '%' . $query . '%')
                    ->orderBy('CRM_ServiceConnections.ServiceAccountName')
                    ->get();
            } else {
                $data = DB::table('CRM_ServiceConnections')
                    ->join('CRM_Barangays', 'CRM_ServiceConnections.Barangay', '=', 'CRM_Barangays.id')                    
                    ->join('CRM_Towns', 'CRM_ServiceConnections.Town', '=', 'CRM_Towns.id')
                    ->join('CRM_ServiceConnectionAccountTypes', 'CRM_ServiceConnections.AccountType', '=', 'CRM_ServiceConnectionAccountTypes.id')
                    ->select('CRM_ServiceConnections.id as ConsumerId',
                                    'CRM_ServiceConnections.ServiceAccountName as ServiceAccountName',
                                    'CRM_ServiceConnections.Status as Status',
                                    'CRM_ServiceConnections.DateOfApplication as DateOfApplication', 
                                    'CRM_ServiceConnections.ContactNumber as ContactNumber', 
                                    'CRM_ServiceConnections.EmailAddress as EmailAddress',  
                                    'CRM_ServiceConnections.AccountCount as AccountCount',  
                                    'CRM_ServiceConnections.Sitio as Sitio', 
                                    'CRM_Towns.Town as Town',
                                    'CRM_ServiceConnectionAccountTypes.AccountType as AccountType',
                                    'CRM_Barangays.Barangay as Barangay')
                    ->orderByDesc('CRM_ServiceConnections.created_at')
                    ->take(10)
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
                                                <h4>' .$row->ServiceAccountName . '</h4>
                                                <p class="text-muted" style="margin-bottom: 0;">Acount Number: ' . $row->ConsumerId . '</p>
                                                <p class="text-muted" style="margin-bottom: 0;">' . $row->Barangay . ', ' . $row->Town  . '</p>
                                                <a href="' . route('serviceConnections.show', [$row->ConsumerId]) . '" class="text-primary" style="margin-top: 5px; padding: 8px;" title="View"><i class="fas fa-eye"></i></a>
                                                <a href="' . route('serviceConnections.edit', [$row->ConsumerId]) . '" class="text-warning" style="margin-top: 5px; padding: 8px;" title="Edit"><i class="fas fa-pen"></i></a>
                                            </div>     
                                        </div> 

                                        <div class="col-md-6 col-lg-6 d-sm-none d-md-block d-none d-sm-block" style="border-left: 2px solid #007bff; padding-left: 15px;">
                                            <div>
                                                <p class="text-muted" style="margin-bottom: 0;">Date of Application: <strong>' . date('F d, Y', strtotime($row->DateOfApplication)) . '</strong></p>
                                                <p class="text-muted" style="margin-bottom: 0;">AccountCount: <strong>' . $row->AccountCount . '</strong></p>
                                                <p class="text-muted" style="margin-bottom: 0;">Status: <strong>' . $row->Status . '</strong></p>
                                                <p class="text-muted" style="margin-bottom: 0;">Account Type: <strong>' . $row->AccountType . '</strong></p>
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
}
