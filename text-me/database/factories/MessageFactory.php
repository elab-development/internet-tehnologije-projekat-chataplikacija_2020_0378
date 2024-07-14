<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $senderId = $this->faker->randomElement([0,1]);// sender id moze uzeti vrednosti 0 ili 1
        if ($senderId === 0) {
            $senderId = $this->faker->randomElement(\App\Models\User::where('id','!=',1)->pluck('id')->toArray());//uzima random id ali da nije 1 zbog receiver id 
            $receiverId = 1;
        }else {
            $receiverId = $this->faker->randomElement(\App\Models\User::pluck('id')->toArray());
        }

        $groupId = null; 
        if ($this->faker->boolean(50)) { //ima 50% sanse da bude true
            $groupId = $this->faker->randomElement(\App\Models\Group::pluck('id')->toArray());
            // ako je group id selaktovan onda receiver id mora biti null
            $group = \App\Models\Group::find($groupId);
            $senderId = $this->faker->randomElement($group->users->pluck('id')->toArray());//sender id mora biti od nekog usera iz grupe
            $receiverId = null;
        }

        return [
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'group_id' => $groupId,
            'message' => $this->faker->realText(200),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'), //generise poruku koja ce biti datuma izmedju sadasnjeg trenutka i jedna godine pre
        ];
    }
}
