<?php

namespace App\Actions;

use App\Repositories\TaskRepository;

class TaskManager
{
    public function __construct(protected readonly TaskRepository $repository)
    {
    }

    public function list(int $userId, ?string $status = null, string $order = 'asc')
    {
        return $this->repository->list($userId, $status, $order);
    }

    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    public function find(int $userId, int $taskId)
    {
        return $this->repository->findByUser($userId, $taskId);
    }

    public function update(int $userId, int $taskId, array $data)
    {
        return $this->repository->updateByUser($userId, $taskId, $data);
    }

    public function delete(int $userId, int $taskId)
    {
        return $this->repository->deleteByUser($userId, $taskId);
    }
}
