<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskCollection;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;

class TaskController extends Controller
{
    public function index(Request $request) {

        $tasks = QueryBuilder::for(Task::class)
            ->allowedFilters(['is_done', 'title'])
            ->defaultSorts('-created_at')
            ->allowedSorts(['title','is_done', 'created_at'])
            ->paginate();
        return new TaskCollection($tasks);
    }
    public function show(Request $request, Task $task) {
        return new TaskResource($task);
    }
    public function store(Request $request) {
        $validate = $request->validate([
            'title'=> 'required|max:255',
            'is_done'=> 'boolean',
        ]);

       /** @var \App\Models\User $tasks **/

        $task = Auth::user()->tasks()->create($validate);
        return new TaskResource($task);
    }
    public function update(UpdateTaskRequest $request, Task $task) {
        $validate = $request->validate([
            'title'=> 'sometimes|max:255',
            'is_done'=> 'sometimes',
            'project_id'=> 'sometimes'
        ]);
        $task->update($validate);
        return new TaskResource($task);
    }
    public function destroy(Request $request, Task $task) {
        $task->delete();
        return response()->noContent();
    }
}
