<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TaskStatusEnum;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
final class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $createdAt = $this->faker->dateTimeBetween('-1 year', 'now');
        $updatedAt = $this->faker->dateTimeBetween($createdAt, 'now');
        $status = $this->faker->randomElement(TaskStatusEnum::toArray());
        $completedAt = $status === TaskStatusEnum::DONE->value ? $this->faker->dateTimeBetween($updatedAt, '+1 year') : null;

        return [
            'title' => $this->faker->sentence,
            'status' => $status,
            'time_spent' => $this->faker->numberBetween(0, 1000),
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
            'completed_at' => $completedAt,
        ];
    }
}
