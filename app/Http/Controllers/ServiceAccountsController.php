<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateServiceAccountsRequest;
use App\Http\Requests\UpdateServiceAccountsRequest;
use App\Repositories\ServiceAccountsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use App\Models\ServiceConnections;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use App\Models\Towns;
use App\Models\Barangays;
use App\Models\ServiceConnectionInspections;
use App\Models\ServiceConnectionAccountTypes;
use App\Models\ServiceAccounts;
use App\Models\MeterReaders;
use App\Models\BillingMeters;
use App\Models\BillingTransformers;
use App\Models\ServiceConnectionMtrTrnsfrmr;
use Illuminate\Pagination\LengthAwarePaginator;
use Flash;
use Response;

class ServiceAccountsController extends AppBaseController
{
    /** @var  ServiceAccountsRepository */
    private $serviceAccountsRepository;

    public function __construct(ServiceAccountsRepository $serviceAccountsRepo)
    {
        $this->middleware('auth');
        $this->serviceAccountsRepository = $serviceAccountsRepo;
    }

    /**
     * Display a listing of the ServiceAccounts.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        if ($request['params'] == null) {
            $serviceAccounts = DB::table('Billing_ServiceAccounts')
                        ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
                        ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
                        ->select('Billing_ServiceAccounts.ServiceAccountName', 'Billing_ServiceAccounts.id', 'CRM_Towns.Town', 'CRM_Barangays.Barangay', 'Billing_ServiceAccounts.AccountCount')
                        ->orderBy('Billing_ServiceAccounts.ServiceAccountName')
                        ->paginate(15);
        } else {
            $serviceAccounts = DB::table('Billing_ServiceAccounts')
                        ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
                        ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
                        ->select('Billing_ServiceAccounts.ServiceAccountName', 'Billing_ServiceAccounts.id', 'CRM_Towns.Town', 'CRM_Barangays.Barangay', 'Billing_ServiceAccounts.AccountCount')
                        ->where('Billing_ServiceAccounts.ServiceAccountName', 'LIKE', '%' . $request['params'] . '%')
                        ->orWhere('Billing_ServiceAccounts.id', 'LIKE', '%' . $request['params'] . '%')
                        ->orderBy('Billing_ServiceAccounts.ServiceAccountName')
                        ->paginate(15);
        }     

        return view('service_accounts.index', ['serviceAccounts' => $serviceAccounts]);
    }

    /**
     * Show the form for creating a new ServiceAccounts.
     *
     * @return Response
     */
    public function create()
    {
        return view('service_accounts.create');
    }

    /**
     * Store a newly created ServiceAccounts in storage.
     *
     * @param CreateServiceAccountsRequest $request
     *
     * @return Response
     */
    public function store(CreateServiceAccountsRequest $request)
    {
        $input = $request->all();

        $sc = ServiceAccounts::find($input['id']);

        if ($sc != null) {
            $serviceAccounts = $this->serviceAccountsRepository->update($request->all(), $sc->id);

            return redirect(route('serviceAccounts.account-migration-step-two', [$input['id']]));
        } else {
            $serviceAccounts = $this->serviceAccountsRepository->create($input);

            Flash::success('Service Accounts saved successfully.');

            return redirect(route('serviceAccounts.account-migration-step-two', [$serviceAccounts->id]));
        }
        
    }

    /**
     * Display the specified ServiceAccounts.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $serviceAccounts = DB::table('Billing_ServiceAccounts')
            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
            ->select('Billing_ServiceAccounts.id',
                    'Billing_ServiceAccounts.ServiceAccountName',
                    'Billing_ServiceAccounts.OldAccountNo',
                    'Billing_ServiceAccounts.AccountCount',
                    'Billing_ServiceAccounts.Purok',
                    'Billing_ServiceAccounts.AccountType',
                    'Billing_ServiceAccounts.AccountStatus',
                    'Billing_ServiceAccounts.AreaCode',
                    'Billing_ServiceAccounts.SequenceCode',
                    'Billing_ServiceAccounts.ForDistribution',
                    'Billing_ServiceAccounts.Organization',
                    'Billing_ServiceAccounts.Main',
                    'Billing_ServiceAccounts.GroupCode',
                    'Billing_ServiceAccounts.Multiplier',
                    'Billing_ServiceAccounts.Coreloss',
                    'Billing_ServiceAccounts.ConnectionDate',
                    'Billing_ServiceAccounts.ServiceConnectionId',
                    'Billing_ServiceAccounts.SeniorCitizen',
                    'Billing_ServiceAccounts.Evat5Percent',
                    'Billing_ServiceAccounts.Ewt2Percent',
                    'CRM_Towns.Town',
                    'CRM_Barangays.Barangay')
            ->where('Billing_ServiceAccounts.id', $id)
            ->first();

        $meters = BillingMeters::where('ServiceAccountId', $id)
            ->orderByDesc('created_at')
            ->first();

        $transformer = BillingTransformers::where('ServiceAccountId', $id)
            ->orderByDesc('created_at')
            ->first();

        if (empty($serviceAccounts)) {
            Flash::error('Service Accounts not found');

            return redirect(route('serviceAccounts.index'));
        }

        return view('service_accounts.show', [
            'serviceAccounts' => $serviceAccounts,
            'meters' => $meters,
            'transformer' => $transformer,
        ]);
    }

    /**
     * Show the form for editing the specified ServiceAccounts.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $serviceAccount = $this->serviceAccountsRepository->find($id);

        if (empty($serviceAccount)) {
            Flash::error('Service Accounts not found');

            return redirect(route('serviceAccounts.index'));
        }

        return view('service_accounts.edit')->with('serviceAccount', $serviceAccount);
    }

    /**
     * Update the specified ServiceAccounts in storage.
     *
     * @param int $id
     * @param UpdateServiceAccountsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateServiceAccountsRequest $request)
    {
        $serviceAccounts = $this->serviceAccountsRepository->find($id);

        if (empty($serviceAccounts)) {
            Flash::error('Service Accounts not found');

            return redirect(route('serviceAccounts.index'));
        }

        $serviceAccounts = $this->serviceAccountsRepository->update($request->all(), $id);

        Flash::success('Service Accounts updated successfully.');

        return redirect(route('serviceAccounts.show', [$id]));
    }

    /**
     * Remove the specified ServiceAccounts from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $serviceAccounts = $this->serviceAccountsRepository->find($id);

        if (empty($serviceAccounts)) {
            Flash::error('Service Accounts not found');

            return redirect(route('serviceAccounts.index'));
        }

        $this->serviceAccountsRepository->delete($id);

        Flash::success('Service Accounts deleted successfully.');

        return redirect(route('serviceAccounts.index'));
    }

    public function pendingAccounts() {
        $serviceConnections = DB::table('CRM_ServiceConnections')
            ->join('CRM_Barangays', 'CRM_ServiceConnections.Barangay', '=', 'CRM_Barangays.id')                    
            ->join('CRM_Towns', 'CRM_ServiceConnections.Town', '=', 'CRM_Towns.id')
            ->select('CRM_ServiceConnections.id as id',
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
            ->where('Status', 'Energized')          
            ->orderBy('CRM_ServiceConnections.ServiceAccountName')
            ->get();

        return view('/service_accounts/pending_accounts', ['serviceConnections' => $serviceConnections]);
    }

    public function accountMigration($id) {
        $serviceConnection = ServiceConnections::find($id);
        $serviceAccount = ServiceAccounts::where('ServiceConnectionId', $id)->first();
        $serviceConnectionInspection = ServiceConnectionInspections::where('ServiceConnectionId', $id)->orderByDesc('created_at')->first();
        $towns = Towns::where('id', $serviceConnection->Town)->pluck('Town', 'id');
        $barangays = Barangays::where('TownId', $serviceConnection->Town)->pluck('Barangay', 'id');
        $accountTypes = ServiceConnectionAccountTypes::all();
        $meterReaders = MeterReaders::all();

        return view('/service_accounts/account_migration',
            [
                'serviceConnection' => $serviceConnection,
                'inspection' => $serviceConnectionInspection,
                'town' => $towns,
                'barangays' => $barangays,
                'accountTypes' => $accountTypes,
                'meterReaders' => $meterReaders,
                'serviceAccount' => $serviceAccount,
            ]
        );
    }

    public function accountMigrationStepTwo($id) {
        $serviceAccount = DB::table('Billing_ServiceAccounts')
            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
            ->select('Billing_ServiceAccounts.id',
                    'Billing_ServiceAccounts.OldAccountNo',
                    'Billing_ServiceAccounts.ServiceAccountName',
                    'CRM_Towns.Town',
                    'CRM_Barangays.Barangay',
                    'Billing_ServiceAccounts.Purok as Sitio',
                    'Billing_ServiceAccounts.AccountType',
                    'Billing_ServiceAccounts.AreaCode',
                    'Billing_ServiceAccounts.SequenceCode',
                    'Billing_ServiceAccounts.ServiceConnectionId',
                    'Billing_ServiceAccounts.MeterDetailsId')
            ->where('Billing_ServiceAccounts.id', $id)
            ->first();

        $serviceConnection = ServiceConnections::find($serviceAccount->ServiceConnectionId);
        $meters = BillingMeters::find($serviceAccount->MeterDetailsId);
        $meterAndTransformer = ServiceConnectionMtrTrnsfrmr::where('ServiceConnectionId', $serviceAccount->ServiceConnectionId)->first();

        return view('/service_accounts/account_migration_step_two', [
            'serviceAccount' => $serviceAccount,
            'meterAndTransformer' => $meterAndTransformer,
            'serviceConnection' => $serviceConnection,
            'meters' => $meters
        ]);
    }

    public function accountMigrationStepThree($id) {
        $serviceAccount = DB::table('Billing_ServiceAccounts')
            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
            ->select('Billing_ServiceAccounts.id',
                    'Billing_ServiceAccounts.OldAccountNo',
                    'Billing_ServiceAccounts.ServiceAccountName',
                    'CRM_Towns.Town',
                    'CRM_Barangays.Barangay',
                    'Billing_ServiceAccounts.Purok as Sitio',
                    'Billing_ServiceAccounts.AccountType',
                    'Billing_ServiceAccounts.AreaCode',
                    'Billing_ServiceAccounts.SequenceCode',
                    'Billing_ServiceAccounts.ServiceConnectionId',
                    'Billing_ServiceAccounts.MeterDetailsId')
            ->where('Billing_ServiceAccounts.id', $id)
            ->first();

        $serviceConnection = ServiceConnections::find($serviceAccount->ServiceConnectionId);
        $meters = BillingMeters::find($serviceAccount->MeterDetailsId);
        $meterAndTransformer = ServiceConnectionMtrTrnsfrmr::where('ServiceConnectionId', $serviceAccount->ServiceConnectionId)->first();

        return view('/service_accounts/account_migration_step_three', [
            'serviceAccount' => $serviceAccount,
            'meterAndTransformer' => $meterAndTransformer,
            'serviceConnection' => $serviceConnection,
            'meters' => $meters
        ]);
    }

    public function updateStepOne($id) {
        $serviceAccount = ServiceAccounts::find($id);
        $towns = Towns::where('id', $serviceAccount->Town)->pluck('Town', 'id');
        $barangays = Barangays::where('TownId', $serviceAccount->Town)->pluck('Barangay', 'id');
        $accountTypes = ServiceConnectionAccountTypes::all();
        $meterReaders = MeterReaders::all();

        return view('/service_accounts/update_step_one', [
            'serviceAccount' => $serviceAccount,
            'towns' => $towns,
            'barangays' => $barangays,
            'accountTypes' => $accountTypes,
            'meterReaders' => $meterReaders
        ]);
    }
}
