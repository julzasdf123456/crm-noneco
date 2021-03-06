<?php

namespace Database\Factories;

use App\Models\ServiceConnectionLgLoadInsp;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceConnectionLgLoadInspFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ServiceConnectionLgLoadInsp::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'ServiceConnectionId' => $this->faker->word,
        'Assessment' => $this->faker->word,
        'DateOfInspection' => $this->faker->word,
        'Notes' => $this->faker->word,
        'created_at' => $this->faker->date('Y-m-d H:i:s'),
        'updated_at' => $this->faker->date('Y-m-d H:i:s')
        ];
    }
}
