<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBillOfMaterialsMatrixRequest;
use App\Http\Requests\UpdateBillOfMaterialsMatrixRequest;
use App\Repositories\BillOfMaterialsMatrixRepository;
use App\Http\Controllers\AppBaseController;
use App\Models\ServiceConnections;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\BillOfMaterialsMatrix;
use App\Models\MaterialsMatrix;
use App\Exports\BillOfMaterialsExport;
use App\Models\TransformersAssignedMatrix;
use App\Models\StructureAssignments;
use App\Models\Structures;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\IDGenerator;
use Flash;
use Response;

class BillOfMaterialsMatrixController extends AppBaseController
{
    /** @var  BillOfMaterialsMatrixRepository */
    private $billOfMaterialsMatrixRepository;

    public function __construct(BillOfMaterialsMatrixRepository $billOfMaterialsMatrixRepo)
    {
        $this->middleware('auth');
        $this->billOfMaterialsMatrixRepository = $billOfMaterialsMatrixRepo;
    }

    /**
     * Display a listing of the BillOfMaterialsMatrix.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $billOfMaterialsMatrices = $this->billOfMaterialsMatrixRepository->all();

        return view('bill_of_materials_matrices.index')
            ->with('billOfMaterialsMatrices', $billOfMaterialsMatrices);
    }

    /**
     * Show the form for creating a new BillOfMaterialsMatrix.
     *
     * @return Response
     */
    public function create()
    {
        return view('bill_of_materials_matrices.create');
    }

    /**
     * Store a newly created BillOfMaterialsMatrix in storage.
     *
     * @param CreateBillOfMaterialsMatrixRequest $request
     *
     * @return Response
     */
    public function store(CreateBillOfMaterialsMatrixRequest $request)
    {
        $input = $request->all();

        $billOfMaterialsMatrix = $this->billOfMaterialsMatrixRepository->create($input);

        Flash::success('Bill Of Materials Matrix saved successfully.');

        return redirect(route('billOfMaterialsMatrices.index'));
    }

    /**
     * Display the specified BillOfMaterialsMatrix.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $billOfMaterialsMatrix = $this->billOfMaterialsMatrixRepository->find($id);

        if (empty($billOfMaterialsMatrix)) {
            Flash::error('Bill Of Materials Matrix not found');

            return redirect(route('billOfMaterialsMatrices.index'));
        }

        return view('bill_of_materials_matrices.show')->with('billOfMaterialsMatrix', $billOfMaterialsMatrix);
    }

    /**
     * Show the form for editing the specified BillOfMaterialsMatrix.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $billOfMaterialsMatrix = $this->billOfMaterialsMatrixRepository->find($id);

        if (empty($billOfMaterialsMatrix)) {
            Flash::error('Bill Of Materials Matrix not found');

            return redirect(route('billOfMaterialsMatrices.index'));
        }

        return view('bill_of_materials_matrices.edit')->with('billOfMaterialsMatrix', $billOfMaterialsMatrix);
    }

    /**
     * Update the specified BillOfMaterialsMatrix in storage.
     *
     * @param int $id
     * @param UpdateBillOfMaterialsMatrixRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBillOfMaterialsMatrixRequest $request)
    {
        $billOfMaterialsMatrix = $this->billOfMaterialsMatrixRepository->find($id);

        if (empty($billOfMaterialsMatrix)) {
            Flash::error('Bill Of Materials Matrix not found');

            return redirect(route('billOfMaterialsMatrices.index'));
        }

        $billOfMaterialsMatrix = $this->billOfMaterialsMatrixRepository->update($request->all(), $id);

        Flash::success('Bill Of Materials Matrix updated successfully.');

        return redirect(route('billOfMaterialsMatrices.index'));
    }

    /**
     * Remove the specified BillOfMaterialsMatrix from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $billOfMaterialsMatrix = $this->billOfMaterialsMatrixRepository->find($id);

        if (empty($billOfMaterialsMatrix)) {
            Flash::error('Bill Of Materials Matrix not found');

            return redirect(route('billOfMaterialsMatrices.index'));
        }

        $this->billOfMaterialsMatrixRepository->delete($id);

        Flash::success('Bill Of Materials Matrix deleted successfully.');

        return redirect(route('billOfMaterialsMatrices.index'));
    }

    public function view($scId) {
        $serviceConnection = DB::table('CRM_ServiceConnections')
            ->join('CRM_Barangays', 'CRM_ServiceConnections.Barangay', '=', 'CRM_Barangays.id')                    
            ->join('CRM_Towns', 'CRM_ServiceConnections.Town', '=', 'CRM_Towns.id')
            ->join('CRM_ServiceConnectionAccountTypes', 'CRM_ServiceConnections.AccountType', '=', 'CRM_ServiceConnectionAccountTypes.id')
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
            ->leftJoin('CRM_Structures', 'CRM_BillOfMaterialsMatrix.StructureId', '=', 'CRM_Structures.id')    
            ->select('CRM_MaterialAssets.id',
                    'CRM_MaterialAssets.Description',
                    'CRM_MaterialAssets.Amount',
                    DB::raw('SUM(CAST(CRM_BillOfMaterialsMatrix.Quantity AS Integer)) AS ProjectRequirements'),
                    DB::raw('(CAST(CRM_MaterialAssets.Amount As Money) * SUM(CAST(CRM_BillOfMaterialsMatrix.Quantity AS Integer))) AS ExtendedCost'))
            ->whereIn('CRM_Structures.Data', function($query)  use ($scId) {
                $query->select('StructureId')
                    ->from('CRM_StructureAssignments')
                    ->where('ServiceConnectionId', $scId);
            })
            ->groupBy('CRM_MaterialAssets.Description', 'CRM_MaterialAssets.Amount', 'CRM_MaterialAssets.id')
            ->orderBy('CRM_MaterialAssets.Description')
            ->get();   

        return view('/bill_of_materials_matrices/view', ['serviceConnection' => $serviceConnection, 'billOfMaterials' => $billOfMaterials]);
    }

    public function downloadBillOfMaterials($scId) {
        $data = DB::table('CRM_BillOfMaterialsMatrix')
            ->leftJoin('CRM_MaterialAssets', 'CRM_BillOfMaterialsMatrix.MaterialsId', '=', 'CRM_MaterialAssets.id')
            ->leftJoin('CRM_Structures', 'CRM_BillOfMaterialsMatrix.StructureId', '=', 'CRM_Structures.id')    
            ->select('CRM_MaterialAssets.id',
                    'CRM_MaterialAssets.Description',
                    'CRM_MaterialAssets.Amount',
                    DB::raw('SUM(CAST(CRM_BillOfMaterialsMatrix.Quantity AS Integer)) AS ProjectRequirements'),
                    DB::raw('(CAST(CRM_MaterialAssets.Amount As Money) * SUM(CAST(CRM_BillOfMaterialsMatrix.Quantity AS Integer))) AS ExtendedCost'))
            ->where('CRM_BillOfMaterialsMatrix.ServiceConnectionId', $scId)
            ->groupBy('CRM_MaterialAssets.Description', 'CRM_MaterialAssets.Amount', 'CRM_MaterialAssets.id')
            ->orderBy('CRM_MaterialAssets.Description')
            ->get();  
            
            $export = new BillOfMaterialsExport($data->toArray());

            // dd($data);
        return Excel::download($export, 'BillOfMaterials.xlsx');
    }

    public function getBillOfMaterialsJson(Request $request) {
        if ($request->ajax()) {
            $scId = $request['scId'];
            // $billOfMaterials = DB::table('CRM_BillOfMaterialsMatrix')
            //     ->leftJoin('CRM_MaterialAssets', 'CRM_BillOfMaterialsMatrix.MaterialsId', '=', 'CRM_MaterialAssets.id')
            //     ->leftJoin('CRM_Structures', 'CRM_BillOfMaterialsMatrix.StructureId', '=', 'CRM_Structures.id')    
            //     ->select('CRM_MaterialAssets.id',
            //             'CRM_MaterialAssets.Description',
            //             'CRM_MaterialAssets.Amount',
            //             DB::raw('SUM(CAST(CRM_BillOfMaterialsMatrix.Quantity AS Integer)) AS ProjectRequirements'),
            //             DB::raw('(CAST(CRM_MaterialAssets.Amount As Money) * SUM(CAST(CRM_BillOfMaterialsMatrix.Quantity AS Integer))) AS ExtendedCost'))
            //     ->whereIn('CRM_Structures.Data', function($query)  use ($scId) {
            //         $query->select('StructureId')
            //             ->from('CRM_StructureAssignments')
            //             ->where('ServiceConnectionId', $scId);
            //     })
            //     ->groupBy('CRM_MaterialAssets.Description', 'CRM_MaterialAssets.Amount', 'CRM_MaterialAssets.id')
            //     ->orderBy('CRM_MaterialAssets.Description')
            //     ->get();   

            $billOfMaterials = DB::table('CRM_BillOfMaterialsMatrix')
                ->leftJoin('CRM_MaterialAssets', 'CRM_BillOfMaterialsMatrix.MaterialsId', '=', 'CRM_MaterialAssets.id')
                ->leftJoin('CRM_Structures', 'CRM_BillOfMaterialsMatrix.StructureId', '=', 'CRM_Structures.id')    
                ->select('CRM_MaterialAssets.id',
                        'CRM_MaterialAssets.Description',
                        'CRM_MaterialAssets.Amount',
                        DB::raw('SUM(CAST(CRM_BillOfMaterialsMatrix.Quantity AS Integer)) AS ProjectRequirements'),
                        DB::raw('(CAST(CRM_MaterialAssets.Amount As Money) * SUM(CAST(CRM_BillOfMaterialsMatrix.Quantity AS Integer))) AS ExtendedCost'))
                ->where('CRM_BillOfMaterialsMatrix.ServiceConnectionId', $scId)
                ->whereNull('CRM_BillOfMaterialsMatrix.StructureType')
                ->groupBy('CRM_MaterialAssets.Description', 'CRM_MaterialAssets.Amount', 'CRM_MaterialAssets.id')
                ->orderBy('CRM_MaterialAssets.Description')
                ->get();  

            echo json_encode($billOfMaterials);
        }        
    }

    public function getBillOfMaterialsBrackets(Request $request) {
        if ($request->ajax()) {
            $scId = $request['scId'];
            $billOfMaterials = DB::table('CRM_BillOfMaterialsMatrix')
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

            echo json_encode($billOfMaterials);
        }        
    }

    public function insertTransformerBracket(Request $request) {
        if ($request->ajax()) {
            // QUERY TOTAL QUANTITY OF TRANSFORMER ASSIGNED
            $transformers = TransformersAssignedMatrix::where('ServiceConnectionId', $request['ServiceConnectionId'])
                                ->where('Type', 'Transformer')
                                ->get();
            $totalTrans = $request['Quantity'];

            // SAVE TO STRUCTURE ASSIGNMENTS FIRST
            $structureCore = Structures::find($request['StructureId']);

            $structure  = new StructureAssignments;
            $structure->id = IDGenerator::generateID();
            $structure->ServiceConnectionId = $request['ServiceConnectionId'];
            $structure->StructureId = $structureCore->Data;
            $structure->Quantity = $totalTrans;
            $structure->Type = 'A_DT'; // TRANSFORMER 
            $structure->save();

            // QUERY MATERIALS INSIDE STRUCTURE
            $materials = MaterialsMatrix::where('StructureId', $request['StructureId'])->get();
            if ($materials != null) {
                foreach ($materials as $item) {
                    // INSERT TO BillOfMaterialsMatrix
                    $bracket = new BillOfMaterialsMatrix;
                    $bracket->id = IDGenerator::generateID();
                    $bracket->ServiceConnectionId = $request['ServiceConnectionId'];
                    $bracket->StructureAssigningId = $structure->id;
                    $bracket->StructureId = $request['StructureId'];
                    $bracket->MaterialsId = $item->MaterialsId;
                    $bracket->Quantity = ($totalTrans * intval($item->Quantity));
                    $bracket->StructureType = 'A_DT'; // BRACKET TYPE
                    $bracket->save();
                }
            }
            
            return json_encode(['response' => 'ok']);
        }
    }

    public function insertPole(Request $request) {
        if ($request->ajax()) {
            $pole = new BillOfMaterialsMatrix;
            $pole->id = IDGenerator::generateID();
            $pole->ServiceConnectionId = $request['ServiceConnectionId'];
            $pole->MaterialsId = $request['MaterialsId'];
            $pole->Quantity = $request['Quantity'];
            $pole->StructureType = 'POLE'; // FOR POLES
            $pole->save();

            return json_encode(['response' => 'ok']);
        }
    }

    public function fetchPoles(Request $request) {
        if ($request->ajax()) {
            $poleAssigned = DB::table('CRM_BillOfMaterialsMatrix')
                ->leftJoin('CRM_MaterialAssets', 'CRM_BillOfMaterialsMatrix.MaterialsId', '=', 'CRM_MaterialAssets.id')  
                ->select('CRM_BillOfMaterialsMatrix.id',
                        'CRM_MaterialAssets.Description',
                        'CRM_MaterialAssets.Amount',
                        'CRM_BillOfMaterialsMatrix.Quantity')
                ->where('CRM_BillOfMaterialsMatrix.ServiceConnectionId', $request['ServiceConnectionId'])
                ->where('CRM_BillOfMaterialsMatrix.StructureType', 'POLE')
                ->orderBy('CRM_MaterialAssets.Description')
                ->get(); 

            return json_encode($poleAssigned);
        }
    }

    public function deletePole(Request $request) {
        if ($request->ajax()) {
            BillOfMaterialsMatrix::find($request['id'])->delete(); 

            return json_encode(['response' => 'deleted']);
        }
    }
}
