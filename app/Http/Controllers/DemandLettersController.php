<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDemandLettersRequest;
use App\Http\Requests\UpdateDemandLettersRequest;
use App\Repositories\DemandLettersRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\ServiceAccounts;
use App\Models\Towns;
use Flash;
use Response;

class DemandLettersController extends AppBaseController
{
    /** @var  DemandLettersRepository */
    private $demandLettersRepository;

    public function __construct(DemandLettersRepository $demandLettersRepo)
    {
        $this->middleware('auth');
        $this->demandLettersRepository = $demandLettersRepo;
    }

    /**
     * Display a listing of the DemandLetters.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $demandLetters = $this->demandLettersRepository->all();

        return view('demand_letters.index')
            ->with('demandLetters', $demandLetters);
    }

    /**
     * Show the form for creating a new DemandLetters.
     *
     * @return Response
     */
    public function create()
    {
        return view('demand_letters.create');
    }

    /**
     * Store a newly created DemandLetters in storage.
     *
     * @param CreateDemandLettersRequest $request
     *
     * @return Response
     */
    public function store(CreateDemandLettersRequest $request)
    {
        $input = $request->all();

        $demandLetters = $this->demandLettersRepository->create($input);

        Flash::success('Demand Letters saved successfully.');

        return redirect(route('demandLetters.index'));
    }

    /**
     * Display the specified DemandLetters.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $demandLetters = $this->demandLettersRepository->find($id);

        if (empty($demandLetters)) {
            Flash::error('Demand Letters not found');

            return redirect(route('demandLetters.index'));
        }

        return view('demand_letters.show')->with('demandLetters', $demandLetters);
    }

    /**
     * Show the form for editing the specified DemandLetters.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $demandLetters = $this->demandLettersRepository->find($id);

        if (empty($demandLetters)) {
            Flash::error('Demand Letters not found');

            return redirect(route('demandLetters.index'));
        }

        return view('demand_letters.edit')->with('demandLetters', $demandLetters);
    }

    /**
     * Update the specified DemandLetters in storage.
     *
     * @param int $id
     * @param UpdateDemandLettersRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDemandLettersRequest $request)
    {
        $demandLetters = $this->demandLettersRepository->find($id);

        if (empty($demandLetters)) {
            Flash::error('Demand Letters not found');

            return redirect(route('demandLetters.index'));
        }

        $demandLetters = $this->demandLettersRepository->update($request->all(), $id);

        Flash::success('Demand Letters updated successfully.');

        return redirect(route('demandLetters.index'));
    }

    /**
     * Remove the specified DemandLetters from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $demandLetters = $this->demandLettersRepository->find($id);

        if (empty($demandLetters)) {
            Flash::error('Demand Letters not found');

            return redirect(route('demandLetters.index'));
        }

        $this->demandLettersRepository->delete($id);

        Flash::success('Demand Letters deleted successfully.');

        return redirect(route('demandLetters.index'));
    }

    public function perAccount($accountNo, $asOf) {
        if ($accountNo != '0' && $asOf != '0') {
            $serviceAccounts = DB::table('Billing_ServiceAccounts')
            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
            ->leftJoin('users', 'Billing_ServiceAccounts.MeterReader', '=', 'users.id')
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
                    'Billing_ServiceAccounts.ContactNumber',
                    'Billing_ServiceAccounts.Organization',
                    'Billing_ServiceAccounts.OrganizationParentAccount',
                    'Billing_ServiceAccounts.Main',
                    'Billing_ServiceAccounts.GroupCode',
                    'Billing_ServiceAccounts.Multiplier',
                    'Billing_ServiceAccounts.Coreloss',
                    'Billing_ServiceAccounts.ConnectionDate',
                    'Billing_ServiceAccounts.ServiceConnectionId',
                    'Billing_ServiceAccounts.SeniorCitizen',
                    'Billing_ServiceAccounts.Evat5Percent',
                    'Billing_ServiceAccounts.Ewt2Percent',
                    'Billing_ServiceAccounts.Contestable',
                    'Billing_ServiceAccounts.NetMetered',
                    'Billing_ServiceAccounts.Latitude',
                    'Billing_ServiceAccounts.Longitude',
                    'Billing_ServiceAccounts.Item1 AS CoopConsumption',
                    'Billing_ServiceAccounts.AccountRetention',
                    'Billing_ServiceAccounts.DurationInMonths',
                    'Billing_ServiceAccounts.AccountExpiration',
                    'CRM_Towns.Town',
                    'CRM_Barangays.Barangay',
                    'DownloadedByDisco',
                    'users.name as MeterReader')
            ->where('Billing_ServiceAccounts.id', $accountNo)
            ->first();

            $bills = DB::table('Billing_Bills')
                ->whereRaw("AccountNumber='" . $accountNo . "' AND ServicePeriod <= '" . $asOf . "' AND
                    AccountNumber NOT IN (SELECT AccountNumber FROM Cashier_PaidBills WHERE AccountNumber=Billing_Bills.AccountNumber AND ServicePeriod=Billing_Bills.ServicePeriod AND (Status IS NULL OR Status='Application'))")
                ->select('Billing_Bills.*')
                ->orderByDesc('ServicePeriod')
                ->get();
        } else {
            $serviceAccounts = null;
            $bills = [];
        }

        return view('/demand_letters/per_account', [
            'serviceAccounts' => $serviceAccounts,
            'bills' => $bills,
            'accountNo' => $accountNo,
        ]);
    }

    public function printPerAccount($accountNo, $asOf) {
        if ($accountNo != '0' && $asOf != '0') {
            $serviceAccounts = DB::table('Billing_ServiceAccounts')
            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
            ->leftJoin('users', 'Billing_ServiceAccounts.MeterReader', '=', 'users.id')
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
                    'Billing_ServiceAccounts.ContactNumber',
                    'Billing_ServiceAccounts.Organization',
                    'Billing_ServiceAccounts.OrganizationParentAccount',
                    'Billing_ServiceAccounts.Main',
                    'Billing_ServiceAccounts.GroupCode',
                    'Billing_ServiceAccounts.Multiplier',
                    'Billing_ServiceAccounts.Coreloss',
                    'Billing_ServiceAccounts.ConnectionDate',
                    'Billing_ServiceAccounts.ServiceConnectionId',
                    'Billing_ServiceAccounts.SeniorCitizen',
                    'Billing_ServiceAccounts.Evat5Percent',
                    'Billing_ServiceAccounts.Ewt2Percent',
                    'Billing_ServiceAccounts.Contestable',
                    'Billing_ServiceAccounts.NetMetered',
                    'Billing_ServiceAccounts.Latitude',
                    'Billing_ServiceAccounts.Longitude',
                    'Billing_ServiceAccounts.Item1 AS CoopConsumption',
                    'Billing_ServiceAccounts.AccountRetention',
                    'Billing_ServiceAccounts.DurationInMonths',
                    'Billing_ServiceAccounts.AccountExpiration',
                    'CRM_Towns.Town',
                    'CRM_Barangays.Barangay',
                    'DownloadedByDisco',
                    'users.name as MeterReader')
            ->where('Billing_ServiceAccounts.id', $accountNo)
            ->first();

            $bills = DB::table('Billing_Bills')
                ->whereRaw("AccountNumber='" . $accountNo . "' AND ServicePeriod <= '" . $asOf . "' AND
                    AccountNumber NOT IN (SELECT AccountNumber FROM Cashier_PaidBills WHERE AccountNumber=Billing_Bills.AccountNumber AND ServicePeriod=Billing_Bills.ServicePeriod AND (Status IS NULL OR Status='Application'))")
                ->select('Billing_Bills.*')
                ->orderByDesc('ServicePeriod')
                ->get();
        } else {
            $serviceAccounts = null;
            $bills = [];
        }

        return view('/demand_letters/print_per_account', [
            'serviceAccounts' => $serviceAccounts,
            'bills' => $bills,
            'accountNo' => $accountNo,
        ]);
    }

    public function searchAccountForDemandLetter(Request $request) {
        $search = $request['Search'];

        $serviceAccounts = DB::table('Billing_ServiceAccounts')
            ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
            ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
            ->select('Billing_ServiceAccounts.id',
                    'Billing_ServiceAccounts.ServiceAccountName',
                    'Billing_ServiceAccounts.Purok',
                    'CRM_Towns.Town',
                    'CRM_Barangays.Barangay',
                    'Billing_ServiceAccounts.OldAccountNo',)
            ->whereRaw("ServiceAccountName LIKE '%" . $search . "%' OR OldAccountNo LIKE '%" . $search . "%' OR Billing_ServiceAccounts.id LIKE '%" . $search . "%'")
            ->get();

        $output = "";
        foreach ($serviceAccounts as $item) {
            $output .= "<tr>" .
                        "<td>" . $item->OldAccountNo . "</td>" .
                        "<td>" . $item->ServiceAccountName . "</td>" .
                        "<td class='text-right'>
                            <button onclick=go('" . $item->id . "') class='btn btn-xs btn-success'>Go</button>
                        </td>" .
                     "</tr>";
        }

        return response()->json($output, 200);
    }

    public function perRoute($route, $asOf, $town) {
        if ($route != 0 && $asOf != 0) {
            $data = DB::table('Billing_Bills')
                ->leftJoin('Billing_ServiceAccounts', 'Billing_Bills.AccountNumber', '=', 'Billing_ServiceAccounts.id')
                ->whereRaw("ServicePeriod <= '" . $asOf . "' AND Billing_ServiceAccounts.Town='" . $town . "' AND Billing_ServiceAccounts.AreaCode='" . $route . "' AND AccountStatus IN ('DISCONNECTED') AND
                AccountNumber NOT IN (SELECT AccountNumber FROM Cashier_PaidBills WHERE AccountNumber=Billing_Bills.AccountNumber AND ServicePeriod=Billing_Bills.ServicePeriod AND (Status IS NULL OR Status='Application'))")
                ->select(
                    'Billing_ServiceAccounts.OldAccountNo', 
                    'Billing_ServiceAccounts.id', 
                    'Billing_ServiceAccounts.ServiceAccountName', 
                    'Billing_ServiceAccounts.AreaCode',
                    DB::raw("SUM(TRY_CAST(Billing_Bills.NetAmount AS DECIMAL(15,2))) AS TotalAmount"),
                    DB::raw("COUNT(Billing_Bills.id) AS BillingMonths"),
                )
                ->groupBy('Billing_ServiceAccounts.OldAccountNo', 'Billing_ServiceAccounts.id', 'Billing_ServiceAccounts.ServiceAccountName', 'Billing_ServiceAccounts.AreaCode')
                ->orderBy('Billing_ServiceAccounts.OldAccountNo')
                ->get();
        } else {
            $data = [];
        }

        return view('/demand_letters/per_route', [
            'data' => $data,
            'route' => $route,
            'asOf' => $asOf,
            'towns' => Towns::orderBy('Town')->get(),
        ]);
    }

    public function searchRoute(Request $request) {
        $search = $request['Search'];
        $town = $request['Town'];

        $serviceAccounts = DB::table('Billing_ServiceAccounts')
            ->select('Billing_ServiceAccounts.AreaCode',
                    DB::raw('COUNT(id) AS ConsumersCount')
            )
            ->whereRaw("(AreaCode LIKE '%" . $search . "%') AND Town='" . $town . "'")
            ->groupBy('AreaCode')
            ->get();

        $output = "";
        foreach ($serviceAccounts as $item) {
            $output .= "<tr>" .
                        "<td>" . $item->AreaCode . "</td>" .
                        "<td>" . $item->ConsumersCount . "</td>" .
                        "<td class='text-right'>
                            <button onclick=go('" . $item->AreaCode . "') class='btn btn-xs btn-success'>Go</button>
                        </td>" .
                     "</tr>";
        }

        return response()->json($output, 200);
    }

    public function printPerRoute($route, $asOf, $town) {
        if ($route != null && $asOf != null) {
            $data = DB::table('Billing_Bills')
                ->leftJoin('Billing_ServiceAccounts', 'Billing_Bills.AccountNumber', '=', 'Billing_ServiceAccounts.id')
                ->leftJoin('CRM_Towns', 'Billing_ServiceAccounts.Town', '=', 'CRM_Towns.id')
                ->leftJoin('CRM_Barangays', 'Billing_ServiceAccounts.Barangay', '=', 'CRM_Barangays.id')
                ->whereRaw("ServicePeriod <= '" . $asOf . "' AND Billing_ServiceAccounts.Town='" . $town . "' AND Billing_ServiceAccounts.AreaCode='" . $route . "' AND AccountStatus IN ('DISCONNECTED')  AND 
                    AccountNumber NOT IN (SELECT AccountNumber FROM Cashier_PaidBills WHERE AccountNumber=Billing_Bills.AccountNumber AND ServicePeriod=Billing_Bills.ServicePeriod AND (Status IS NULL OR Status='Application'))")
                ->select(
                    'Billing_ServiceAccounts.OldAccountNo', 
                    'Billing_ServiceAccounts.id', 
                    'Billing_ServiceAccounts.ServiceAccountName', 
                    'Billing_ServiceAccounts.AreaCode',
                    'Billing_ServiceAccounts.Purok',
                    'CRM_Towns.Town',
                    'CRM_Barangays.Barangay'
                )
                ->groupBy(
                    'Billing_ServiceAccounts.OldAccountNo', 
                    'Billing_ServiceAccounts.id', 
                    'Billing_ServiceAccounts.ServiceAccountName', 
                    'Billing_ServiceAccounts.AreaCode',
                    'Billing_ServiceAccounts.Purok',
                    'CRM_Towns.Town',
                    'CRM_Barangays.Barangay'
                )
                ->orderBy('Billing_ServiceAccounts.OldAccountNo')
                ->get();
        } else {
            $data = [];
        }

        return view('/demand_letters/print_per_route', [
            'data' => $data,
            'route' => $route,
            'town' => $town,
            'asOf' => $asOf,
        ]);
    }
}
