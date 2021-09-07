<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateServiceConnectionChecklistsRequest;
use App\Http\Requests\UpdateServiceConnectionChecklistsRequest;
use App\Repositories\ServiceConnectionChecklistsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use App\Models\ServiceConnectionChecklists;
use App\Models\ServiceConnectionChecklistsRep;
use App\Models\IDGenerator;
use App\Models\ServiceConnectionTimeframes;
use Illuminate\Support\Facades\Auth;
use Flash;
use Response;

class ServiceConnectionChecklistsController extends AppBaseController
{
    /** @var  ServiceConnectionChecklistsRepository */
    private $serviceConnectionChecklistsRepository;

    public function __construct(ServiceConnectionChecklistsRepository $serviceConnectionChecklistsRepo)
    {
        $this->middleware('auth');
        $this->serviceConnectionChecklistsRepository = $serviceConnectionChecklistsRepo;
    }

    /**
     * Display a listing of the ServiceConnectionChecklists.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $serviceConnectionChecklists = $this->serviceConnectionChecklistsRepository->all();

        return view('service_connection_checklists.index')
            ->with('serviceConnectionChecklists', $serviceConnectionChecklists);
    }

    /**
     * Show the form for creating a new ServiceConnectionChecklists.
     *
     * @return Response
     */
    public function create()
    {
        return view('service_connection_checklists.create');
    }

    /**
     * Store a newly created ServiceConnectionChecklists in storage.
     *
     * @param CreateServiceConnectionChecklistsRequest $request
     *
     * @return Response
     */
    public function store(CreateServiceConnectionChecklistsRequest $request)
    {
        $input = $request->all();

        $serviceConnectionChecklists = $this->serviceConnectionChecklistsRepository->create($input);

        Flash::success('Service Connection Checklists saved successfully.');

        return redirect(route('serviceConnectionChecklists.index'));
    }

    /**
     * Display the specified ServiceConnectionChecklists.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $serviceConnectionChecklists = $this->serviceConnectionChecklistsRepository->find($id);

        if (empty($serviceConnectionChecklists)) {
            Flash::error('Service Connection Checklists not found');

            return redirect(route('serviceConnectionChecklists.index'));
        }

        return view('service_connection_checklists.show')->with('serviceConnectionChecklists', $serviceConnectionChecklists);
    }

    /**
     * Show the form for editing the specified ServiceConnectionChecklists.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $serviceConnectionChecklists = $this->serviceConnectionChecklistsRepository->find($id);

        if (empty($serviceConnectionChecklists)) {
            Flash::error('Service Connection Checklists not found');

            return redirect(route('serviceConnectionChecklists.index'));
        }

        return view('service_connection_checklists.edit')->with('serviceConnectionChecklists', $serviceConnectionChecklists);
    }

    /**
     * Update the specified ServiceConnectionChecklists in storage.
     *
     * @param int $id
     * @param UpdateServiceConnectionChecklistsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateServiceConnectionChecklistsRequest $request)
    {
        $serviceConnectionChecklists = $this->serviceConnectionChecklistsRepository->find($id);

        if (empty($serviceConnectionChecklists)) {
            Flash::error('Service Connection Checklists not found');

            return redirect(route('serviceConnectionChecklists.index'));
        }

        $serviceConnectionChecklists = $this->serviceConnectionChecklistsRepository->update($request->all(), $id);

        Flash::success('Service Connection Checklists updated successfully.');

        return redirect(route('serviceConnectionChecklists.index'));
    }

    /**
     * Remove the specified ServiceConnectionChecklists from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $serviceConnectionChecklists = $this->serviceConnectionChecklistsRepository->find($id);

        if (empty($serviceConnectionChecklists)) {
            Flash::error('Service Connection Checklists not found');

            return redirect(route('serviceConnectionChecklists.index'));
        }

        $this->serviceConnectionChecklistsRepository->delete($id);

        Flash::success('Service Connection Checklists deleted successfully.');

        return redirect(route('serviceConnectionChecklists.index'));
    }

    public function complyChecklists($id, Request $request) {
        $inputs = $request->input('ChecklistId');

        $checklistsRep = ServiceConnectionChecklistsRep::all();

        ServiceConnectionChecklists::where('ServiceConnectionId', $id)->delete();

        $reqSubmitted = "";

        foreach($inputs as $input){
            $scChecklists = new ServiceConnectionChecklists;
            $scChecklists->id = IDGenerator::generateID();
            $scChecklists->ServiceConnectionId = $id;
            $scChecklists->ChecklistId = $input;

            $scChecklists->save();

            $reqSubmitted .= ServiceConnectionChecklistsRep::find($input)->Checklist . ', ';
        }

        if (count($inputs) == count($checklistsRep)) {
            // IF REQUIREMENTS ARE COMPLETE

            // CREATE Timeframes
            $timeFrame = new ServiceConnectionTimeframes;
            $timeFrame->id = IDGenerator::generateID();
            $timeFrame->ServiceConnectionId = $id;
            $timeFrame->UserId = Auth::id();
            $timeFrame->Status = 'Requirements Completed';
            $timeFrame->save();

            return redirect(route('serviceConnectionInspections.create-step-two', [$id]));
        } else {
            // IF REQUIREMENTS AIN'T COMPLETE
            
            // CREATE Timeframes
            $timeFrame = new ServiceConnectionTimeframes;
            $timeFrame->id = IDGenerator::generateID();
            $timeFrame->ServiceConnectionId = $id;
            $timeFrame->UserId = Auth::id();
            $timeFrame->Status = 'Incomplete Requirements';
            $timeFrame->Notes = 'Only submitted ' . $reqSubmitted;
            $timeFrame->save();

            return redirect(route('serviceConnections.show', [$id]));
        }
    }
}
