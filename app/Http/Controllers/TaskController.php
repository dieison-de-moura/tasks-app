<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Actions\TaskManager;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function __construct(protected TaskManager $taskManager) {}

    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $status = $request->query('status');
        $order = $request->query('order', 'asc');
        $tasks = $this->taskManager->list($userId, $status, $order);
        return Inertia::render('Tasks', [
            'tasks' => $tasks,
        ]);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,finished',
            'deadline' => 'string',
        ]);
        $validated['user_id'] = $request->user()->id;
        $this->taskManager->create($validated);
        return Inertia::location(route('tasks.index'));
    }

    public function show(Request $request, $taskId)
    {
        $userId = $request->user()->id;
        $task = $this->taskManager->find($userId, $taskId);
        if (!$task) {
            return response()->json(['message' => 'Tarefa não encontrada'], 404);
        }
        return Inertia::render('Tasks', [
            'task' => $task,
        ]);
    }

    public function update(Request $request, $taskId)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|required|in:pending,finished',
            'deadline' => 'nullable|date',
        ]);
        $userId = $request->user()->id;
        $task = $this->taskManager->update($userId, $taskId, $validated);
        if (!$task) {
            return response()->json(['message' => 'Tarefa não encontrada ou não autorizada'], 404);
        }
        return Inertia::location(route('tasks.index'));
    }

    public function destroy(Request $request, $taskId)
    {
        $userId = $request->user()->id;
        $this->taskManager->delete($userId, $taskId);
        return Inertia::location(route('tasks.index'));
    }
}
