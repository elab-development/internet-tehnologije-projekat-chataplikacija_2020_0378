<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Events\GroupDeleted;
use App\Models\Group;

class DeleteGroupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Group $group)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $id = $this->group->id;
        $name = $this->group->name;

        $this->group->last_message_id = null;
        $this->group->save();

        //Prolazak kroz sve poruke i njihovo brisanje
        $this->group->messages->each->delete();

        //Uklanjanje svih korisnika iz grupe
        $this->group->users()->detach();

        //Brisanje grupe
        $this->group->delete();

        //dd('Group deleted', $this->group);

        GroupDeleted::dispatch($id, $name);
    }
}
