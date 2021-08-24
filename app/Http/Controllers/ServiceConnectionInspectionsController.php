<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateServiceConnectionInspectionsRequest;
use App\Http\Requests\UpdateServiceConnectionInspectionsRequest;
use App\Repositories\ServiceConnectionInspectionsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use App\Models\ServiceConnections;
use App\Http\Controllers\ServiceConnectionsController;
use App\Models\User;
use Flash;
use Response;

class ServiceConnectionInspectionsController extends AppBaseController
{
    /** @var  ServiceConnectionInspectionsRepository */
    private $serviceConnectionInspectionsRepository;

    public function __construct(ServiceConnectionInspectionsRepository $serviceConnectionInspectionsRepo)
    {
        $this->middleware('auth');
        $this->serviceConnectionInspectionsRepository = $serviceConnectionInspectionsRepo;
    }

    /**
     * Display a listing of the ServiceConnectionInspections.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $serviceConnectionInspections = $this->serviceConnectionInspectionsRepository->all();

        return view('service_connection_inspections.index')
            ->with('serviceConnectionInspections', $serviceConnectionInspections);
    }

    /**
     * Show the form for creating a new ServiceConnectionInspections.
     *
     * @return Response
     */
    public function create()
    {
        return view('service_connection_inspections.create');
    }

    /**
     * Store a newly created ServiceConnectionInspections in storage.
     *
     * @param CreateServiceConnectionInspectionsRequest $request
     *
     * @return Response
     */
    public function store(CreateServiceConnectionInspectionsRequest $request)
    {
        $input = $request->all();

        $serviceConnectionInspections = $this->serviceConnectionInspectionsRepository->create($input);

        Flash::success('Service Connection Inspections saved successfully.');

        // return redirect()->action([ServiceConnectionsController::class, 'show'], [$input['ServiceConnectionId']]);
        // return redirect()->action([App\Http\Controllers\ServiceConnectionMtrTrnsfrmrController::class, 'createStepThree'], [$input['ServiceConnectionId']]);
        return redirect(route('serviceConnectionMtrTrnsfrmrs.create-step-three', [$input['ServiceConnectionId']]));
    }

    /**
     * Display the specified ServiceConnectionInspections.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $serviceConnectionInspections = $this->serviceConnectionInspectionsRepository->find($id);

        if (empty($serviceConnectionInspections)) {
            Flash::error('Service Connection Inspections not found');

            return redirect(route('serviceConnectionInspections.index'));
        }

        return view('service_connection_inspections.show')->with('serviceConnectionInspections', $serviceConnectionInspections);
    }

    /**
     * Show the form for editing the specified ServiceConnectionInspections.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $serviceConnectionInspections = $this->serviceConnectionInspectionsRepository->find($id);

        $inspectors = User::permission('sc verifier')->pluck('name', 'id'); // CHANGE PERMISSION TO WHATEVER VERIFIER NAME IS

        if (empty($serviceConnectionInspections)) {
            Flash::error('Service Connection Inspections not found');

            return redirect(route('serviceConnectionInspections.index'));
        }

        return view('service_connection_inspections.edit', ['serviceConnectionInspections' => $serviceConnectionInspections, 'inspectors' => $inspectors]);
    }

    /**
     * Update the specified ServiceConnectionInspections in storage.
     *
     * @param int $id
     * @param UpdateServiceConnectionInspectionsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateServiceConnectionInspectionsRequest $request)
    {
        $serviceConnectionInspections = $this->serviceConnectionInspectionsRepository->find($id);

        if (empty($serviceConnectionInspections)) {
            Flash::error('Service Connection Inspections not found');

            return redirect(route('serviceConnectionInspections.index'));
        }

        $serviceConnectionInspections = $this->serviceConnectionInspectionsRepository->update($request->all(), $id);

        Flash::success('Service Connection Inspections updated successfully.');

        // return redirect(route('serviceConnectionInspections.index'));
        return redirect()->action([ServiceConnectionsController::class, 'show'], [$request['ServiceConnectionId']]);
    }

    /**
     * Remove the specified ServiceConnectionInspections from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $serviceConnectionInspections = $this->serviceConnectionInspectionsRepository->find($id);

        if (empty($serviceConnectionInspections)) {
            Flash::error('Service Connection Inspections not found');

            return redirect(route('serviceConnectionInspections.index'));
        }

        $this->serviceConnectionInspectionsRepository->delete($id);

        Flash::success('Service Connection Inspections deleted successfully.');

        return redirect(route('serviceConnectionInspections.index'));
    }

    public function createStepTwo($scId) {
        $serviceConnection = ServiceConnections::find($scId);

        $inspectors = User::permission('sc verifier')->pluck('name', 'id'); // CHANGE PERMISSION TO WHATEVER VERIFIER NAME IS

        $serviceConnectionInspections = null;

        return view('/service_connection_inspections/create_step_two', ['serviceConnection' => $serviceConnection, 'inspectors' => $inspectors, 'serviceConnectionInspections' => $serviceConnectionInspections]);
    }
}
