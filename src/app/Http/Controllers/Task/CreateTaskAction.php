<?php

declare(strict_types=1);

namespace App\Http\Controllers\Task;

use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CreateTaskAction
{
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'due_date' => 'nullable|date',
            'is_completed' => 'required|boolean',
            'assigned_user_id' => 'required|exists:users,id',
        ]);

        $assignedUserId = $validated['assigned_user_id'];
        if (Task::hasIncompleteTaskAssigned($assignedUserId)) {
            return response()->json(['message' => 'このユーザには進行中のタスクがアサインされています'], 400);
        }

        $task = new Task();
        $task->title = $validated['title'];
        $task->description = $validated['description'];
        $task->due_date = $validated['due_date'] ?? null;
        $task->is_completed = $validated['is_completed'] ?? false;
        $task->assigned_user_id = $assignedUserId;
        $task->save();

        return response()->json(['id' => $task->id], 201);
    }
}
