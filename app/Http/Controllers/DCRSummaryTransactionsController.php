<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDCRSummaryTransactionsRequest;
use App\Http\Requests\UpdateDCRSummaryTransactionsRequest;
use App\Repositories\DCRSummaryTransactionsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Response;

class DCRSummaryTransactionsController extends AppBaseController
{
    /** @var  DCRSummaryTransactionsRepository */
    private $dCRSummaryTransactionsRepository;

    public function __construct(DCRSummaryTransactionsRepository $dCRSummaryTransactionsRepo)
    {
        $this->middleware('auth');
        $this->dCRSummaryTransactionsRepository = $dCRSummaryTransactionsRepo;
    }

    /**
     * Display a listing of the DCRSummaryTransactions.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $dCRSummaryTransactions = $this->dCRSummaryTransactionsRepository->all();

        return view('d_c_r_summary_transactions.index')
            ->with('dCRSummaryTransactions', $dCRSummaryTransactions);
    }

    /**
     * Show the form for creating a new DCRSummaryTransactions.
     *
     * @return Response
     */
    public function create()
    {
        return view('d_c_r_summary_transactions.create');
    }

    /**
     * Store a newly created DCRSummaryTransactions in storage.
     *
     * @param CreateDCRSummaryTransactionsRequest $request
     *
     * @return Response
     */
    public function store(CreateDCRSummaryTransactionsRequest $request)
    {
        $input = $request->all();

        $dCRSummaryTransactions = $this->dCRSummaryTransactionsRepository->create($input);

        Flash::success('D C R Summary Transactions saved successfully.');

        return redirect(route('dCRSummaryTransactions.index'));
    }

    /**
     * Display the specified DCRSummaryTransactions.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $dCRSummaryTransactions = $this->dCRSummaryTransactionsRepository->find($id);

        if (empty($dCRSummaryTransactions)) {
            Flash::error('D C R Summary Transactions not found');

            return redirect(route('dCRSummaryTransactions.index'));
        }

        return view('d_c_r_summary_transactions.show')->with('dCRSummaryTransactions', $dCRSummaryTransactions);
    }

    /**
     * Show the form for editing the specified DCRSummaryTransactions.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $dCRSummaryTransactions = $this->dCRSummaryTransactionsRepository->find($id);

        if (empty($dCRSummaryTransactions)) {
            Flash::error('D C R Summary Transactions not found');

            return redirect(route('dCRSummaryTransactions.index'));
        }

        return view('d_c_r_summary_transactions.edit')->with('dCRSummaryTransactions', $dCRSummaryTransactions);
    }

    /**
     * Update the specified DCRSummaryTransactions in storage.
     *
     * @param int $id
     * @param UpdateDCRSummaryTransactionsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDCRSummaryTransactionsRequest $request)
    {
        $dCRSummaryTransactions = $this->dCRSummaryTransactionsRepository->find($id);

        if (empty($dCRSummaryTransactions)) {
            Flash::error('D C R Summary Transactions not found');

            return redirect(route('dCRSummaryTransactions.index'));
        }

        $dCRSummaryTransactions = $this->dCRSummaryTransactionsRepository->update($request->all(), $id);

        Flash::success('D C R Summary Transactions updated successfully.');

        return redirect(route('dCRSummaryTransactions.index'));
    }

    /**
     * Remove the specified DCRSummaryTransactions from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $dCRSummaryTransactions = $this->dCRSummaryTransactionsRepository->find($id);

        if (empty($dCRSummaryTransactions)) {
            Flash::error('D C R Summary Transactions not found');

            return redirect(route('dCRSummaryTransactions.index'));
        }

        $this->dCRSummaryTransactionsRepository->delete($id);

        Flash::success('D C R Summary Transactions deleted successfully.');

        return redirect(route('dCRSummaryTransactions.index'));
    }
}
