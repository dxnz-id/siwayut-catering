<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Event;

class EventService {
    public function __construct(private Event $event) {}

    public function all(): array {
        return $this->event->all();
    }

    public function getActive(): array {
        return $this->event->getActive();
    }

    public function paginate(int $page = 1, int $perPage = 10, string $search = '', array $filters = [], string $orderBy = 'created_at', string $direction = 'DESC'): array {
        $conditions = [];
        if (!empty($filters['status'])) $conditions['status'] = $filters['status'];
        return $this->event->paginate($page, $perPage, $conditions, $search, $this->event->getSearchableColumns(), $orderBy, $direction);
    }

    public function find(int $id): ?array {
        return $this->event->find($id);
    }

    public function create(array $data): int {
        return $this->event->create([
            'name' => $data['name'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'status' => $data['status'] ?? 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function update(int $id, array $data): bool {
        return $this->event->update($id, [
            'name' => $data['name'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'status' => $data['status'] ?? 'active',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function delete(int $id): bool {
        return $this->event->delete($id);
    }
}
