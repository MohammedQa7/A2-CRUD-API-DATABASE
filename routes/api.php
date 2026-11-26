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
Route::post('/tasks', function (Request $request) {
    $title = $request->input('title');

    if (!is_string($title) || trim($title) === '') {
        return response()->json(['error' => 'Title is required'], 400);
    }

    $task = Task::create([
        'title' => trim($title),
        'done' => false,
    ]);

    return response()->json($task, 201);
});


// PUT /tasks/{id} - update a task
Route::put('/tasks/{id}', function (Request $request, $id) {
    $task = Task::find($id);

    if (!$task) {
        return response()->json(['error' => 'Task not found'], 404);
    }

    $data = collect($request->json()->all())
        ->only(['title', 'done'])
        ->all();

    if (
        empty($data) ||
        (array_key_exists('title', $data) && (!is_string($data['title']) || trim($data['title']) === '')) ||
        (array_key_exists('done', $data) && !is_bool($data['done']))
    ) {
        return response()->json(['error' => 'Invalid body'], 400);
    }

    $task->update($data);

    return response()->json($task);
});
// DELETE /tasks/{id} - remove a task
Route::delete('/tasks/{id}', function (string $id) {
    $task = Task::find($id);

    if (!$task) {
        return response()->json(['error' => 'Task not found'], 404);
    }

    $task->delete();

    return response()->noContent(); // 204
});