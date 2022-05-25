<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBAPAAdjustmentsRequest;
use App\Http\Requests\UpdateBAPAAdjustmentsRequest;
use App\Repositories\BAPAAdjustmentsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use App\Models\Towns;
use App\Models\BAPAAdjustments;
use App\Models\BAPAAdjustmentDetails;
use App\Models\IDGenerator;
use App\Models\Bills;
use Illuminate\Support\Facades\DB;   
use Illuminate\Support\Facades\Auth; 
use Flash;
use Response;

class BAPAAdjustmentsController extends AppBaseController
{
    /** @var  BAPAAdjustmentsRepository */
    private $bAPAAdjustmentsRepository;

    public function __construct(BAPAAdjustmentsRepository $bAPAAdjustmentsRepo)
    {
        $this->middleware('auth');
        $this->bAPAAdjustmentsRepository = $bAPAAdjustmentsRepo;
    }

    /**
     * Display a listing of the BAPAAdjustments.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $towns = Towns::orderBy('id')->get();

        return view('b_a_p_a_adjustments.index',[
            'towns' => $towns,
        ]);
    }

    /**
     * Show the form for creating a new BAPAAdjustments.
     *
     * @return Response
     */
    public function create()
    {
        return view('b_a_p_a_adjustments.create');
    }

    /**
     * Store a newly created BAPAAdjustments in storage.
     *
     * @param CreateBAPAAdjustmentsRequest $request
     *
     * @return Response
     */
    public function store(CreateBAPAAdjustmentsRequest $request)
    {
        $input = $request->all();

        $bAPAAdjustments = $this->bAPAAdjustmentsRepository->create($input);

        Flash::success('B A P A Adjustments saved successfully.');

        return redirect(route('bAPAAdjustments.index'));
    }

    /**
     * Display the specified BAPAAdjustments.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $bAPAAdjustments = $this->bAPAAdjustmentsRepository->find($id);

        if (empty($bAPAAdjustments)) {
            Flash::error('B A P A Adjustments not found');

            return redirect(route('bAPAAdjustments.index'));
        }

        return view('b_a_p_a_adjustments.show')->with('bAPAAdjustments', $bAPAAdjustments);
    }

    /**
     * Show the form for editing the specified BAPAAdjustments.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $bAPAAdjustments = $this->bAPAAdjustmentsRepository->find($id);

        if (empty($bAPAAdjustments)) {
            Flash::error('B A P A Adjustments not found');

            return redirect(route('bAPAAdjustments.index'));
        }

        return view('b_a_p_a_adjustments.edit')->with('bAPAAdjustments', $bAPAAdjustments);
    }

    /**
     * Update the specified BAPAAdjustments in storage.
     *
     * @param int $id
     * @param UpdateBAPAAdjustmentsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBAPAAdjustmentsRequest $request)
    {
        $bAPAAdjustments = $this->bAPAAdjustmentsRepository->find($id);

        if (empty($bAPAAdjustments)) {
            Flash::error('B A P A Adjustments not found');

            return redirect(route('bAPAAdjustments.index'));
        }

        $bAPAAdjustments = $this->bAPAAdjustmentsRepository->update($request->all(), $id);

        Flash::success('B A P A Adjustments updated successfully.');

        return redirect(route('bAPAAdjustments.index'));
    }

    /**
     * Remove the specified BAPAAdjustments from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $bAPAAdjustments = $this->bAPAAdjustmentsRepository->find($id);

        if (empty($bAPAAdjustments)) {
            Flash::error('B A P A Adjustments not found');

            return redirect(route('bAPAAdjustments.index'));
        }

        $this->bAPAAdjustmentsRepository->delete($id);

        Flash::success('B A P A Adjustments deleted successfully.');

        return redirect(route('bAPAAdjustments.index'));
    }

    public function searchBapa(Request $request) {
        $param = $request['BAPA'];
        $town = $request['Town'];

        if ($town == 'All') {
            $bapas = DB::table('Billing_ServiceAccounts AS sa')
            ->where('sa.OrganizationParentAccount', 'LIKE', '%' . $param . '%')
            ->select('sa.OrganizationParentAccount', 
                'sa.Town',
                DB::raw("COUNT(sa.id) AS NoOfAccounts"),
                DB::raw("(SELECT SUBSTRING((SELECT ',' + AreaCode AS 'data()' FROM Billing_ServiceAccounts WHERE OrganizationParentAccount=sa.OrganizationParentAccount GROUP BY AreaCode FOR XML PATH('')), 2 , 9999)) As Result"))
            ->groupBy('sa.OrganizationParentAccount', 
                'sa.Town')
            ->orderBy('sa.OrganizationParentAccount')
            ->get();
        } else {
            $bapas = DB::table('Billing_ServiceAccounts AS sa')
            ->where('sa.OrganizationParentAccount', 'LIKE', '%' . $param . '%')
            ->where('sa.Town', $town)
            ->select('sa.OrganizationParentAccount', 
                'sa.Town',
                DB::raw("COUNT(sa.id) AS NoOfAccounts"),
                DB::raw("(SELECT SUBSTRING((SELECT ',' + AreaCode AS 'data()' FROM Billing_ServiceAccounts WHERE OrganizationParentAccount=sa.OrganizationParentAccount GROUP BY AreaCode FOR XML PATH('')), 2 , 9999)) As Result"))
            ->groupBy('sa.OrganizationParentAccount', 
                'sa.Town')
            ->orderBy('sa.OrganizationParentAccount')
            ->get();
        }

        $output = "";
        foreach($bapas as $item) {
            if (strlen($item->OrganizationParentAccount) > 1) {
                $output .= '<tr>
                                <td><a href="' . route('bAPAAdjustments.adjust-bapa', [urlencode($item->OrganizationParentAccount)]) . '">' . $item->OrganizationParentAccount . '</a></td>
                                <td>' . $item->Town . '</td>
                                <td>' . number_format($item->NoOfAccounts) . '</td>
                                <td>' . $item->Result . '</td>
                            </tr>';
            }            
        }

        return response()->json($output, 200);
    }

    public function adjustBapaPayments($bapaName) {
        $bapaName = urldecode($bapaName);

        return view('/b_a_p_a_adjustments/adjust_payment', [
            'bapaName' => $bapaName,
        ]);
    }

    public function saveBapaAdjustments(Request $request) {
        $bapaName = urldecode($request['BAPAName']);
        $percentage = floatval($request['DiscountPercentage']);
        $period = $request['Period'];
        // FETCH FIRST IF EXISTS IN BAPA ADJUSTMENTS
        $bAPAAdjustments = BAPAAdjustments::where('ServicePeriod', $request['Period'])
            ->where('BAPAName', $bapaName)
            ->first();
        
        if ($bAPAAdjustments != null) {
            // UPDATE
            $bAPAAdjustments->BAPAName = $bapaName;
            $bAPAAdjustments->ServicePeriod = $request['Period'];
            $bAPAAdjustments->DiscountPercentage = $request['DiscountPercentage'];
            $bAPAAdjustments->DiscountAmount = round($request['DiscountAmount'], 2);
            $bAPAAdjustments->NumberOfConsumers = $request['NoOfConsumers'];
            $bAPAAdjustments->SubTotal = round($request['SubTotal'], 2);
            $bAPAAdjustments->NetAmount = round($request['NetAmount'], 2);
            $bAPAAdjustments->UserId = Auth::id();
            $bAPAAdjustments->save();
        } else {
            // SAVE NEW
            $bAPAAdjustments = new BAPAAdjustments;
            $bAPAAdjustments->id = IDGenerator::generateIDandRandString();
            $bAPAAdjustments->BAPAName = $bapaName;
            $bAPAAdjustments->ServicePeriod = $request['Period'];
            $bAPAAdjustments->DiscountPercentage = $request['DiscountPercentage'];
            $bAPAAdjustments->DiscountAmount = round($request['DiscountAmount'], 2);
            $bAPAAdjustments->NumberOfConsumers = $request['NoOfConsumers'];
            $bAPAAdjustments->SubTotal = round($request['SubTotal'], 2);
            $bAPAAdjustments->NetAmount = round($request['NetAmount'], 2);
            $bAPAAdjustments->UserId = Auth::id();
            $bAPAAdjustments->save();
        }

        // SAVE EVERY BILL
        $billNumbers = $request['BillNumbers'];
        $len = count($billNumbers);

        for($i=0; $i<$len; $i++) {
            $bill = Bills::find($billNumbers[$i]);

            if ($bill != null) {
                // CHECK IF EXISTS IN ADJUSTMENT DETAILS
                $adjusted = BAPAAdjustmentDetails::where('BillId', $bill->id)->first();
                $discountAmnt = round(floatval($bill->NetAmount) * $percentage, 2);

                if ($adjusted != null) {
                    // UPDATE
                    $adjusted->AccountNumber = $bill->AccountNumber;
                    $adjusted->BillId = $bill->id;
                    $adjusted->DiscountPercentage = $percentage;
                    $adjusted->DiscountAmount = $discountAmnt;
                    $adjusted->BAPAName = $bapaName;
                    $adjusted->ServicePeriod = $period;
                    $adjusted->save();
                } else {
                    // SAVE NEW
                    $adjusted = new BAPAAdjustmentDetails;
                    $adjusted->id = IDGenerator::generateIDandRandString();
                    $adjusted->AccountNumber = $bill->AccountNumber;
                    $adjusted->BillId = $bill->id;
                    $adjusted->DiscountPercentage = $percentage;
                    $adjusted->DiscountAmount = $discountAmnt;
                    $adjusted->BAPAName = $bapaName;
                    $adjusted->ServicePeriod = $period;
                    $adjusted->save();
                }
            } 
        }

        return response()->json('ok', 200);
    }
}
