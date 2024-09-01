<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\TaskStatusEnum;
use App\Http\Requests\IndexTaskRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use OpenApi\Attributes as OA;

#[OA\Info(version: '1.0.0', title: 'Task API')]
#[OA\Tag(name: 'Tasks', description: 'Operations related to tasks')]
final class TaskController extends Controller
{
    #[OA\Get(
        path: '/tasks',
        summary: 'Get a list of tasks',
        tags: ['Tasks'],
        parameters: [
            new OA\QueryParameter(
                name: 'title',
                description: 'Filter tasks by title',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\QueryParameter(
                name: 'status',
                description: 'Filter tasks by status',
                required: false,
                schema: new OA\Schema(type: 'string', enum: TaskStatusEnum::class)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'A list of tasks',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/Task')
                )
            ),
        ]
    )]
    public function index(IndexTaskRequest $request)
    {
        $query = Task::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('title')) {
            $query->where('title', $request->title);
        }

        return response()->json($query->get(['title', 'status']));
    }

    #[OA\Post(
        path: '/tasks',
        summary: 'Create a new task',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/StoreTaskRequest')
        ),
        tags: ['Tasks'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Task created',
                content: new OA\JsonContent(ref: '#/components/schemas/Task')
            ),
        ]
    )]
    public function store(StoreTaskRequest $request)
    {
        $task = Task::create([
            'title' => $request->title,
            'status' => TaskStatusEnum::from($request->status)->value,
            'completed_at' => $request->status === TaskStatusEnum::DONE->value ? now() : null,
        ]);

        return response()->json($task, 201);
    }

    #[OA\Get(
        path: '/tasks/{task}',
        summary: 'Get a specific task',
        tags: ['Tasks'],
        parameters: [
            new OA\Parameter(
                name: 'task',
                description: 'Task ID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'A specific task',
                content: new OA\JsonContent(ref: '#/components/schemas/Task')
            ),
        ]
    )]
    public function show(Task $task)
    {
        return response()->json($task->only('title', 'status', 'time_spent', 'created_at', 'completed_at'));
    }

    #[OA\Put(
        path: '/tasks/{task}',
        summary: 'Update an existing task',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateTaskRequest')
        ),
        tags: ['Tasks'],
        parameters: [
            new OA\Parameter(
                name: 'task',
                description: 'Task ID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Task updated',
                content: new OA\JsonContent(ref: '#/components/schemas/Task')
            ),
        ]
    )]
    public function update(Task $task, UpdateTaskRequest $request)
    {
        $task->update($request->validated());

        return response()->json($task);
    }
}
