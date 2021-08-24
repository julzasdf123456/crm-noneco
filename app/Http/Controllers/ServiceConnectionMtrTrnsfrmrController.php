<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateServiceConnectionMtrTrnsfrmrRequest;
use App\Http\Requests\UpdateServiceConnectionMtrTrnsfrmrRequest;
use App\Repositories\ServiceConnectionMtrTrnsfrmrRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use App\Models\ServiceConnections;
use App\Http\Controllers\ServiceConnectionsController;
use Flash;
use Response;

class ServiceConnectionMtrTrnsfrmrController extends AppBaseController
{
    /** @var  ServiceConnectionMtrTrnsfrmrRepository */
    private $serviceConnectionMtrTrnsfrmrRepository;

    public function __construct(ServiceConnectionMtrTrnsfrmrRepository $serviceConnectionMtrTrnsfrmrRepo)
    {
        $this->middleware('auth');
        $this->serviceConnectionMtrTrnsfrmrRepository = $serviceConnectionMtrTrnsfrmrRepo;
    }

    /**
     * Display a listing of the ServiceConnectionMtrTrnsfrmr.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $serviceConnectionMtrTrnsfrmrs = $this->serviceConnectionMtrTrnsfrmrRepository->all();

        return view('service_connection_mtr_trnsfrmrs.index')
            ->with('serviceConnectionMtrTrnsfrmrs', $serviceConnectionMtrTrnsfrmrs);
    }

    /**
     * Show the form for creating a new ServiceConnectionMtrTrnsfrmr.
     *
     * @return Response
     */
    public function create()
    {
        return view('service_connection_mtr_trnsfrmrs.create');
    }

    /**
     * Store a newly created ServiceConnectionMtrTrnsfrmr in storage.
     *
     * @param CreateServiceConnectionMtrTrnsfrmrRequest $request
     *
     * @return Response
     */
    public function store(CreateServiceConnectionMtrTrnsfrmrRequest $request)
    {
        $input = $request->all();

        $serviceConnectionMtrTrnsfrmr = $this->serviceConnectionMtrTrnsfrmrRepository->create($input);

        Flash::success('Service Connection Mtr Trnsfrmr saved successfully.');

        // return redirect()->action([ServiceConnectionsController::class, 'show'], [$input['ServiceConnectionId']]);        
        return redirect(route('serviceConnectionPayTransactions.create-step-four', [$input['ServiceConnectionId']]));
    }

    /**
     * Display the specified ServiceConnectionMtrTrnsfrmr.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $serviceConnectionMtrTrnsfrmr = $this->serviceConnectionMtrTrnsfrmrRepository->find($id);

        if (empty($serviceConnectionMtrTrnsfrmr)) {
            Flash::error('Service Connection Mtr Trnsfrmr not found');

            return redirect(route('serviceConnectionMtrTrnsfrmrs.index'));
        }

        return view('service_connection_mtr_trnsfrmrs.show')->with('serviceConnectionMtrTrnsfrmr', $serviceConnectionMtrTrnsfrmr);
    }

    /**
     * Show the form for editing the specified ServiceConnectionMtrTrnsfrmr.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $serviceConnectionMtrTrnsfrmr = $this->serviceConnectionMtrTrnsfrmrRepository->find($id);

        if (empty($serviceConnectionMtrTrnsfrmr)) {
            Flash::error('Service Connection Mtr Trnsfrmr not found');

            return redirect(route('serviceConnectionMtrTrnsfrmrs.index'));
        }

        return view('service_connection_mtr_trnsfrmrs.edit')->with('serviceConnectionMtrTrnsfrmr', $serviceConnectionMtrTrnsfrmr);
    }

    /**
     * Update the specified ServiceConnectionMtrTrnsfrmr in storage.
     *
     * @param int $id
     * @param UpdateServiceConnectionMtrTrnsfrmrRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateServiceConnectionMtrTrnsfrmrRequest $request)
    {
        $serviceConnectionMtrTrnsfrmr = $this->serviceConnectionMtrTrnsfrmrRepository->find($id);

        if (empty($serviceConnectionMtrTrnsfrmr)) {
            Flash::error('Service Connection Mtr Trnsfrmr not found');

            return redirect(route('serviceConnectionMtrTrnsfrmrs.index'));
        }

        $serviceConnectionMtrTrnsfrmr = $this->serviceConnectionMtrTrnsfrmrRepository->update($request->all(), $id);

        Flash::success('Service Connection Mtr Trnsfrmr updated successfully.');

        // return redirect(route('serviceConnectionMtrTrnsfrmrs.index'));
        return redirect()->action([ServiceConnectionsController::class, 'show'], [$request['ServiceConnectionId']]);
    }

    /**
     * Remove the specified ServiceConnectionMtrTrnsfrmr from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $serviceConnectionMtrTrnsfrmr = $this->serviceConnectionMtrTrnsfrmrRepository->find($id);

        if (empty($serviceConnectionMtrTrnsfrmr)) {
            Flash::error('Service Connection Mtr Trnsfrmr not found');

            return redirect(route('serviceConnectionMtrTrnsfrmrs.index'));
        }

        $this->serviceConnectionMtrTrnsfrmrRepository->delete($id);

        Flash::success('Service Connection Mtr Trnsfrmr deleted successfully.');

        return redirect(route('serviceConnectionMtrTrnsfrmrs.index'));
    }

    public function createStepThree($scId) {
        $serviceConnection = ServiceConnections::find($scId);

        $serviceConnectionMtrTrnsfrmr = null;

        return view('/service_connection_mtr_trnsfrmrs/create_step_three', ['serviceConnection' => $serviceConnection, 'serviceConnectionMtrTrnsfrmr' => $serviceConnectionMtrTrnsfrmr]);
    }
}
