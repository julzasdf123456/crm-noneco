<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateNotifiersRequest;
use App\Http\Requests\UpdateNotifiersRequest;
use App\Repositories\NotifiersRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Response;

class NotifiersController extends AppBaseController
{
    /** @var  NotifiersRepository */
    private $notifiersRepository;

    public function __construct(NotifiersRepository $notifiersRepo)
    {
        $this->middleware('auth');
        $this->notifiersRepository = $notifiersRepo;
    }

    /**
     * Display a listing of the Notifiers.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $notifiers = $this->notifiersRepository->all();

        return view('notifiers.index')
            ->with('notifiers', $notifiers);
    }

    /**
     * Show the form for creating a new Notifiers.
     *
     * @return Response
     */
    public function create()
    {
        return view('notifiers.create');
    }

    /**
     * Store a newly created Notifiers in storage.
     *
     * @param CreateNotifiersRequest $request
     *
     * @return Response
     */
    public function store(CreateNotifiersRequest $request)
    {
        $input = $request->all();

        $notifiers = $this->notifiersRepository->create($input);

        Flash::success('Notifiers saved successfully.');

        return redirect(route('notifiers.index'));
    }

    /**
     * Display the specified Notifiers.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $notifiers = $this->notifiersRepository->find($id);

        if (empty($notifiers)) {
            Flash::error('Notifiers not found');

            return redirect(route('notifiers.index'));
        }

        return view('notifiers.show')->with('notifiers', $notifiers);
    }

    /**
     * Show the form for editing the specified Notifiers.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $notifiers = $this->notifiersRepository->find($id);

        if (empty($notifiers)) {
            Flash::error('Notifiers not found');

            return redirect(route('notifiers.index'));
        }

        return view('notifiers.edit')->with('notifiers', $notifiers);
    }

    /**
     * Update the specified Notifiers in storage.
     *
     * @param int $id
     * @param UpdateNotifiersRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateNotifiersRequest $request)
    {
        $notifiers = $this->notifiersRepository->find($id);

        if (empty($notifiers)) {
            Flash::error('Notifiers not found');

            return redirect(route('notifiers.index'));
        }

        $notifiers = $this->notifiersRepository->update($request->all(), $id);

        Flash::success('Notifiers updated successfully.');

        return redirect(route('notifiers.index'));
    }

    /**
     * Remove the specified Notifiers from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $notifiers = $this->notifiersRepository->find($id);

        if (empty($notifiers)) {
            Flash::error('Notifiers not found');

            return redirect(route('notifiers.index'));
        }

        $this->notifiersRepository->delete($id);

        Flash::success('Notifiers deleted successfully.');

        return redirect(route('notifiers.index'));
    }
}
