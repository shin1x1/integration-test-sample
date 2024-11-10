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
        $task = new Task();
        $task->title = $request->input('title');
        $task->description = $request->input('description');
        $task->due_date = $request->input('due_date');
        $task->is_completed = $request->input('is_completed');
        $task->save();

        return response()->json(['id' => $task->id], 201);
    }
}
