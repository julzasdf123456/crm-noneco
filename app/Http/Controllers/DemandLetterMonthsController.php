<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDemandLetterMonthsRequest;
use App\Http\Requests\UpdateDemandLetterMonthsRequest;
use App\Repositories\DemandLetterMonthsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Response;

class DemandLetterMonthsController extends AppBaseController
{
    /** @var  DemandLetterMonthsRepository */
    private $demandLetterMonthsRepository;

    public function __construct(DemandLetterMonthsRepository $demandLetterMonthsRepo)
    {
        $this->middleware('auth');
        $this->demandLetterMonthsRepository = $demandLetterMonthsRepo;
    }

    /**
     * Display a listing of the DemandLetterMonths.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $demandLetterMonths = $this->demandLetterMonthsRepository->all();

        return view('demand_letter_months.index')
            ->with('demandLetterMonths', $demandLetterMonths);
    }

    /**
     * Show the form for creating a new DemandLetterMonths.
     *
     * @return Response
     */
    public function create()
    {
        return view('demand_letter_months.create');
    }

    /**
     * Store a newly created DemandLetterMonths in storage.
     *
     * @param CreateDemandLetterMonthsRequest $request
     *
     * @return Response
     */
    public function store(CreateDemandLetterMonthsRequest $request)
    {
        $input = $request->all();

        $demandLetterMonths = $this->demandLetterMonthsRepository->create($input);

        Flash::success('Demand Letter Months saved successfully.');

        return redirect(route('demandLetterMonths.index'));
    }

    /**
     * Display the specified DemandLetterMonths.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $demandLetterMonths = $this->demandLetterMonthsRepository->find($id);

        if (empty($demandLetterMonths)) {
            Flash::error('Demand Letter Months not found');

            return redirect(route('demandLetterMonths.index'));
        }

        return view('demand_letter_months.show')->with('demandLetterMonths', $demandLetterMonths);
    }

    /**
     * Show the form for editing the specified DemandLetterMonths.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $demandLetterMonths = $this->demandLetterMonthsRepository->find($id);

        if (empty($demandLetterMonths)) {
            Flash::error('Demand Letter Months not found');

            return redirect(route('demandLetterMonths.index'));
        }

        return view('demand_letter_months.edit')->with('demandLetterMonths', $demandLetterMonths);
    }

    /**
     * Update the specified DemandLetterMonths in storage.
     *
     * @param int $id
     * @param UpdateDemandLetterMonthsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDemandLetterMonthsRequest $request)
    {
        $demandLetterMonths = $this->demandLetterMonthsRepository->find($id);

        if (empty($demandLetterMonths)) {
            Flash::error('Demand Letter Months not found');

            return redirect(route('demandLetterMonths.index'));
        }

        $demandLetterMonths = $this->demandLetterMonthsRepository->update($request->all(), $id);

        Flash::success('Demand Letter Months updated successfully.');

        return redirect(route('demandLetterMonths.index'));
    }

    /**
     * Remove the specified DemandLetterMonths from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $demandLetterMonths = $this->demandLetterMonthsRepository->find($id);

        if (empty($demandLetterMonths)) {
            Flash::error('Demand Letter Months not found');

            return redirect(route('demandLetterMonths.index'));
        }

        $this->demandLetterMonthsRepository->delete($id);

        Flash::success('Demand Letter Months deleted successfully.');

        return redirect(route('demandLetterMonths.index'));
    }
}
