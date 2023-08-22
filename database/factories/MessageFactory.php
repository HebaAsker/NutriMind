<?php

namespace Database\Factories;

use App\Models\Chat;
use App\Models\Doctor;
use App\Models\Message;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{


    protected $model = Message::class;

    public function definition(): array
    {
        return [
            'chat_id' => Chat::all()->random()->id,
            'status' => fake()->randomElement(['read','unread']),
            'content' => fake()->sentence(10),
            'sender_name' => Doctor::all()->random()->name,
            'receiver_name' => Patient::all()->random()->name,

        ];
    }
}
