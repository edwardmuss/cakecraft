<?php

namespace Database\Factories;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    protected $model = Contact::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $operators = ['safaricom', 'airtel', 'telkom', 'equitel', 'unknown'];
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->safeEmail(),
            'operator' => $this->faker->randomElement($operators),
            'phone' => $this->faker->phoneNumber,
            'field_1' => $this->faker->optional()->text(30),
            'field_2' => $this->faker->optional()->text(30),
            'field_3' => $this->faker->optional()->text(30),
            'blocked' => $this->faker->boolean,
            'branch_id' => 1,
            'created_by' => 1,
        ];
    }
}
