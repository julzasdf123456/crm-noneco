<?php

namespace Database\Factories;

use App\Models\DemandLetters;
use Illuminate\Database\Eloquent\Factories\Factory;

class DemandLettersFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DemandLetters::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'AccountNumber' => $this->faker->word,
        'UserId' => $this->faker->word,
        'Status' => $this->faker->word,
        'DateSent' => $this->faker->word,
        'Notes' => $this->faker->word,
        'created_at' => $this->faker->date('Y-m-d H:i:s'),
        'updated_at' => $this->faker->date('Y-m-d H:i:s')
        ];
    }
}
