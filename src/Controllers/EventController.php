<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Core\Logger;
use App\Exceptions\NotFoundException;
use App\Services\EventService;

class EventController extends BaseController {
    public function __construct(
        private EventService $eventService
    ) {
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
            'title' => __('events'),
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
            'title' => __('add_event'),
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
            if ($request->isAjax()) {
                Response::jsonError(__('validation_failed'), $validator->errors());
            }
            $this->withOldInput($data);
            Session::flash('errors', json_encode($validator->errors()));
            $this->redirect('/events/create');
        }

        try {
            $this->eventService->create($data);
            if ($request->isAjax()) {
                Response::jsonSuccess(null, __('event_added'));
            }
            $this->redirectWithFlash('/events', 'success', __('event_added'));
        } catch (\Exception $e) {
            Logger::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $errorMsg = APP_DEBUG ? $e->getMessage() : __('operation_failed');
            if ($request->isAjax()) {
                Response::jsonError(__('failed_create_event', ['error' => $errorMsg]));
            }
            $this->withOldInput($data);
            Session::flash('error', __('failed_create_event', ['error' => $errorMsg]));
            $this->redirect('/events/create');
        }
    }

    public function apiShow(Request $request): void {
        $id = (int) $request->param('id');
        $event = $this->eventService->find($id);
        if (!$event) Response::jsonError(__('not_found_api'), [], 404);
        Response::jsonSuccess($event);
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
            if ($request->isAjax()) Response::jsonError(__('validation_failed'), $validator->errors());
            $this->withOldInput($data);
            Session::flash('errors', json_encode($validator->errors()));
            $this->redirect("/events/{$id}/edit");
        }

        try {
            $this->eventService->update($id, $data);
            if ($request->isAjax()) Response::jsonSuccess(null, __('event_updated'));
            $this->redirectWithFlash('/events', 'success', __('event_updated'));
        } catch (\Exception $e) {
            Logger::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $errorMsg = APP_DEBUG ? $e->getMessage() : __('operation_failed');
            if ($request->isAjax()) Response::jsonError(__('failed_update_event', ['error' => $errorMsg]));
            $this->withOldInput($data);
            Session::flash('error', __('failed_update_event', ['error' => $errorMsg]));
            $this->redirect("/events/{$id}/edit");
        }
    }

    public function destroy(Request $request): void {
        $id = (int) $request->param('id');
        
        try {
            if ($this->eventService->delete($id)) {
                $this->redirectWithFlash('/events', 'success', __('event_deleted'));
            } else {
                $this->redirectWithFlash('/events', 'error', __('failed_delete_event'));
            }
        } catch (\PDOException $e) {
            Logger::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            if ($e->getCode() == 23000) {
                $this->redirectWithFlash('/events', 'error', __('event_in_use'));
            } else {
                $errorMsg = APP_DEBUG ? $e->getMessage() : __('operation_failed');
                $this->redirectWithFlash('/events', 'error', __('db_error', ['error' => $errorMsg]));
            }
        }
    }
}
