<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TaskStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Task',
    required: ['title', 'status'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', readOnly: true),
        new OA\Property(property: 'title', type: 'string', maxLength: 255),
        new OA\Property(property: 'status', type: 'string', enum: TaskStatusEnum::class),
        new OA\Property(property: 'time_spent', type: 'integer', nullable: true),
        new OA\Property(property: 'completed_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', readOnly: true),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', readOnly: true),
    ],
    type: 'object'
)]
final class Task extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'status', 'time_spent', 'completed_at'];
}
