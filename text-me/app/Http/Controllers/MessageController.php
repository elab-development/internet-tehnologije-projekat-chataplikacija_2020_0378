<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Message;
use App\Http\Resources\MessageResource;
use App\Models\Group;

use App\Models\Conversation;
use App\Http\Requests\StoreMessageRequest;
use App\Models\MessageAttachment;
use App\Events\SocketMessage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MessageController extends Controller
{

    public function index(){

        $messages = Message::all();

        return $messages;

    }

    public function show($message_id){

        try {
            
            $message = new MessageResource(Message::find($message_id));

            return response()->json($message);

        } catch (\Exception $e) {
            
            return response()->json(['error' => 'Data not found'], 404);
        }






        // $message = new MessageResource(Message::find($message_id));
        
        // if (is_null($message)) {
        //     return response()->json('Data not found', 404);
        // }

       


        //return new MessageResource(Message::findOrFail($message_id));

    }

    //////////////////////////////////////////////////

    public function byUser(User $user){//Ucitavanje poruke korisnika

        $messages = Message::where('sender_id', auth()->id())
            ->where('receiver_id', $user->id)
            ->orWhere('sender_id', $user->id)
            ->where('receiver_id', auth()->id())
            ->latest()
            ->paginate(10)
        ;

        return inertia('Home', [
            'selectedConversation' => $user->toConversationArray(),
            'messages' => MessageResource::collection($messages),
        ]);

    }

    public function byGroup(Group $group){//Ucitavanje poruka grupa

        $messages = Message::where('group_id', $group->id)
            ->latest()
            ->paginate(10)
        ;

        return inertia('Home', [
            'selectedConversation' => $group->toConversationArray(),
            'messages' => MessageResource::collection($messages),

        ]);

    }

    public function loadOlder(Message $message){//Ucitavanje starijih poruka

        if ($message->group_id) {
            
            $messages = Message::where('created_at', '<' , $message->created_at)
                ->where('group_id', $message->group_id)
                ->latest()
                ->paginate(10)
            ;

        }else {
            $messages = Message::where('created_at', '<' , $message->created_at)
                ->where(function   ($query) use ($message){
                    $query->where('sender_id', $message->sender_id)
                        ->where('receiver_id', $message->receiver_id)
                        ->orWhere('sender_id', $message->receiver_id)
                        ->where('receiver_id', $message->sender_id);
                })
                ->latest()
                ->paginate(10)
            ;
        }

        return MessageResource::collection($messages);

    }

    public function store(StoreMessageRequest $request){//Cuvanje novih poruka

        $data = $request->validated();
        $data['sender_id'] = auth()->id();
        $receiverId = $data['receiver_id'] ?? null;
        $groupId = $data['group_id'] ?? null;

        $files = $data['attachments'] ?? [];

        $message = Message::create($data);

        $attachments = [];
        if ($files) {
            
            foreach ($files as $file) {

                $directory = 'attachments/' . Str::random(32);
                Storage::makeDirectory($directory);

                $model= [
                    'message_id' => $message->id,
                    'name' => $file->getClientOriginalName(),
                    'mime' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                    'path' => $file->store($directory,'public'),
                ];
                $attachment = MessageAttachment::create($model);
                $attachments[] = $attachment;
            }

            $message->attachments = $attachments;
        }

        if ($receiverId) {
            
            Conversation::updateConversationWithMessage($receiverId, auth()->id(), $message);

        }

        if ($groupId) {
            
            Group::updateGroupWithMessage($groupId, $message);
        }

        SocketMessage::dispatch($message);

        return new MessageResource($message);

    }

    public function destroy(Message $message){//Brisanje poruka

        //Provera da je user poslao poruku
        if ($message->sender_id !== auth()->id()) {
            
            return response()->json(['message'=>'Forbidden'], 403);
        }

        $group=null;
        $conversation=null;

        //proveriti da li je poruka poruka grupe
        if ($message->group_id) {
            $group = Group::where('last_message_id', $message->id)->first();

        }  else {
            $conversation = Conversation::where('last_message_id', $message->id)->first();

        }
        

        $message->delete();

        if ($group) {
            //moramo da $group updateujemo sa poslednjim podacima iz baze
            $group = Group::find($group->id);
           $lastMessage = $group->lastMessage;
        }else if($conversation) {
            $conversation = Conversation::find($conversation->id);
            $lastMessage = $conversation->lastMessage;
        }

        //return response('', 204);

        return response()->json(['message' => $lastMessage ? new MessageResource($lastMessage) : null]);

    }
    


}
