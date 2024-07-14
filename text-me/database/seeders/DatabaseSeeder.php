<?php

namespace Database\Seeders;
use Carbon\Carbon;
use App\Models\Message;
use App\Models\Conversation;
use App\Models\Group;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //Generisanje dva usera
        User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'is_admin' => true
        ]);
        User::factory()->create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => bcrypt('password'),
        ]);
        //Generisanje 10 random usera
        User::factory(10)->create();

        //Generisanje 5 grupa
        for ($i=0; $i < 5; $i++) { 
            
            $group = Group::factory()->create([
                'owner_id' => 1,
            ]);

            //Unutar grupe biramo od 2 do 5 random usera
            $users = User::inRandomOrder()->limit(rand(2,5))->pluck('id');
            $group->users()->attach(array_unique([1, ...$users]));//Ubacivanje od 2 do 5 RAZLICITIH('id') usera u grupu

        }

        //Generisanje poruka
        Message::factory(1000)->create();
        $messages = Message::whereNull('group_id')->orderBy('created_at')->get();// U messages stavljamo sve poruke koje nisu iz grupa (group_id == null)

        $conversations = $messages->groupBy(function ($message){ //grupisanje poruka u konverzacije na osnovu sender id i receiver id
            return collect([$message->sender_id, $message->receiver_id])->sort()->implode('_');// implode sluzi da kljuc konverzacije bude u formatu senderid_receiverid

        })->map(function($groupedMessages){
            return [// Vraca sender id i receiver id kao i id poslednje poruke konverzacije i ubacuje u tabelu konverzacije
                'user_id1' => $groupedMessages->first()->sender_id,
                'user_id2' => $groupedMessages->first()->receiver_id,
                'last_message_id' => $groupedMessages->last()->id,
                'created_at' => new Carbon(),
                'updated_at' => new Carbon(),
            ];
        })->values();

        Conversation::insertOrIgnore($conversations->toArray());

    }
}
