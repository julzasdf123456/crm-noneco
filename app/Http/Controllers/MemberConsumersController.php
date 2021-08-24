<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateMemberConsumersRequest;
use App\Http\Requests\UpdateMemberConsumersRequest;
use App\Repositories\MemberConsumersRepository;
use App\Http\Controllers\AppBaseController;
use App\Models\MemberConsumerTypes;
use Illuminate\Http\Request;
use App\Models\IDGenerator;
use App\Models\Barangays;
use App\Models\Towns;
use App\Models\MemberConsumers;
use Illuminate\Support\Facades\DB;
use Flash;
use Response;

class MemberConsumersController extends AppBaseController
{
    /** @var  MemberConsumersRepository */
    private $memberConsumersRepository;

    public function __construct(MemberConsumersRepository $memberConsumersRepo)
    {
        $this->middleware('auth');
        $this->memberConsumersRepository = $memberConsumersRepo;
    }

    /**
     * Display a listing of the MemberConsumers.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $memberConsumers = $this->memberConsumersRepository->all();

        return view('member_consumers.index')
            ->with('memberConsumers', $memberConsumers);
    }

    /**
     * Show the form for creating a new MemberConsumers.
     *
     * @return Response
     */
    public function create()
    {
        $memberConsumers = null;

        $types = MemberConsumerTypes::orderByDesc('Id')->pluck('Type', 'Id');

        $barangays = Barangays::orderBy('Barangay')->pluck('Barangay', 'id');

        $towns = Towns::orderBy('Town')->pluck('Town', 'id');
        
        $cond = 'new';

        return view('member_consumers.create', ['memberConsumers' => $memberConsumers, 'types' => $types, 'cond' => $cond, 'barangays' => $barangays, 'towns' => $towns]);
    }

    /**
     * Store a newly created MemberConsumers in storage.
     *
     * @param CreateMemberConsumersRequest $request
     *
     * @return Response
     */
    public function store(CreateMemberConsumersRequest $request)
    {
        $input = $request->all();

        $memberConsumers = $this->memberConsumersRepository->create($input);

        Flash::success('Member Consumers saved successfully.');

        if ($input['CivilStatus'] == 'Married') {
            return redirect(route('memberConsumerSpouses.create', [$input['Id']]));
        } else {
            return redirect(route('memberConsumers.index'));
        }
    }

    /**
     * Display the specified MemberConsumers.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $memberConsumers = DB::table('CRM_MemberConsumers')
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
                            ->where('CRM_MemberConsumers.Id', $id)
                            ->first();

        $memberConsumerSpouse = DB::table('CRM_MemberConsumerSpouse')
                            ->join('CRM_MemberConsumers', 'CRM_MemberConsumerSpouse.MemberConsumerId', '=', 'CRM_MemberConsumers.id')
                            ->select('CRM_MemberConsumerSpouse.*')
                            ->where('CRM_MemberConsumerSpouse.MemberConsumerId', $id)
                            ->first();

        if (empty($memberConsumers)) {
            Flash::error('Member Consumers not found');

            return redirect(route('memberConsumers.index'));
        }
        
        return view('member_consumers.show', ['memberConsumers' => $memberConsumers, 'memberConsumerSpouse' => $memberConsumerSpouse]);
    }

    /**
     * Show the form for editing the specified MemberConsumers.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $memberConsumers = $this->memberConsumersRepository->find($id);

        $types = MemberConsumerTypes::orderByDesc('Id')->pluck('Type', 'Id');

        $barangays = Barangays::orderBy('Barangay')->pluck('Barangay', 'id');

        $towns = Towns::orderBy('Town')->pluck('Town', 'id');

        $cond = 'edit';

        if (empty($memberConsumers)) {
            Flash::error('Member Consumers not found');

            return redirect(route('memberConsumers.index'));
        }

        return view('member_consumers.edit', ['memberConsumers' => $memberConsumers, 'types' => $types, 'cond' => $cond, 'barangays' => $barangays, 'towns' => $towns]);
    }

    /**
     * Update the specified MemberConsumers in storage.
     *
     * @param int $id
     * @param UpdateMemberConsumersRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateMemberConsumersRequest $request)
    {
        $memberConsumers = $this->memberConsumersRepository->find($id);

        if (empty($memberConsumers)) {
            Flash::error('Member Consumers not found');

            return redirect(route('memberConsumers.index'));
        }

        $memberConsumers = $this->memberConsumersRepository->update($request->all(), $id);

        Flash::success('Member Consumers updated successfully.');

        return redirect(route('memberConsumers.show', [$id]));
    }

    /**
     * Remove the specified MemberConsumers from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $memberConsumers = $this->memberConsumersRepository->find($id);

        if (empty($memberConsumers)) {
            Flash::error('Member Consumers not found');

            return redirect(route('memberConsumers.index'));
        }

        $this->memberConsumersRepository->delete($id);

        Flash::success('Member Consumers deleted successfully.');

        return redirect(route('memberConsumers.index'));
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
                                                <a href="' . route('memberConsumers.show', [$row->ConsumerId]) . '" class="text-primary" style="margin-top: 5px; padding: 8px;" title="View"><i class="fas fa-eye"></i></a>
                                                <a href="' . route('memberConsumers.edit', [$row->ConsumerId]) . '" class="text-warning" style="margin-top: 5px; padding: 8px;" title="Edit"><i class="fas fa-pen"></i></a>
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
}
