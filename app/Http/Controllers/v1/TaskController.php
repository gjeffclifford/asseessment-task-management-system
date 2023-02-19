<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Task;
use App\Http\Requests\CreateTaskFormRequest;
use Illuminate\Validation\Rule;
use Spatie\RouteAttributes\Attributes\Resource;

#[Resource(
    resource: 'v1/tasks', 
    apiResource: true,
    shallow: true, 
    parameters: ['comments' => 'comment:uuid'],
    names: 'api.v1.tasks',
)]
class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        if ($request->keyword) {
            $tasks = Task::where('name', 'LIKE', "%$request->keyword%")
                ->orWhere('description', 'LIKE', "%$request->keyword%")
                ->paginate(10);
        } else {
            $tasks = Task::paginate($request->rowsPerPage ?? 10);
        }
        return new Response($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateTaskFormRequest $request): Response
    {
        $task = Task::create($request->all());

        return new Response($task);
    }

    /**
     * Display the specified resource.
     */

    public function show(Task $task): Response
    {
        return new Response($task);
    }

    /**
     * Update the specified resource in storage.
     */
    // #[Route(uri: ':id/update')]
    public function update(Request $request, Task $task): Response
    {
        $request->validate(['name' => [
            'required',
            Rule::unique("tasks","name")->ignore($task->id),
            'max:255'
        ]]);

        $task->update($request->all());

        return new Response($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task): Response
    {
        $task->delete();

        return new Response('success', 201);
    }
}
