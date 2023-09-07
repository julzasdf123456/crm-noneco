<?php

namespace Database\Factories;

use App\Models\DemandLetterMonths;
use Illuminate\Database\Eloquent\Factories\Factory;

class DemandLetterMonthsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DemandLetterMonths::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'DemandLetterId' => $this->faker->word,
        'ServicePeriod' => $this->faker->word,
        'AccountNumber' => $this->faker->word,
        'NetAmount' => $this->faker->word,
        'Surcharge' => $this->faker->word,
        'Interest' => $this->faker->word,
        'TotalAmountDue' => $this->faker->word,
        'Notes' => $this->faker->word,
        'Status' => $this->faker->word,
        'created_at' => $this->faker->date('Y-m-d H:i:s'),
        'updated_at' => $this->faker->date('Y-m-d H:i:s')
        ];
    }
}
