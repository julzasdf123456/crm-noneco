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
use App\Models\ServiceConnectionChecklistsRep;
use App\Models\ServiceConnectionChecklists;
use App\Models\ServiceConnectionCrew;
use App\Models\ServiceConnectionLgLoadInsp;
use App\Models\StructureAssignments;
use App\Models\Structures;
use App\Models\MaterialAssets;
use App\Models\BillsOfMaterialsSummary;
use App\Models\SpanningData;
use App\Models\PoleIndex;
use App\Models\PreDefinedMaterials;
use App\Models\PreDefinedMaterialsMatrix;
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

    public function dashboard() {
        return view('/service_connections/dashboard');
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

        // return redirect(route('serviceConnectionInspections.create-step-two', [$input['id']]));
        return redirect(route('serviceConnections.assess-checklists', [$input['id']]));
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
            ->leftJoin('CRM_ServiceConnectionAccountTypes', 'CRM_ServiceConnections.AccountType', '=', 'CRM_ServiceConnectionAccountTypes.id')
            ->join('CRM_ServiceConnectionCrew', 'CRM_ServiceConnections.StationCrewAssigned', '=', 'CRM_ServiceConnectionCrew.id')
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
                        'CRM_ServiceConnections.ORNumber as ORNumber', 
                        'CRM_ServiceConnections.Sitio as Sitio', 
                        'CRM_ServiceConnections.LoadCategory as LoadCategory', 
                        'CRM_ServiceConnections.DateTimeOfEnergization as DateTimeOfEnergization', 
                        'CRM_ServiceConnections.DateTimeLinemenArrived as DateTimeLinemenArrived', 
                        'CRM_Towns.Town as Town',
                        'CRM_Barangays.Barangay as Barangay',
                        'CRM_ServiceConnectionAccountTypes.AccountType as AccountType',
                        'CRM_ServiceConnectionCrew.StationName as StationName',
                        'CRM_ServiceConnectionCrew.CrewLeader as CrewLeader',
                        'CRM_ServiceConnectionCrew.Members as Members')
        ->where('CRM_ServiceConnections.id', $id)
        ->where(function ($query) {
            $query->where('CRM_ServiceConnections.Trash', 'No')
                ->orWhereNull('CRM_ServiceConnections.Trash');
        })
        ->first(); 

        $serviceConnectionInspections = ServiceConnectionInspections::where('ServiceConnectionId', $id)
                                ->orderByDesc('created_at')
                                ->first();

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
                        'CRM_ServiceConnectionTimeframes.Notes',
                        'users.name')
                ->where('CRM_ServiceConnectionTimeframes.ServiceConnectionId', $id)
                ->orderByDesc('created_at')
                ->get();

        $billOfMaterialsSummary = BillsOfMaterialsSummary::where('ServiceConnectionId', $id)->first();

        $structures = DB::table('CRM_StructureAssignments')
            ->leftJoin('CRM_Structures', 'CRM_StructureAssignments.StructureId', '=', 'CRM_Structures.Data')
            ->select('CRM_Structures.id as id',
                    'CRM_StructureAssignments.StructureId',
                    DB::raw('SUM(CAST(CRM_StructureAssignments.Quantity AS Integer)) AS Quantity'))
            ->where('ServiceConnectionId', $id)
            ->groupBy('CRM_Structures.id', 'CRM_StructureAssignments.StructureId')
            ->get();

        $conAss = DB::table('CRM_StructureAssignments')
            ->where('ServiceConnectionId', $id)
            ->select('ConAssGrouping', 'StructureId', 'Quantity', 'Type')
            ->groupBy('StructureId', 'ConAssGrouping', 'Quantity', 'Type')
            ->orderBy('ConAssGrouping')
            ->get();

        $materials = DB::table('CRM_BillOfMaterialsMatrix')
            ->leftJoin('CRM_MaterialAssets', 'CRM_BillOfMaterialsMatrix.MaterialsId', '=', 'CRM_MaterialAssets.id')   
            ->select('CRM_MaterialAssets.id',
                    'CRM_MaterialAssets.Description',
                    'CRM_BillOfMaterialsMatrix.Amount',
                    DB::raw('SUM(CAST(CRM_BillOfMaterialsMatrix.Quantity AS Integer)) AS ProjectRequirements'),
                    DB::raw('(CAST(CRM_BillOfMaterialsMatrix.Amount As Money) * SUM(CAST(CRM_BillOfMaterialsMatrix.Quantity AS Integer))) AS Cost'))
            ->where('CRM_BillOfMaterialsMatrix.ServiceConnectionId', $id)
            // ->where('CRM_BillOfMaterialsMatrix.StructureType', 'A_DT')
            ->groupBy('CRM_MaterialAssets.Description', 'CRM_BillOfMaterialsMatrix.Amount', 'CRM_MaterialAssets.id')
            ->orderBy('CRM_MaterialAssets.Description')
            ->get();
        
        $poles = DB::table('CRM_BillOfMaterialsMatrix')
                ->leftJoin('CRM_MaterialAssets', 'CRM_BillOfMaterialsMatrix.MaterialsId', '=', 'CRM_MaterialAssets.id')   
                ->select('CRM_MaterialAssets.id',
                        'CRM_MaterialAssets.Description',
                        DB::raw('SUM(CAST(CRM_BillOfMaterialsMatrix.Quantity AS Integer)) AS ProjectRequirements'),)
                ->where('CRM_BillOfMaterialsMatrix.ServiceConnectionId', $id)
                ->where('CRM_BillOfMaterialsMatrix.StructureType', 'POLE')
                ->groupBy('CRM_MaterialAssets.Description', 'CRM_MaterialAssets.id')
                ->orderBy('CRM_MaterialAssets.Description')
                ->get();

        $transformers = DB::table('CRM_TransformersAssignedMatrix')
                ->leftJoin('CRM_MaterialAssets', 'CRM_TransformersAssignedMatrix.MaterialsId', '=', 'CRM_MaterialAssets.id')
                ->select('CRM_MaterialAssets.id',
                        'CRM_TransformersAssignedMatrix.id as TransformerId',
                        'CRM_MaterialAssets.Description',
                        'CRM_MaterialAssets.Amount',
                        'CRM_TransformersAssignedMatrix.Quantity',
                        'CRM_TransformersAssignedMatrix.Type')
                ->where('CRM_TransformersAssignedMatrix.ServiceConnectionId', $id)
                ->get();

        if (empty($serviceConnections)) {
            Flash::error('Service Connections not found');

            return redirect(route('serviceConnections.index'));
        }

        $serviceConnectionChecklistsRep = ServiceConnectionChecklistsRep::all();
        
        $serviceConnectionChecklists = ServiceConnectionChecklists::where('ServiceConnectionId', $id)->pluck('ChecklistId')->all();

        return view('service_connections.show', ['serviceConnections' => $serviceConnections, 
                                                'serviceConnectionInspections' => $serviceConnectionInspections, 
                                                'serviceConnectionMeter' => $serviceConnectionMeter, 
                                                'serviceConnectionTransactions' => $serviceConnectionTransactions,
                                                'materialPayments' => $materialPayments,
                                                'particularPayments' => $particularPayments,
                                                'totalTransactions' => $totalTransactions,
                                                'timeFrame' => $timeFrame,
                                                'serviceConnectionChecklistsRep' => $serviceConnectionChecklistsRep,
                                                'serviceConnectionChecklists' => $serviceConnectionChecklists,
                                                'billOfMaterialsSummary' => $billOfMaterialsSummary,
                                                'structures' => $structures,
                                                'conAss' => $conAss,
                                                'materials' => $materials,
                                                'poles' => $poles,
                                                'transformers' => $transformers]);
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

        $accountTypes = ServiceConnectionAccountTypes::orderBy('id')->get();

        $crew = ServiceConnectionCrew::orderBy('StationName')->pluck('StationName', 'id');

        if (empty($serviceConnections)) {
            Flash::error('Service Connections not found');

            return redirect(route('serviceConnections.index'));
        }

        return view('service_connections.edit', ['serviceConnections' => $serviceConnections, 'cond' => $cond, 'towns' => $towns, 'memberConsumer' => $memberConsumer, 'accountTypes' => $accountTypes, 'crew' => $crew]);
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

    public function selectApplicationType($consumerId) {
        return view('/service_connections/select_application_type', ['consumerId' => $consumerId]);
    }

    public function relayApplicationType($consumerId, Request $request) {
        return redirect(route('serviceConnections.create_new', [$consumerId, $request['type']]));
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

        $accountTypes = ServiceConnectionAccountTypes::orderBy('id')->get();

        $crew = ServiceConnectionCrew::orderBy('StationName')->pluck('StationName', 'id');

        return view('/service_connections/create_new', ['memberConsumer' => $memberConsumer, 'cond' => $cond, 'towns' => $towns, 'accountTypes' => $accountTypes, 'crew' => $crew]);
    }

    public function fetchserviceconnections(Request $request) {
        if ($request->ajax()) {
            $query = $request->get('query');
            
            if ($query != '' ) {
                $data = DB::table('CRM_ServiceConnections')
                    ->join('CRM_Barangays', 'CRM_ServiceConnections.Barangay', '=', 'CRM_Barangays.id')                    
                    ->join('CRM_Towns', 'CRM_ServiceConnections.Town', '=', 'CRM_Towns.id')
                    ->select('CRM_ServiceConnections.id as ConsumerId',
                                    'CRM_ServiceConnections.ServiceAccountName as ServiceAccountName',
                                    'CRM_ServiceConnections.Status as Status',
                                    'CRM_ServiceConnections.DateOfApplication as DateOfApplication', 
                                    'CRM_ServiceConnections.ContactNumber as ContactNumber', 
                                    'CRM_ServiceConnections.EmailAddress as EmailAddress',  
                                    'CRM_ServiceConnections.AccountCount as AccountCount',  
                                    'CRM_ServiceConnections.Sitio as Sitio', 
                                    'CRM_Towns.Town as Town',
                                    'CRM_Barangays.Barangay as Barangay')
                    ->where(function ($query) {
                                        $query->where('CRM_ServiceConnections.Trash', 'No')
                                            ->orWhereNull('CRM_ServiceConnections.Trash');
                                    })
                    ->where('CRM_ServiceConnections.ServiceAccountName', 'LIKE', '%' . $query . '%')
                    ->orWhere('CRM_ServiceConnections.Id', 'LIKE', '%' . $query . '%')
                    
                    ->orderBy('CRM_ServiceConnections.ServiceAccountName')
                    ->get();
            } else {
                $data = DB::table('CRM_ServiceConnections')
                    ->join('CRM_Barangays', 'CRM_ServiceConnections.Barangay', '=', 'CRM_Barangays.id')                    
                    ->join('CRM_Towns', 'CRM_ServiceConnections.Town', '=', 'CRM_Towns.id')
                    ->select('CRM_ServiceConnections.id as ConsumerId',
                                    'CRM_ServiceConnections.ServiceAccountName as ServiceAccountName',
                                    'CRM_ServiceConnections.Status as Status',
                                    'CRM_ServiceConnections.DateOfApplication as DateOfApplication', 
                                    'CRM_ServiceConnections.ContactNumber as ContactNumber', 
                                    'CRM_ServiceConnections.EmailAddress as EmailAddress',  
                                    'CRM_ServiceConnections.AccountCount as AccountCount',  
                                    'CRM_ServiceConnections.Sitio as Sitio', 
                                    'CRM_Towns.Town as Town',
                                    'CRM_Barangays.Barangay as Barangay')
                    ->where(function ($query) {
                                        $query->where('CRM_ServiceConnections.Trash', 'No')
                                            ->orWhereNull('CRM_ServiceConnections.Trash');
                                    })
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

    public function assessChecklists($id) {
        $serviceConnections = $this->serviceConnectionsRepository->find($id);

        $checklist = ServiceConnectionChecklistsRep::all();

        return view('/service_connections/assess_checklists', ['serviceConnections' => $serviceConnections, 'checklist' => $checklist]);
    }

    public function updateChecklists($id) {
        $serviceConnections = $this->serviceConnectionsRepository->find($id);

        $checklist = ServiceConnectionChecklistsRep::all();

        $checklistCompleted = ServiceConnectionChecklists::where('ServiceConnectionId', $id)->pluck('ChecklistId')->all();

        return view('/service_connections/update_checklists', ['serviceConnections' => $serviceConnections, 'checklist' => $checklist, 'checklistCompleted' => $checklistCompleted]);
    }

    public function moveToTrash($id) {
        $serviceConnections = $this->serviceConnectionsRepository->find($id);

        $serviceConnections->Trash = 'Yes';

        $serviceConnections->save();

        return redirect(route('serviceConnections.index'));
    }

    public function trash() {
        return view('/service_connections/trash');
    }

    public function fetchserviceconnectiontrash(Request $request) {
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
                    ->where('CRM_ServiceConnections.Trash', 'Yes')
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
                    ->where('CRM_ServiceConnections.Trash', 'Yes')
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
                                                <a href="' . route('serviceConnections.restore', [$row->ConsumerId]) . '" class="text-primary" style="margin-top: 5px; padding: 8px;" title="Restore"><lord-icon
                                                        src="https://cdn.lordicon.com/ybgqhhgb.json"
                                                        trigger="loop"
                                                        delay="1500"
                                                        colors="primary:#e83a30,secondary:#e83a30"
                                                        stroke="100"
                                                        style="width:25px;height:25px">
                                                    </lord-icon></a>
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

    public function restore($id) {
        $serviceConnections = $this->serviceConnectionsRepository->find($id);

        $serviceConnections->Trash = null;

        $serviceConnections->save();

        return redirect(route('serviceConnections.trash'));
    }

    public function energization() {
        if (Auth::user()->hasAnyRole(['Administrator', 'Heads and Managers', 'Energization Clerk'])) {
            $serviceConnections = DB::table('CRM_ServiceConnections')
                        ->leftJoin('CRM_Barangays', 'CRM_ServiceConnections.Barangay', '=', 'CRM_Barangays.id')                    
                        ->leftJoin('CRM_Towns', 'CRM_ServiceConnections.Town', '=', 'CRM_Towns.id')
                        ->leftJoin('CRM_ServiceConnectionAccountTypes', 'CRM_ServiceConnections.AccountType', '=', 'CRM_ServiceConnectionAccountTypes.id')                        
                        ->leftJoin('CRM_ServiceConnectionCrew', 'CRM_ServiceConnections.StationCrewAssigned', '=', 'CRM_ServiceConnectionCrew.id')
                        ->select('CRM_ServiceConnections.id as id',
                                        'CRM_ServiceConnections.ServiceAccountName as ServiceAccountName',
                                        'CRM_ServiceConnections.Status as Status',
                                        'CRM_ServiceConnections.DateOfApplication as DateOfApplication', 
                                        'CRM_ServiceConnections.ContactNumber as ContactNumber', 
                                        'CRM_ServiceConnections.EmailAddress as EmailAddress',  
                                        'CRM_ServiceConnections.AccountCount as AccountCount',  
                                        'CRM_ServiceConnections.Sitio as Sitio', 
                                        'CRM_Towns.Town as Town',
                                        'CRM_ServiceConnectionAccountTypes.AccountType as AccountType',
                                        'CRM_ServiceConnections.EnergizationOrderIssued as EnergizationOrderIssued', 
                                        'CRM_ServiceConnections.StationCrewAssigned as StationCrewAssigned',
                                        'CRM_ServiceConnectionCrew.StationName as StationName',
                                        'CRM_ServiceConnectionCrew.CrewLeader as CrewLeader',
                                        'CRM_ServiceConnectionCrew.Members as Members',
                                        'CRM_Barangays.Barangay as Barangay')
                        ->whereNotNull('CRM_ServiceConnections.ORNumber')
                        ->where(function ($query) {
                            $query->where('CRM_ServiceConnections.Status', 'Approved')
                                ->orWhere('CRM_ServiceConnections.Status', 'Not Energized');
                        })
                        ->whereIn('CRM_ServiceConnections.id', DB::table('CRM_ServiceConnectionMeterAndTransformer')->pluck('ServiceConnectionId'))
                        ->orderBy('CRM_ServiceConnections.ServiceAccountName')
                        ->get();

            $crew = ServiceConnectionCrew::orderBy('StationName')->get();

            return view('/service_connections/energization', ['serviceConnections' => $serviceConnections, 'crew' => $crew]);
        } else {
            abort(403, 'Access denied');
        }      
    }

    public function changeStationCrew(Request $request) {
        if(request()->ajax()){
            $serviceConnection = ServiceConnections::find($request['id']);

            $serviceConnection->StationCrewAssigned = $request['StationCrewAssigned'];
            $serviceConnection->DateTimeOfEnergizationIssue = date('Y-m-d H:i:s');

            $serviceConnection->save();

            // CREATE Timeframes
            $timeFrame = new ServiceConnectionTimeframes;
            $timeFrame->id = IDGenerator::generateID();
            $timeFrame->ServiceConnectionId = $request['id'];
            $timeFrame->UserId = Auth::id();
            $timeFrame->Status = 'Station Crew Re-assigned';
            $timeFrame->Notes = 'From ' . $request['FromStationCrewName'] . ' to ' . $request['ToStationCrewName'];
            $timeFrame->save();

            return response()->json([ 'success' => true ]);
        }        
    }

    public function updateEnergizationStatus(Request $request) {
        if (request()->ajax()) {
            $serviceConnection = ServiceConnections::find($request['id']);

            $serviceConnection->Status = $request['Status'];
            $serviceConnection->DateTimeOfEnergization = $request['EnergizationDate'];
            $serviceConnection->DateTimeLinemenArrived = $request['ArrivalDate'];

            $serviceConnection->save();

            // CREATE Timeframes
            $timeFrame = new ServiceConnectionTimeframes;
            $timeFrame->id = IDGenerator::generateID();
            $timeFrame->ServiceConnectionId = $request['id'];
            $timeFrame->UserId = Auth::id();
            $timeFrame->Status = $request['Status'];
            $timeFrame->Notes = 'Crew arrived at ' . date('F d, Y h:i:s A', strtotime($request['ArrivalDate'])) . '<br>' . 'Performed energization attempt at ' . date('F d, Y h:i:s A', strtotime($request['EnergizationDate'])) . '<br>' . $request['Reason'];
            $timeFrame->save();

            return response()->json([ 'success' => true ]);
        }
    }

    public function printOrder($id) {
        $serviceConnections = DB::table('CRM_ServiceConnections')
            ->leftJoin('CRM_Barangays', 'CRM_ServiceConnections.Barangay', '=', 'CRM_Barangays.id')
            ->leftJoin('CRM_Towns', 'CRM_ServiceConnections.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_ServiceConnectionAccountTypes', 'CRM_ServiceConnections.AccountType', '=', 'CRM_ServiceConnectionAccountTypes.id')
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
                        'CRM_ServiceConnections.BuildingType',
                        'CRM_ServiceConnections.Notes as Notes', 
                        'CRM_ServiceConnections.ORNumber as ORNumber', 
                        'CRM_ServiceConnections.ORDate as ORDate', 
                        'CRM_ServiceConnections.Sitio as Sitio', 
                        'CRM_Towns.Town as Town',
                        'CRM_Barangays.Barangay as Barangay',
                        'CRM_ServiceConnectionAccountTypes.AccountType as AccountType')
        ->where('CRM_ServiceConnections.id', $id)
        ->where(function ($query) {
            $query->where('CRM_ServiceConnections.Trash', 'No')
                ->orWhereNull('CRM_ServiceConnections.Trash');
        })
        ->first(); 

        $serviceConnectionInspections = ServiceConnectionInspections::where('ServiceConnectionId', $id)
                                ->orderByDesc('created_at')
                                ->first();

        $serviceConnectionMeter = ServiceConnectionMtrTrnsfrmr::where('ServiceConnectionId', $id)->first();

        // CREATE Timeframes
        $timeFrame = new ServiceConnectionTimeframes;
        $timeFrame->id = IDGenerator::generateID();
        $timeFrame->ServiceConnectionId = $id;
        $timeFrame->UserId = Auth::id();
        $timeFrame->Status = 'Energization Order Issued';
        $timeFrame->save();

        // UPDATE ENERGIZATION COLUMN IN SEVICE CONNECTIONS;
        $scUpdate = ServiceConnections::find($id);
        $scUpdate->EnergizationOrderIssued = 'Yes';
        $scUpdate->DateTimeOfEnergizationIssue = date('Y-m-d H:i:s');
        $scUpdate->save();

        return view('/service_connections/print_order', ['serviceConnection' => $serviceConnections, 'serviceConnectionInspections' => $serviceConnectionInspections, 'serviceConnectionMeter' => $serviceConnectionMeter]);
    }

    public function largeLoadInspections() {
        $serviceConnections = DB::table('CRM_ServiceConnections')
                    ->leftJoin('CRM_Barangays', 'CRM_ServiceConnections.Barangay', '=', 'CRM_Barangays.id')
                    ->leftJoin('CRM_Towns', 'CRM_ServiceConnections.Town', '=', 'CRM_Towns.id')                    
                    ->leftJoin('CRM_ServiceConnectionAccountTypes', 'CRM_ServiceConnections.AccountType', '=', 'CRM_ServiceConnectionAccountTypes.id')  
                    ->where('CRM_ServiceConnections.Status', 'Forwarded To Planning')
                    ->where(function ($query) {
                        $query->where('Trash', 'No')
                            ->orWhereNull('Trash');
                    })
                    ->select('CRM_ServiceConnections.id as id',
                        'CRM_ServiceConnections.ServiceAccountName as ServiceAccountName',
                        'CRM_ServiceConnections.Status as Status', 
                        'CRM_ServiceConnections.Sitio as Sitio', 
                        'CRM_Towns.Town as Town',
                        'CRM_ServiceConnections.LoadCategory as LoadCategory', 
                        'CRM_ServiceConnections.LongSpan as LongSpan', 
                        'CRM_Barangays.Barangay as Barangay')
                    ->orderBy('CRM_ServiceConnections.ServiceAccountName')
                    ->get();

        $accountTypes = ServiceConnectionAccountTypes::orderBy('id')->get();

        return view('/service_connections/large_load_inspections', ['serviceConnections' => $serviceConnections, 'accountTypes' => $accountTypes]);
    }

    public function largeLoadInspectionUpdate(Request $request) {
        if ($request->ajax()) {
            // ADD INSPECTION DATA
            $largeLoadInspections = new ServiceConnectionLgLoadInsp;

            $largeLoadInspections->id = IDGenerator::generateID();
            $largeLoadInspections->ServiceConnectionId = $request['ServiceConnectionId'];
            $largeLoadInspections->Assessment = $request['Assessment'];
            $largeLoadInspections->DateOfInspection = $request['DateOfInspection'];
            $largeLoadInspections->Notes = $request['Notes'];
            $largeLoadInspections->Options = $request['Options'];

            $largeLoadInspections->save();

            // UPDATE SERVICE CONNECTION STATUS
            $serviceConnection = ServiceConnections::find($request['ServiceConnectionId']);

            $serviceConnection->Status = 'For BoM';
            $serviceConnection->AccountType = $request['AccountType'];

            $serviceConnection->save();

            // CREATE Timeframes
            $timeFrame = new ServiceConnectionTimeframes;
            $timeFrame->id = IDGenerator::generateID();
            $timeFrame->ServiceConnectionId = $request['ServiceConnectionId'];
            $timeFrame->UserId = Auth::id();
            $timeFrame->Status = $request['Assessment'];
            $timeFrame->Notes = '(Power load inspection) See inspection log # <a href="' . route('serviceConnectionLgLoadInsps.show', [$largeLoadInspections->id]) . '">' . $largeLoadInspections->id . '</a> for further details';
            $timeFrame->save();

            return response()->json([ 'success' => true ]);
        }
    }

    public function bomIndex() {
        $serviceConnections = DB::table('CRM_ServiceConnections')
                    ->join('CRM_Barangays', 'CRM_ServiceConnections.Barangay', '=', 'CRM_Barangays.id')
                    ->join('CRM_Towns', 'CRM_ServiceConnections.Town', '=', 'CRM_Towns.id')                    
                    ->leftJoin('CRM_ServiceConnectionAccountTypes', 'CRM_ServiceConnections.AccountType', '=', 'CRM_ServiceConnectionAccountTypes.id')  
                    ->leftJoin('CRM_LargeLoadInspections', 'CRM_ServiceConnections.id', '=', 'CRM_LargeLoadInspections.ServiceConnectionId')
                    ->where('CRM_ServiceConnections.Status', 'For BoM')
                    ->where(function ($query) {
                        $query->where('Trash', 'No')
                            ->orWhereNull('Trash');
                    })
                    ->select('CRM_ServiceConnections.id as id',
                        'CRM_ServiceConnections.ServiceAccountName as ServiceAccountName',
                        'CRM_ServiceConnections.Status as Status', 
                        'CRM_ServiceConnections.Sitio as Sitio', 
                        'CRM_ServiceConnections.AccountApplicationType as AccountApplicationType', 
                        'CRM_Towns.Town as Town',
                        'CRM_ServiceConnectionAccountTypes.AccountType as AccountType',
                        'CRM_Barangays.Barangay as Barangay',
                        'CRM_LargeLoadInspections.Options')
                    ->orderBy('CRM_ServiceConnections.ServiceAccountName')
                    ->get();

        return view('/service_connections/bom_index', ['serviceConnections' => $serviceConnections]);
    }

    public function bomAssigning($scId) {
        $serviceConnection = DB::table('CRM_ServiceConnections')
            ->leftJoin('CRM_Barangays', 'CRM_ServiceConnections.Barangay', '=', 'CRM_Barangays.id')                    
            ->leftJoin('CRM_Towns', 'CRM_ServiceConnections.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_ServiceConnectionAccountTypes', 'CRM_ServiceConnections.AccountType', '=', 'CRM_ServiceConnectionAccountTypes.id')
            ->select('CRM_ServiceConnections.ServiceAccountName',
                    'CRM_ServiceConnections.id',
                    'CRM_ServiceConnections.Sitio',
                    'CRM_ServiceConnections.ContactNumber',
                    'CRM_ServiceConnections.BuildingType',
                    'CRM_ServiceConnections.DateOfApplication',
                    'CRM_Towns.Town',
                    'CRM_Barangays.Barangay')
            ->where('CRM_ServiceConnections.id', $scId)
            ->first();

        $materials = MaterialAssets::orderBy('Description')->get();

        $structures = Structures::orderBy('Data')->get();

        $billOfMaterials = DB::table('CRM_BillOfMaterialsMatrix')
            ->leftJoin('CRM_MaterialAssets', 'CRM_BillOfMaterialsMatrix.MaterialsId', '=', 'CRM_MaterialAssets.id')
            ->leftJoin('CRM_Structures', 'CRM_BillOfMaterialsMatrix.StructureId', '=', 'CRM_Structures.id')    
            ->select('CRM_MaterialAssets.id',
                    'CRM_MaterialAssets.Description',
                    'CRM_BillOfMaterialsMatrix.Amount',
                    DB::raw('SUM(CAST(CRM_BillOfMaterialsMatrix.Quantity AS Integer)) AS ProjectRequirements'),
                    DB::raw('(CAST(CRM_BillOfMaterialsMatrix.Amount As Money) * SUM(CAST(CRM_BillOfMaterialsMatrix.Quantity AS Integer))) AS ExtendedCost'))
            ->where('CRM_BillOfMaterialsMatrix.ServiceConnectionId', $scId)
            ->whereNull('CRM_BillOfMaterialsMatrix.StructureType')
            ->groupBy('CRM_MaterialAssets.Description', 'CRM_BillOfMaterialsMatrix.Amount', 'CRM_MaterialAssets.id')
            ->orderBy('CRM_MaterialAssets.Description')
            ->get(); 

        $structuresAssigned = StructureAssignments::where('ServiceConnectionId', $scId)
            ->whereNotIn('ConAssGrouping', ['9', '1', '3'])            
            ->orderBy('StructureId')->get();
            
        return view('/service_connections/bom_assigning', ['serviceConnection' => $serviceConnection, 
                            'structuresAssigned' => $structuresAssigned, 
                            'billOfMaterials' => $billOfMaterials,
                            'materials' => $materials,
                            'structures' => $structures,
                        ]);
    }

    public function forwardToTransformerAssigning($scId) {
        $serviceConnection = ServiceConnections::find($scId);

        $serviceConnection->Status = 'For Transformer and Pole Assigning';

        $serviceConnection->save();

        // CREATE Timeframes
        $timeFrame = new ServiceConnectionTimeframes;
        $timeFrame->id = IDGenerator::generateID();
        $timeFrame->ServiceConnectionId = $scId;
        $timeFrame->UserId = Auth::id();
        $timeFrame->Status = 'Bill of Materials Assigned';
        $timeFrame->save();

        // CREATE Timeframes
        $timeFrame = new ServiceConnectionTimeframes;
        $timeFrame->id = IDGenerator::generateID();
        $timeFrame->ServiceConnectionId = $scId;
        $timeFrame->UserId = Auth::id();
        $timeFrame->Status = 'For Transformer and Pole Assigning';
        $timeFrame->save();

        return redirect(route('serviceConnections.transformer-assigning', [$scId]));
    }

    public function transformerIndex() {
        $serviceConnections = DB::table('CRM_ServiceConnections')
                    ->leftJoin('CRM_Barangays', 'CRM_ServiceConnections.Barangay', '=', 'CRM_Barangays.id')
                    ->leftJoin('CRM_Towns', 'CRM_ServiceConnections.Town', '=', 'CRM_Towns.id')                    
                    ->leftJoin('CRM_ServiceConnectionAccountTypes', 'CRM_ServiceConnections.AccountType', '=', 'CRM_ServiceConnectionAccountTypes.id')  
                    ->where('CRM_ServiceConnections.Status', 'For Transformer and Pole Assigning')
                    ->where(function ($query) {
                        $query->where('Trash', 'No')
                            ->orWhereNull('Trash');
                    })
                    ->select('CRM_ServiceConnections.id as id',
                        'CRM_ServiceConnections.ServiceAccountName as ServiceAccountName',
                        'CRM_ServiceConnections.Status as Status', 
                        'CRM_ServiceConnections.Sitio as Sitio', 
                        'CRM_Towns.Town as Town',
                        'CRM_ServiceConnectionAccountTypes.AccountType as AccountType',
                        'CRM_Barangays.Barangay as Barangay')
                    ->orderBy('CRM_ServiceConnections.ServiceAccountName')
                    ->get();

        return view('/service_connections/transformer_index', ['serviceConnections' => $serviceConnections]);
    }

    public function transformerAssigning($scId) {
        $serviceConnection = DB::table('CRM_ServiceConnections')
            ->leftJoin('CRM_Barangays', 'CRM_ServiceConnections.Barangay', '=', 'CRM_Barangays.id')                    
            ->leftJoin('CRM_Towns', 'CRM_ServiceConnections.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_ServiceConnectionAccountTypes', 'CRM_ServiceConnections.AccountType', '=', 'CRM_ServiceConnectionAccountTypes.id')
            ->select('CRM_ServiceConnections.ServiceAccountName',
                    'CRM_ServiceConnections.id',
                    'CRM_ServiceConnections.Sitio',
                    'CRM_ServiceConnections.ContactNumber',
                    'CRM_ServiceConnections.BuildingType',
                    'CRM_ServiceConnections.DateOfApplication',
                    'CRM_Towns.Town',
                    'CRM_Barangays.Barangay')
            ->where('CRM_ServiceConnections.id', $scId)
            ->first();

        $transformerIndex = DB::table('CRM_TransformerIndex')
            ->leftJoin('CRM_MaterialAssets', 'CRM_TransformerIndex.NEACode', '=', 'CRM_MaterialAssets.id')
            ->select('CRM_MaterialAssets.*',
                    'CRM_TransformerIndex.id as IndexId')
            ->get();

        $transformerMatrix = DB::table('CRM_TransformersAssignedMatrix')
            ->leftJoin('CRM_MaterialAssets', 'CRM_TransformersAssignedMatrix.MaterialsId', '=', 'CRM_MaterialAssets.id')
            ->select('CRM_TransformersAssignedMatrix.id',
                    'CRM_MaterialAssets.Description',
                    'CRM_MaterialAssets.Amount',
                    'CRM_TransformersAssignedMatrix.Quantity')
            ->where('CRM_TransformersAssignedMatrix.ServiceConnectionId', $scId)
            ->get();

        $structureBrackets = Structures::where('Type', 'A_DT')->get();

        $bracketsAssigned = DB::table('CRM_BillOfMaterialsMatrix')
                ->leftJoin('CRM_MaterialAssets', 'CRM_BillOfMaterialsMatrix.MaterialsId', '=', 'CRM_MaterialAssets.id')
                ->leftJoin('CRM_Structures', 'CRM_BillOfMaterialsMatrix.StructureId', '=', 'CRM_Structures.id')    
                ->select('CRM_MaterialAssets.id',
                        'CRM_MaterialAssets.Description',
                        'CRM_MaterialAssets.Amount',
                        DB::raw('SUM(CAST(CRM_BillOfMaterialsMatrix.Quantity AS Integer)) AS ProjectRequirements'),
                        DB::raw('(CAST(CRM_MaterialAssets.Amount As Money) * SUM(CAST(CRM_BillOfMaterialsMatrix.Quantity AS Integer))) AS ExtendedCost'))
                ->where('CRM_BillOfMaterialsMatrix.ServiceConnectionId', $scId)
                ->where('CRM_BillOfMaterialsMatrix.StructureType', 'A_DT')
                ->groupBy('CRM_MaterialAssets.Description', 'CRM_MaterialAssets.Amount', 'CRM_MaterialAssets.id')
                ->orderBy('CRM_MaterialAssets.Description')
                ->get(); 

        return view('/service_connections/transformer_assigning', ['serviceConnection' => $serviceConnection, 'transformerIndex' => $transformerIndex, 'transformerMatrix' => $transformerMatrix, 'structureBrackets' => $structureBrackets, 'bracketsAssigned' => $bracketsAssigned]);
    }

    public function poleAssigning($scId) {
        $serviceConnection = DB::table('CRM_ServiceConnections')
            ->leftJoin('CRM_Barangays', 'CRM_ServiceConnections.Barangay', '=', 'CRM_Barangays.id')                    
            ->leftJoin('CRM_Towns', 'CRM_ServiceConnections.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_ServiceConnectionAccountTypes', 'CRM_ServiceConnections.AccountType', '=', 'CRM_ServiceConnectionAccountTypes.id')
            ->select('CRM_ServiceConnections.ServiceAccountName',
                    'CRM_ServiceConnections.id',
                    'CRM_ServiceConnections.Sitio',
                    'CRM_ServiceConnections.ContactNumber',
                    'CRM_ServiceConnections.BuildingType',
                    'CRM_ServiceConnections.DateOfApplication',
                    'CRM_Towns.Town',
                    'CRM_Barangays.Barangay')
            ->where('CRM_ServiceConnections.id', $scId)
            ->first();

        $poleIndex = DB::table('CRM_PoleIndex')
            ->leftJoin('CRM_MaterialAssets', 'CRM_PoleIndex.NEACode', '=', 'CRM_MaterialAssets.id')
            ->select('CRM_MaterialAssets.*',
                    'CRM_PoleIndex.id as IndexId',
                    'CRM_PoleIndex.Type as Type')
            ->get();

        $poleAssigned = DB::table('CRM_BillOfMaterialsMatrix')
            ->leftJoin('CRM_MaterialAssets', 'CRM_BillOfMaterialsMatrix.MaterialsId', '=', 'CRM_MaterialAssets.id')  
            ->select('CRM_BillOfMaterialsMatrix.id',
                    'CRM_MaterialAssets.Description',
                    'CRM_MaterialAssets.Amount',
                    'CRM_BillOfMaterialsMatrix.Quantity')
            ->where('CRM_BillOfMaterialsMatrix.ServiceConnectionId', $scId)
            ->where('CRM_BillOfMaterialsMatrix.StructureType', 'POLE')
            ->orderBy('CRM_MaterialAssets.Description')
            ->get(); 

        return view('/service_connections/pole_assigning', ['serviceConnection' => $serviceConnection, 'poleIndex' => $poleIndex, 'poleAssigned' => $poleAssigned]);
    }

    public function quotationSummary($scId) {
        $serviceConnection = DB::table('CRM_ServiceConnections')
            ->join('CRM_Barangays', 'CRM_ServiceConnections.Barangay', '=', 'CRM_Barangays.id')
            ->join('CRM_Towns', 'CRM_ServiceConnections.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_ServiceConnectionAccountTypes', 'CRM_ServiceConnections.AccountType', '=', 'CRM_ServiceConnectionAccountTypes.id')
            ->join('CRM_ServiceConnectionCrew', 'CRM_ServiceConnections.StationCrewAssigned', '=', 'CRM_ServiceConnectionCrew.id')
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
                        'CRM_ServiceConnections.ORNumber as ORNumber', 
                        'CRM_ServiceConnections.Sitio as Sitio', 
                        'CRM_ServiceConnections.LoadCategory as LoadCategory', 
                        'CRM_ServiceConnections.DateTimeOfEnergization as DateTimeOfEnergization', 
                        'CRM_ServiceConnections.DateTimeLinemenArrived as DateTimeLinemenArrived', 
                        'CRM_Towns.Town as Town',
                        'CRM_Barangays.Barangay as Barangay',
                        'CRM_ServiceConnectionAccountTypes.AccountType as AccountType',
                        'CRM_ServiceConnectionCrew.StationName as StationName',
                        'CRM_ServiceConnectionCrew.CrewLeader as CrewLeader',
                        'CRM_ServiceConnections.TemporaryDurationInMonths as TemporaryDurationInMonths',
                        'CRM_ServiceConnectionCrew.Members as Members')
        ->where('CRM_ServiceConnections.id', $scId)
        ->where(function ($query) {
            $query->where('CRM_ServiceConnections.Trash', 'No')
                ->orWhereNull('CRM_ServiceConnections.Trash');
        })
        ->first(); 

        $materials = DB::table('CRM_BillOfMaterialsMatrix')
                ->leftJoin('CRM_MaterialAssets', 'CRM_BillOfMaterialsMatrix.MaterialsId', '=', 'CRM_MaterialAssets.id')   
                ->select('CRM_MaterialAssets.id',
                        'CRM_MaterialAssets.Description',
                        'CRM_BillOfMaterialsMatrix.Amount',
                        DB::raw('SUM(CAST(CRM_BillOfMaterialsMatrix.Quantity AS Integer)) AS ProjectRequirements'),
                        DB::raw('(CAST(CRM_BillOfMaterialsMatrix.Amount As Money) * SUM(CAST(CRM_BillOfMaterialsMatrix.Quantity AS Integer))) AS Cost'))
                ->where('CRM_BillOfMaterialsMatrix.ServiceConnectionId', $scId)
                // ->where('CRM_BillOfMaterialsMatrix.StructureType', 'A_DT')
                ->groupBy('CRM_MaterialAssets.Description', 'CRM_BillOfMaterialsMatrix.Amount', 'CRM_MaterialAssets.id')
                ->orderBy('CRM_MaterialAssets.Description')
                ->get();
                
        $poles = DB::table('CRM_BillOfMaterialsMatrix')
                ->leftJoin('CRM_MaterialAssets', 'CRM_BillOfMaterialsMatrix.MaterialsId', '=', 'CRM_MaterialAssets.id')   
                ->select('CRM_MaterialAssets.id',
                        'CRM_MaterialAssets.Description',
                        DB::raw('SUM(CAST(CRM_BillOfMaterialsMatrix.Quantity AS Integer)) AS ProjectRequirements'),)
                ->where('CRM_BillOfMaterialsMatrix.ServiceConnectionId', $scId)
                ->where('CRM_BillOfMaterialsMatrix.StructureType', 'POLE')
                ->groupBy('CRM_MaterialAssets.Description', 'CRM_MaterialAssets.id')
                ->orderBy('CRM_MaterialAssets.Description')
                ->get();

        $transformers = DB::table('CRM_TransformersAssignedMatrix')
                ->leftJoin('CRM_MaterialAssets', 'CRM_TransformersAssignedMatrix.MaterialsId', '=', 'CRM_MaterialAssets.id')
                ->select('CRM_MaterialAssets.id',
                        'CRM_TransformersAssignedMatrix.id as TransformerId',
                        'CRM_MaterialAssets.Description',
                        'CRM_MaterialAssets.Amount',
                        'CRM_TransformersAssignedMatrix.Quantity',
                        'CRM_TransformersAssignedMatrix.Type')
                ->where('CRM_TransformersAssignedMatrix.ServiceConnectionId', $scId)
                ->get();

        $structures = DB::table('CRM_StructureAssignments')
            ->leftJoin('CRM_Structures', 'CRM_StructureAssignments.StructureId', '=', 'CRM_Structures.Data')
            ->select('CRM_Structures.id as id',
                    'CRM_StructureAssignments.StructureId',
                    DB::raw('SUM(CAST(CRM_StructureAssignments.Quantity AS Integer)) AS Quantity'))
            ->where('ServiceConnectionId', $scId)
            ->groupBy('CRM_Structures.id', 'CRM_StructureAssignments.StructureId')
            ->get();

        $conAss = DB::table('CRM_StructureAssignments')
            ->where('ServiceConnectionId', $scId)
            ->select('ConAssGrouping', 'StructureId', 'Quantity', 'Type')
            ->groupBy('StructureId', 'ConAssGrouping', 'Quantity', 'Type')
            ->orderBy('ConAssGrouping')
            ->get();

        $billOfMaterialsSummary = BillsOfMaterialsSummary::where('ServiceConnectionId', $scId)->first();
        if ($billOfMaterialsSummary == null) {
            $billOfMaterialsSummary = new BillsOfMaterialsSummary;
            $billOfMaterialsSummary->id = IDGenerator::generateID();
            $billOfMaterialsSummary->ServiceConnectionId = $scId;
            $billOfMaterialsSummary->TransformerLaborCostPercentage = '0.035';
            $billOfMaterialsSummary->MaterialLaborCostPercentage = '0.35';
            $billOfMaterialsSummary->HandlingCostPercentage = '0.30';
            $billOfMaterialsSummary->MonthDuration = $serviceConnection->TemporaryDurationInMonths;

            // CALCULATE SUB-TOTAL
            $subTtl = 0.0;
            foreach($materials as $items) { // materials total
                $subTtl += floatval($items->Cost);
            }
            foreach($transformers as $items) { 
                if ($items->Type != 'Transformer') {
                    $subTtl += floatval($items->Quantity) * floatval($items->Amount);
                }
            }

            // transformer sub total
            $transSoloTtl = 0.0;
            foreach($transformers as $items) { 
                if ($items->Type == 'Transformer') {
                    $transSoloTtl += floatval($items->Quantity) * floatval($items->Amount);
                }                
            }

            // sub total
            $billOfMaterialsSummary->SubTotal = $subTtl + $transSoloTtl;

            // transformer total
            $billOfMaterialsSummary->TransformerTotal = $transSoloTtl;

            // transformer labor cost
            $billOfMaterialsSummary->TransformerLaborCost = $transSoloTtl * floatval($billOfMaterialsSummary->TransformerLaborCostPercentage);

            // materials labor cost            
            $billOfMaterialsSummary->MaterialLaborCost = $subTtl * floatval($billOfMaterialsSummary->MaterialLaborCostPercentage);

            // total labor cost except handling (just transformer and material labor costs)
            $billOfMaterialsSummary->LaborCost = floatval($billOfMaterialsSummary->TransformerLaborCost) + floatval($billOfMaterialsSummary->MaterialLaborCost);

            // handling labor cost            
            $billOfMaterialsSummary->HandlingCost = floatval($billOfMaterialsSummary->LaborCost) * floatval($billOfMaterialsSummary->HandlingCostPercentage);

            // overall total
            $billOfMaterialsSummary->Total = floatval($billOfMaterialsSummary->SubTotal) +
                                            floatval($billOfMaterialsSummary->HandlingCost) +
                                            floatval($billOfMaterialsSummary->LaborCost);
            
            $billOfMaterialsSummary->save();
        }

        return view('/service_connections/quotation_summary', [
                'serviceConnection' => $serviceConnection, 
                'materials' => $materials, 
                'transformers' => $transformers, 
                'billOfMaterialsSummary' => $billOfMaterialsSummary,
                'structures' => $structures,
                'conAss' => $conAss,
                'poles' => $poles,
            ]
        );
    }

    public function spanningAssigning($scId) {
        $serviceConnection = DB::table('CRM_ServiceConnections')
            ->leftJoin('CRM_Barangays', 'CRM_ServiceConnections.Barangay', '=', 'CRM_Barangays.id')                    
            ->leftJoin('CRM_Towns', 'CRM_ServiceConnections.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_ServiceConnectionAccountTypes', 'CRM_ServiceConnections.AccountType', '=', 'CRM_ServiceConnectionAccountTypes.id')
            ->select('CRM_ServiceConnections.ServiceAccountName',
                    'CRM_ServiceConnections.id',
                    'CRM_ServiceConnections.Sitio',
                    'CRM_ServiceConnections.ContactNumber',
                    'CRM_ServiceConnections.BuildingType',
                    'CRM_ServiceConnections.DateOfApplication',
                    'CRM_Towns.Town',
                    'CRM_Barangays.Barangay')
            ->where('CRM_ServiceConnections.id', $scId)
            ->first();

        $billOfMaterials = DB::table('CRM_BillOfMaterialsMatrix')
            ->leftJoin('CRM_MaterialAssets', 'CRM_BillOfMaterialsMatrix.MaterialsId', '=', 'CRM_MaterialAssets.id')   
            ->select('CRM_MaterialAssets.id',
                    'CRM_MaterialAssets.Description',
                    'CRM_MaterialAssets.Amount',
                    DB::raw('SUM(CAST(CRM_BillOfMaterialsMatrix.Quantity AS Integer)) AS ProjectRequirements'),
                    DB::raw('(CAST(CRM_MaterialAssets.Amount As Money) * SUM(CAST(CRM_BillOfMaterialsMatrix.Quantity AS Integer))) AS ExtendedCost'))
            ->where('CRM_BillOfMaterialsMatrix.ServiceConnectionId', $scId)
            ->where('CRM_BillOfMaterialsMatrix.StructureType', 'SPANNING')
            ->groupBy('CRM_MaterialAssets.Description', 'CRM_MaterialAssets.Amount', 'CRM_MaterialAssets.id')
            ->orderBy('CRM_MaterialAssets.Description')
            ->get(); 
        
        $spanningData = SpanningData::where('ServiceConnectionId', $scId)->first();

        return view('/service_connections/spanning_assigning', [
            'serviceConnection' => $serviceConnection,
            'billOfMaterials' => $billOfMaterials,
            'spanningData' => $spanningData
        ]);
    }

    public function forwardToVerification($scId) {
        $serviceConnection = ServiceConnections::find($scId);
        $serviceConnection->Status = 'For Inspection';
        $serviceConnection->save();

        // CREATE Timeframes
        $timeFrame = new ServiceConnectionTimeframes;
        $timeFrame->id = IDGenerator::generateID();
        $timeFrame->ServiceConnectionId = $scId;
        $timeFrame->UserId = Auth::id();
        $timeFrame->Status = 'Forwarded for Verfication';
        $timeFrame->Notes = 'Forwarded to ISD for Verfication';
        $timeFrame->save();

        return redirect(route('serviceConnections.show', [$scId]));
    }

    public function largeLoadPredefinedMaterials($scId, $options) {
        $serviceConnection = DB::table('CRM_ServiceConnections')
            ->leftJoin('CRM_Barangays', 'CRM_ServiceConnections.Barangay', '=', 'CRM_Barangays.id')                    
            ->leftJoin('CRM_Towns', 'CRM_ServiceConnections.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_ServiceConnectionAccountTypes', 'CRM_ServiceConnections.AccountType', '=', 'CRM_ServiceConnectionAccountTypes.id')            
            ->leftJoin('CRM_LargeLoadInspections', 'CRM_ServiceConnections.id', '=', 'CRM_LargeLoadInspections.ServiceConnectionId')
            ->select('CRM_ServiceConnections.ServiceAccountName',
                    'CRM_ServiceConnections.id',
                    'CRM_ServiceConnections.Sitio',
                    'CRM_ServiceConnections.ContactNumber',
                    'CRM_ServiceConnections.BuildingType',
                    'CRM_ServiceConnections.DateOfApplication',
                    'CRM_Towns.Town',
                    'CRM_Barangays.Barangay',
                    'CRM_ServiceConnections.AccountApplicationType',
                    'CRM_ServiceConnections.TemporaryDurationInMonths',
                    'CRM_LargeLoadInspections.Options')
            ->where('CRM_ServiceConnections.id', $scId)
            ->first();

        $materials = MaterialAssets::orderBy('Description')->get();

        if ($serviceConnection->AccountApplicationType == 'Temporary' && $serviceConnection->Options == 'Transformer Only') {
            $preDefMaterials = DB::table('CRM_PreDefinedMaterials')
            ->leftJoin('CRM_MaterialAssets', 'CRM_PreDefinedMaterials.NEACode', '=', 'CRM_MaterialAssets.id')
            ->where('CRM_PreDefinedMaterials.Options', $options)
            ->where('CRM_PreDefinedMaterials.ApplicationType', $serviceConnection->AccountApplicationType)
            ->select('CRM_PreDefinedMaterials.id',
                    'CRM_PreDefinedMaterials.NEACode',
                    'CRM_MaterialAssets.Description',
                    'CRM_MaterialAssets.Amount',
                    'CRM_PreDefinedMaterials.Quantity',
                    'CRM_PreDefinedMaterials.LaborPercentage',
                    DB::raw('(CAST(CRM_MaterialAssets.Amount AS DECIMAL(9,2)) * CAST(CRM_PreDefinedMaterials.Quantity AS DECIMAL(9,2)) * 0.15 * ' . floatval($serviceConnection->TemporaryDurationInMonths) . ') AS Cost'),
                    DB::raw('(CAST(CRM_MaterialAssets.Amount AS DECIMAL(9,2)) * CAST(CRM_PreDefinedMaterials.Quantity AS DECIMAL(9,2)) * CAST(CRM_PreDefinedMaterials.LaborPercentage AS DECIMAL(9,4))) AS LaborCost'))
            ->get();
        } else {
            $preDefMaterials = DB::table('CRM_PreDefinedMaterials')
            ->leftJoin('CRM_MaterialAssets', 'CRM_PreDefinedMaterials.NEACode', '=', 'CRM_MaterialAssets.id')
            ->where('CRM_PreDefinedMaterials.Options', $options)
            ->where('CRM_PreDefinedMaterials.ApplicationType', $serviceConnection->AccountApplicationType)
            ->select('CRM_PreDefinedMaterials.id',
                    'CRM_PreDefinedMaterials.NEACode',
                    'CRM_MaterialAssets.Description',
                    'CRM_MaterialAssets.Amount',
                    'CRM_PreDefinedMaterials.Quantity',
                    'CRM_PreDefinedMaterials.LaborPercentage',
                    DB::raw('(CAST(CRM_MaterialAssets.Amount AS DECIMAL(9,2)) * CAST(CRM_PreDefinedMaterials.Quantity AS DECIMAL(9,2))) AS Cost'),
                    DB::raw('(CAST(CRM_MaterialAssets.Amount AS DECIMAL(9,2)) * CAST(CRM_PreDefinedMaterials.Quantity AS DECIMAL(9,2)) * CAST(CRM_PreDefinedMaterials.LaborPercentage AS DECIMAL(9,4))) AS LaborCost'))
            ->get();
        }

        if ($preDefMaterials != null) {
            $preDef = PreDefinedMaterialsMatrix::where('ServiceConnectionId', $scId)
                            ->get();
            
            if (count($preDef) < 1) {
                // SAVE PRE DEFINED MATERIALS
                foreach($preDefMaterials as $item) {
                    $preDef = PreDefinedMaterialsMatrix::where('ServiceConnectionId', $scId)
                                ->where('NEACode', $item->NEACode)
                                ->first();
                    if ($preDef == null) {
                        $preDef = new PreDefinedMaterialsMatrix;
                        $preDef->id = IDGenerator::generateID();
                        $preDef->ServiceConnectionId = $scId;
                        $preDef->NEACode = $item->NEACode;
                        $preDef->Description = $item->Description;
                        $preDef->Quantity = $item->Quantity;
                        $preDef->Options = $options;
                        $preDef->ApplicationType = $serviceConnection->AccountApplicationType;
                        $preDef->Cost = $item->Cost;
                        $preDef->LaborCost = $item->LaborCost;
                        $preDef->Amount = $item->Amount;
                        $preDef->LaborPercentage = $item->LaborPercentage;
                        $preDef->save();
                    } else {
                        $preDef->ServiceConnectionId = $scId;
                        $preDef->NEACode = $item->NEACode;
                        $preDef->Description = $item->Description;
                        $preDef->Quantity = $item->Quantity;
                        $preDef->Options = $options;
                        $preDef->ApplicationType = $serviceConnection->AccountApplicationType;
                        $preDef->Cost = $item->Cost;
                        $preDef->LaborCost = $item->LaborCost;
                        $preDef->Amount = $item->Amount;
                        $preDef->LaborPercentage = $item->LaborPercentage;
                        $preDef->save();
                    }                
                }
            }            
        }

        $preDef = PreDefinedMaterialsMatrix::where('ServiceConnectionId', $scId)
                            ->get();

        return view('/service_connections/largeload_predefined_materials', 
            [
                'serviceConnection' => $serviceConnection,
                'materials' => $materials,
                'preDefMaterials' => $preDefMaterials,
                'preDef' => $preDef,
            ]);
    }

    public function fleetMonitor() {
        return view('/service_connections/fleet_monitor');
    }
}
