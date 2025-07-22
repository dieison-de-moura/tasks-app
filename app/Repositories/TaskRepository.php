<?php

namespace App\Repositories;

use App\Models\Task;

class TaskRepository
{
    public function __construct(protected readonly Task $model)
    {

    }

    public function create(array $data): Task
    {
        return $this->model->create($data);
    }

    public function list(int $userId, ?string $status = null, string $order = 'asc')
    {
        $query = $this->model->where('user_id', $userId);
        if ($status) {
            $query->where('status', $status);
        }
        $query->orderBy('deadline', $order);
        return $query->get();
    }

    public function findByUser(int $userId, int $taskId)
    {
        return $this->model->where('user_id', $userId)->where('id', $taskId)->first();
    }

    public function updateByUser(int $userId, int $taskId, array $data)
    {
        $task = $this->findByUser($userId, $taskId);
        if (!$task) {
            return null;
        }
        $task->update($data);
        return $task;
    }

    public function deleteByUser(int $userId, int $taskId)
    {
        $task = $this->findByUser($userId, $taskId);
        if (!$task) {
            return false;
        }
        return $task->delete();
    }
}
