<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDisconnectionHistoryRequest;
use App\Http\Requests\UpdateDisconnectionHistoryRequest;
use App\Repositories\DisconnectionHistoryRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Response;

class DisconnectionHistoryController extends AppBaseController
{
    /** @var  DisconnectionHistoryRepository */
    private $disconnectionHistoryRepository;

    public function __construct(DisconnectionHistoryRepository $disconnectionHistoryRepo)
    {
        $this->middleware('auth');
        $this->disconnectionHistoryRepository = $disconnectionHistoryRepo;
    }

    /**
     * Display a listing of the DisconnectionHistory.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $disconnectionHistories = $this->disconnectionHistoryRepository->all();

        return view('disconnection_histories.index')
            ->with('disconnectionHistories', $disconnectionHistories);
    }

    /**
     * Show the form for creating a new DisconnectionHistory.
     *
     * @return Response
     */
    public function create()
    {
        return view('disconnection_histories.create');
    }

    /**
     * Store a newly created DisconnectionHistory in storage.
     *
     * @param CreateDisconnectionHistoryRequest $request
     *
     * @return Response
     */
    public function store(CreateDisconnectionHistoryRequest $request)
    {
        $input = $request->all();

        $disconnectionHistory = $this->disconnectionHistoryRepository->create($input);

        Flash::success('Disconnection History saved successfully.');

        return redirect(route('disconnectionHistories.index'));
    }

    /**
     * Display the specified DisconnectionHistory.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $disconnectionHistory = $this->disconnectionHistoryRepository->find($id);

        if (empty($disconnectionHistory)) {
            Flash::error('Disconnection History not found');

            return redirect(route('disconnectionHistories.index'));
        }

        return view('disconnection_histories.show')->with('disconnectionHistory', $disconnectionHistory);
    }

    /**
     * Show the form for editing the specified DisconnectionHistory.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $disconnectionHistory = $this->disconnectionHistoryRepository->find($id);

        if (empty($disconnectionHistory)) {
            Flash::error('Disconnection History not found');

            return redirect(route('disconnectionHistories.index'));
        }

        return view('disconnection_histories.edit')->with('disconnectionHistory', $disconnectionHistory);
    }

    /**
     * Update the specified DisconnectionHistory in storage.
     *
     * @param int $id
     * @param UpdateDisconnectionHistoryRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDisconnectionHistoryRequest $request)
    {
        $disconnectionHistory = $this->disconnectionHistoryRepository->find($id);

        if (empty($disconnectionHistory)) {
            Flash::error('Disconnection History not found');

            return redirect(route('disconnectionHistories.index'));
        }

        $disconnectionHistory = $this->disconnectionHistoryRepository->update($request->all(), $id);

        Flash::success('Disconnection History updated successfully.');

        return redirect(route('disconnectionHistories.index'));
    }

    /**
     * Remove the specified DisconnectionHistory from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $disconnectionHistory = $this->disconnectionHistoryRepository->find($id);

        if (empty($disconnectionHistory)) {
            Flash::error('Disconnection History not found');

            return redirect(route('disconnectionHistories.index'));
        }

        $this->disconnectionHistoryRepository->delete($id);

        Flash::success('Disconnection History deleted successfully.');

        return redirect(route('disconnectionHistories.index'));
    }
}
