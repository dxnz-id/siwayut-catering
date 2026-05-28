<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Session;
use App\Core\Validator;
use App\Exceptions\NotFoundException;
use App\Services\EventService;

class EventController extends BaseController {
    public function __construct(private EventService $eventService) {
        parent::__construct();
    }

    public function index(Request $request): void {
        $page = (int) $request->input('page', 1);
        $search = $request->input('search', '');
        $orderBy = $request->input('sort_by', 'created_at');
        $direction = $request->input('dir', 'DESC');
        $filters = [
            'status' => $request->input('status', ''),
        ];
        $result = $this->eventService->paginate($page, 10, $search, $filters, $orderBy, $direction);
        
        $this->render('event/index', [
            'title' => 'Events',
            'events' => $result['data'],
            'pagination' => $result,
            'search' => $search,
            'filters' => $filters,
            'sort_by' => $orderBy,
            'dir' => $direction,
        ]);
    }

    public function create(Request $request): void {
        $this->render('event/create', [
            'title' => 'Add Event',
        ]);
    }

    public function store(Request $request): void {
        $data = $request->only(['name', 'start_date', 'end_date', 'status']);

        $validator = new Validator();
        $validator->validate($data, [
            'name' => 'required|min:3|max:100',
            'start_date' => 'required',
            'end_date' => 'required',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            $this->withOldInput($data);
            Session::flash('errors', json_encode($validator->errors()));
            $this->redirect('/events/create');
        }

        $this->eventService->create($data);
        $this->redirectWithFlash('/events', 'success', 'Event successfully added.');
    }

    public function edit(Request $request): void {
        $id = (int) $request->param('id');
        $event = $this->eventService->find($id);

        if (!$event) {
            throw new NotFoundException('Event not found.');
        }

        $this->render('event/edit', [
            'title' => 'Edit Event',
            'event' => $event,
        ]);
    }

    public function update(Request $request): void {
        $id = (int) $request->param('id');
        $data = $request->only(['name', 'start_date', 'end_date', 'status']);

        $validator = new Validator();
        $validator->validate($data, [
            'name' => 'required|min:3|max:100',
            'start_date' => 'required',
            'end_date' => 'required',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            $this->withOldInput($data);
            Session::flash('errors', json_encode($validator->errors()));
            $this->redirect("/events/{$id}/edit");
        }

        $this->eventService->update($id, $data);
        $this->redirectWithFlash('/events', 'success', 'Event successfully updated.');
    }

    public function destroy(Request $request): void {
        $id = (int) $request->param('id');
        
        try {
            if ($this->eventService->delete($id)) {
                $this->redirectWithFlash('/events', 'success', 'Event successfully deleted.');
            } else {
                $this->redirectWithFlash('/events', 'error', 'Failed to delete event.');
            }
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                $this->redirectWithFlash('/events', 'error', 'Cannot delete event. It is still used by a menu or order.');
            } else {
                $this->redirectWithFlash('/events', 'error', 'Database error: ' . $e->getMessage());
            }
        }
    }
}
