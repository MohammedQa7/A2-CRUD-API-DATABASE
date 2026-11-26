<?php

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

// Starting data, used until a new list is saved in the cache
$defaultTasks = [
    ['id' => 1, 'title' => 'Learn PHP', 'done' => true],
    ['id' => 2, 'title' => 'Learn Laravel', 'done' => false],
    ['id' => 3, 'title' => 'Build a CRUD API', 'done' => true],
];

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/', function () {
    return response()->json([
        'status' => '200',
        'message' => 'Hello world',
    ]);
});

Route::get('/', function () {
    return response()->json([
        'name' => 'Task API',
        'version' => '1.0',
        'endpoints' => ['/tasks'],
    ]);
});

Route::get('/health', function () {
    return ['status' => 'OK'];
});

// ---------------------------------------

// GET /tasks - return all tasks
Route::get('/tasks', function () {
    return response()->json(Task::all());
});

// GET /tasks/{id} - return one task
Route::get('/tasks/{id}', function ($id) {
    // Only query for whole numbers, so "1abc" can't match task 1
    $task = Task::find($id);

    if (!$task) {
        return response()->json(['error' => 'Task not found'], 404);
    }

    return response()->json($task);
});

// POST /tasks - create a task
Route::post('/tasks', function (Request $request) use ($defaultTasks) {
    $title = $request->input('title');

    if (!is_string($title) || trim($title) === '') {
        return response()->json(['error' => 'Title is required'], 400);
    }

    $tasks = Cache::store('file')->get('tasks', $defaultTasks);

    $task = [
        'id' => empty($tasks) ? 1 : max(array_column($tasks, 'id')) + 1,
        'title' => trim($title),
        'done' => false,
    ];

    $tasks[] = $task;
    Cache::store('file')->forever('tasks', $tasks);

    return response()->json($task, 201);
});


// PUT /tasks/{id} - update title and/or done
Route::put('/tasks/{id}', function (Request $request, string $id) use ($defaultTasks) {
    $tasks = Cache::store('file')->get('tasks', $defaultTasks);

    $index = collect($tasks)->search(fn($task) => (string) $task['id'] === $id);

    // Use === false, because index 0 is a valid result
    if ($index === false) {
        return response()->json(['error' => "Task {$id} not found"], 404);
    }

    $data = $request->json()->all();

    if (!array_key_exists('title', $data) && !array_key_exists('done', $data)) {
        return response()->json([
            'error' => 'Send a JSON body with title and/or done',
        ], 400);
    }

    if (array_key_exists('title', $data)) {
        if (!is_string($data['title']) || trim($data['title']) === '') {
            return response()->json([
                'error' => 'Title must be a non-empty string',
            ], 400);
        }

        $tasks[$index]['title'] = trim($data['title']);
    }

    if (array_key_exists('done', $data)) {
        if (!is_bool($data['done'])) {
            return response()->json(['error' => 'Done must be true or false'], 400);
        }

        $tasks[$index]['done'] = $data['done'];
    }

    // Save only after all validation has passed
    Cache::store('file')->forever('tasks', $tasks);

    return response()->json($tasks[$index]);
});

// DELETE /tasks/{id} - remove a task
Route::delete('/tasks/{id}', function (string $id) use ($defaultTasks) {
    $tasks = Cache::store('file')->get('tasks', $defaultTasks);

    $index = collect($tasks)->search(fn($task) => (string) $task['id'] === $id);

    if ($index === false) {
        return response()->json(['error' => "Task {$id} not found"], 404);
    }

    // Remove the task and reindex, so GET /tasks still returns a JSON array
    array_splice($tasks, $index, 1);
    Cache::store('file')->forever('tasks', $tasks);

    return response()->noContent(); // 204
});