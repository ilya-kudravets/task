<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\TaskStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'StoreTaskRequest',
    required: ['title', 'status'],
    properties: [
        new OA\Property(property: 'title', type: 'string', maxLength: 255),
        new OA\Property(property: 'status', type: 'string', enum: TaskStatusEnum::class), // Replace with actual enum values
        new OA\Property(property: 'completed_at', type: 'string', format: 'date-time', nullable: true),
    ]
)]
final class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'status' => 'required|in:'.implode(',', TaskStatusEnum::toArray()),
            'completed_at' => 'nullable|date|after:'.now().'|after:'.now(),
        ];
    }

    protected function withValidator(Validator $validator): void
    {
        $validator->sometimes('completed_at', 'required|date|after:created_at|after:updated_at', function ($input) {
            return $input->status === TaskStatusEnum::DONE->value;
        });
    }
}
