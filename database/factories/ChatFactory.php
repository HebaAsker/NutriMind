<?php

namespace Database\Factories;

use App\Models\Chat;
use App\Models\Doctor;
use App\Models\Message;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatFactory extends Factory
{


    protected $model = Chat::class;

    public function definition(): array
    {
        return [
            'last_seen' => fake()->date('Y-m-d H:i:s'),
            'sender_name' => Doctor::all()->random()->name,
            'receiver_name' => Patient::all()->random()->name,
        ];
    }
}
