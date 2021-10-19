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
                        ->select('Billing_ServiceAccounts.ServiceAccountName', 'Billing_ServiceAccounts.id', 'CRM_Towns.Town', 'CRM_Barangays.Barangay')
                        ->orderBy('Billing_ServiceAccounts.ServiceAccountName')
                        ->paginate(15);
        } else {
            $serviceAccounts = DB::table('Billing_ServiceAccounts')
                        ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
                        ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
                        ->select('Billing_ServiceAccounts.ServiceAccountName', 'Billing_ServiceAccounts.id', 'CRM_Towns.Town', 'CRM_Barangays.Barangay')
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

        $serviceAccounts = $this->serviceAccountsRepository->create($input);

        Flash::success('Service Accounts saved successfully.');

        return redirect(route('serviceAccounts.index'));
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
        $serviceAccounts = $this->serviceAccountsRepository->find($id);

        if (empty($serviceAccounts)) {
            Flash::error('Service Accounts not found');

            return redirect(route('serviceAccounts.index'));
        }

        return view('service_accounts.show')->with('serviceAccounts', $serviceAccounts);
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
        $serviceAccounts = $this->serviceAccountsRepository->find($id);

        if (empty($serviceAccounts)) {
            Flash::error('Service Accounts not found');

            return redirect(route('serviceAccounts.index'));
        }

        return view('service_accounts.edit')->with('serviceAccounts', $serviceAccounts);
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

        return redirect(route('serviceAccounts.index'));
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

        return view('/service_accounts/account_migration',
            [
                'serviceConnection' => $serviceConnection,
            ]
        );
    }
}
