<?php

declare(strict_types=1);

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a task can be created', function () {
    $task = Task::factory()->create([
        'title' => 'Test Task',
        'status' => 'ToDo',
    ]);

    $this->assertDatabaseHas('tasks', [
        'title' => 'Test Task',
        'status' => 'ToDo',
    ]);
});

test('a task can be updated', function () {
    $task = Task::factory()->create([
        'title' => 'Old Task',
        'status' => 'ToDo',
    ]);

    $task->update([
        'title' => 'Updated Task',
        'status' => 'Done',
    ]);

    $this->assertDatabaseHas('tasks', [
        'title' => 'Updated Task',
        'status' => 'Done',
    ]);
});

test('a task can be deleted', function () {
    $task = Task::factory()->create([
        'title' => 'Task to be deleted',
        'status' => 'ToDo',
    ]);

    $task->delete();

    $this->assertDatabaseMissing('tasks', [
        'title' => 'Task to be deleted',
        'status' => 'ToDo',
    ]);
});
