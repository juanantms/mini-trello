<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Tarea;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tarea>
 */
class TareaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Tarea>
     */
    protected $model = Tarea::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = [Tarea::STATUS_PENDING, Tarea::STATUS_IN_PROGRESS, Tarea::STATUS_COMPLETED];

        return [
            'titulo' => fake()->sentence(5),
            'descripcion' => fake()->paragraph(),
            'estado' => fake()->randomElement($statuses),
            'user_id' => User::factory(),
        ];
    }

    /**
     * Indicate that the task is pending.
     *
     * @return Factory<Tarea>
     */
    public function pendiente(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'estado' => Tarea::STATUS_PENDING,
        ]);
    }

    /**
     * Indicate that the task is in progress.
     *
     * @return Factory<Tarea>
     */
    public function enProgreso(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'estado' => Tarea::STATUS_IN_PROGRESS,
        ]);
    }

    /**
     * Indicate that the task is completed.
     *
     * @return Factory<Tarea>
     */
    public function completada(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'estado' => Tarea::STATUS_COMPLETED,
        ]);
    }
}
