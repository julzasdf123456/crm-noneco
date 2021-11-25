<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBillingMetersRequest;
use App\Http\Requests\UpdateBillingMetersRequest;
use App\Repositories\BillingMetersRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Response;

class BillingMetersController extends AppBaseController
{
    /** @var  BillingMetersRepository */
    private $billingMetersRepository;

    public function __construct(BillingMetersRepository $billingMetersRepo)
    {
        $this->middleware('auth');
        $this->billingMetersRepository = $billingMetersRepo;
    }

    /**
     * Display a listing of the BillingMeters.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $billingMeters = $this->billingMetersRepository->all();

        return view('billing_meters.index')
            ->with('billingMeters', $billingMeters);
    }

    /**
     * Show the form for creating a new BillingMeters.
     *
     * @return Response
     */
    public function create()
    {
        return view('billing_meters.create');
    }

    /**
     * Store a newly created BillingMeters in storage.
     *
     * @param CreateBillingMetersRequest $request
     *
     * @return Response
     */
    public function store(CreateBillingMetersRequest $request)
    {
        $input = $request->all();

        $billingMeters = $this->billingMetersRepository->create($input);

        Flash::success('Billing Meters saved successfully.');

        return redirect(route('billingMeters.index'));
    }

    /**
     * Display the specified BillingMeters.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $billingMeters = $this->billingMetersRepository->find($id);

        if (empty($billingMeters)) {
            Flash::error('Billing Meters not found');

            return redirect(route('billingMeters.index'));
        }

        return view('billing_meters.show')->with('billingMeters', $billingMeters);
    }

    /**
     * Show the form for editing the specified BillingMeters.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $billingMeters = $this->billingMetersRepository->find($id);

        if (empty($billingMeters)) {
            Flash::error('Billing Meters not found');

            return redirect(route('billingMeters.index'));
        }

        return view('billing_meters.edit')->with('billingMeters', $billingMeters);
    }

    /**
     * Update the specified BillingMeters in storage.
     *
     * @param int $id
     * @param UpdateBillingMetersRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBillingMetersRequest $request)
    {
        $billingMeters = $this->billingMetersRepository->find($id);

        if (empty($billingMeters)) {
            Flash::error('Billing Meters not found');

            return redirect(route('billingMeters.index'));
        }

        $billingMeters = $this->billingMetersRepository->update($request->all(), $id);

        Flash::success('Billing Meters updated successfully.');

        return redirect(route('billingMeters.index'));
    }

    /**
     * Remove the specified BillingMeters from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $billingMeters = $this->billingMetersRepository->find($id);

        if (empty($billingMeters)) {
            Flash::error('Billing Meters not found');

            return redirect(route('billingMeters.index'));
        }

        $this->billingMetersRepository->delete($id);

        Flash::success('Billing Meters deleted successfully.');

        return redirect(route('billingMeters.index'));
    }
}
