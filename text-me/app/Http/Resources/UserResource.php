<?php

namespace App\Http\Resources;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public static $wrap = false; //uklanjamo json kljuc 
    //mozda je i null

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //avatar je putanja gde je uploadovan korisnikov avatar
        return [
            'id' => $this->id,
            'avatar_url' => $this->avatar ? Storage::url($this->avatar) : null,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'is_admin' => (bool) $this->is_admin,
            'last_message' => $this->last_message, //last msg i last msg date ne postoje u tabeli user vec su to dinamicke promenljive koje ce biti dodate kasnije
            'last_message_date' => $this->last_message_date,
        ];
    }
}
