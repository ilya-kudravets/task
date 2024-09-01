<?php

declare(strict_types=1);

use App\Enums\TaskStatusEnum;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can store a new task', function () {
    $data = [
        'title' => 'Test Task',
        'status' => TaskStatusEnum::TO_DO->value,
    ];

    $response = $this->postJson('/api/tasks', $data);

    $response->assertStatus(201)
        ->assertJson([
            'title' => 'Test Task',
            'status' => TaskStatusEnum::TO_DO->value,
            'completed_at' => null,
        ]);

    $this->assertDatabaseHas('tasks', $data);
});

test('can update an existing task', function () {
    $task = Task::factory()->create([
        'status' => TaskStatusEnum::TO_DO->value,
    ]);

    $data = [
        'title' => 'Updated Task',
        'status' => TaskStatusEnum::DONE->value,
        'completed_at' => now()->addDay()->toDateTimeString(),
    ];

    $response = $this->putJson("/api/tasks/{$task->id}", $data);

    $response->assertStatus(200)
        ->assertJson([
            'title' => 'Updated Task',
            'status' => TaskStatusEnum::DONE->value,
            'completed_at' => $data['completed_at'],
        ]);

    $this->assertDatabaseHas('tasks', $data);
});

test('validates data when updating an existing task', function () {
    $task = Task::factory()->create();

    $data = [
        'title' => '',
        'status' => 'invalid_status',
        'completed_at' => $task->created_at->subDay()->toDateTimeString(),
    ];

    $response = $this->putJson("/api/tasks/{$task->id}", $data);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['title', 'status', 'completed_at']);
});

test('can show a specific task', function () {
    $task = Task::factory()->create([
        'title' => 'Test Task',
        'status' => TaskStatusEnum::TO_DO->value,
    ]);

    $response = $this->getJson("/api/tasks/{$task->id}");

    $response->assertStatus(200)
        ->assertJson([
            'title' => 'Test Task',
            'status' => TaskStatusEnum::TO_DO->value,
        ]);
});

test('can list tasks with filters', function () {
    $task1 = Task::factory()->create([
        'title' => 'Task 1',
        'status' => TaskStatusEnum::TO_DO->value,
    ]);

    $task2 = Task::factory()->create([
        'title' => 'Task 2',
        'status' => TaskStatusEnum::DONE->value,
    ]);

    $response = $this->getJson('/api/tasks?status='.TaskStatusEnum::TO_DO->value.'&title=Task 1');

    $response->assertStatus(200)
        ->assertJsonFragment([
            'title' => 'Task 1',
            'status' => TaskStatusEnum::TO_DO->value,
        ])
        ->assertJsonMissing([
            'title' => 'Task 2',
            'status' => TaskStatusEnum::DONE->value,
        ]);
});

test('can list tasks', function () {
    $task1 = Task::factory()->create([
        'title' => 'Task 1',
        'status' => TaskStatusEnum::TO_DO->value,
    ]);
    $task2 = Task::factory()->create([
        'title' => 'Task 2',
        'status' => TaskStatusEnum::TO_DO->value,
    ]);

    $task3 = Task::factory()->create([
        'title' => 'Task 3',
        'status' => TaskStatusEnum::DONE->value,
    ]);

    $response = $this->getJson('/api/tasks?status='.TaskStatusEnum::TO_DO->value);

    $response->assertStatus(200)
        ->assertJsonFragment([
            'title' => 'Task 1',
            'status' => TaskStatusEnum::TO_DO->value,
        ])
        ->assertJsonFragment([
            'title' => 'Task 2',
            'status' => TaskStatusEnum::TO_DO->value,
        ])
        ->assertJsonMissing([
            'title' => 'Task 3',
            'status' => TaskStatusEnum::DONE->value,
        ]);
});
