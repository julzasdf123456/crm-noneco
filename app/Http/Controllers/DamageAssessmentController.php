<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDamageAssessmentRequest;
use App\Http\Requests\UpdateDamageAssessmentRequest;
use App\Repositories\DamageAssessmentRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Response;

class DamageAssessmentController extends AppBaseController
{
    /** @var  DamageAssessmentRepository */
    private $damageAssessmentRepository;

    public function __construct(DamageAssessmentRepository $damageAssessmentRepo)
    {
        $this->damageAssessmentRepository = $damageAssessmentRepo;
    }

    /**
     * Display a listing of the DamageAssessment.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $damageAssessments = $this->damageAssessmentRepository->all();

        return view('damage_assessments.index')
            ->with('damageAssessments', $damageAssessments);
    }

    /**
     * Show the form for creating a new DamageAssessment.
     *
     * @return Response
     */
    public function create()
    {
        return view('damage_assessments.create');
    }

    /**
     * Store a newly created DamageAssessment in storage.
     *
     * @param CreateDamageAssessmentRequest $request
     *
     * @return Response
     */
    public function store(CreateDamageAssessmentRequest $request)
    {
        $input = $request->all();

        $damageAssessment = $this->damageAssessmentRepository->create($input);

        Flash::success('Damage Assessment saved successfully.');

        return redirect(route('damageAssessments.index'));
    }

    /**
     * Display the specified DamageAssessment.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $damageAssessment = $this->damageAssessmentRepository->find($id);

        if (empty($damageAssessment)) {
            Flash::error('Damage Assessment not found');

            return redirect(route('damageAssessments.index'));
        }

        return view('damage_assessments.show')->with('damageAssessment', $damageAssessment);
    }

    /**
     * Show the form for editing the specified DamageAssessment.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $damageAssessment = $this->damageAssessmentRepository->find($id);

        if (empty($damageAssessment)) {
            Flash::error('Damage Assessment not found');

            return redirect(route('damageAssessments.index'));
        }

        return view('damage_assessments.edit')->with('damageAssessment', $damageAssessment);
    }

    /**
     * Update the specified DamageAssessment in storage.
     *
     * @param int $id
     * @param UpdateDamageAssessmentRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDamageAssessmentRequest $request)
    {
        $damageAssessment = $this->damageAssessmentRepository->find($id);

        if (empty($damageAssessment)) {
            Flash::error('Damage Assessment not found');

            return redirect(route('damageAssessments.index'));
        }

        $damageAssessment = $this->damageAssessmentRepository->update($request->all(), $id);

        Flash::success('Damage Assessment updated successfully.');

        return redirect(route('damageAssessments.index'));
    }

    /**
     * Remove the specified DamageAssessment from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $damageAssessment = $this->damageAssessmentRepository->find($id);

        if (empty($damageAssessment)) {
            Flash::error('Damage Assessment not found');

            return redirect(route('damageAssessments.index'));
        }

        $this->damageAssessmentRepository->delete($id);

        Flash::success('Damage Assessment deleted successfully.');

        return redirect(route('damageAssessments.index'));
    }
}
