<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Http\Requests\StoreGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\Jobs\DeleteGroupJob;
use App\Http\Resources\GroupResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */


    public function index(){

        return GroupResource::collection(Group::all());

    }

    public function show($group_id){

        $group = Group::find($group_id);

        if ($group == null) {
            
            return response()->json('Data not found', 404);
        }
        
        
        return response()->json(new GroupResource($group));

    }

    public function store(Request $request)
    {
        return response()->json('Cao', 200);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'owner_id' => 'required|integer|exists:users,id',
            'last_message_id' => 'sometimes|integer|exists:messages,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $group = Group::create($request->all());

        return new GroupResource($group);
    }

    public function update(Request $request, $id)
    {
        $group = Group::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'owner_id' => 'required|integer|exists:users,id',
            'last_message_id' => 'sometimes|integer|exists:messages,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $group->update($request->all());

        return new GroupResource($group);
    }

    public function destroy($id)
    {
        $group = Group::find($id);

        if ($group == null) {
            return response()->json(['error' => 'Data not found'], 404);
        }else {
            $group->delete();

            return response()->json(['Group has been deleted'], 200);
        }

        return response()->json('GRESKA', 204);

    }


    ///////////////////////////////////////////////////////////////////

    public function store1(StoreGroupRequest $request)
    {
        $data = $request->validated();
        $user_ids = $data['user_ids'] ?? [];
        $group = Group::create($data);
        $group->users()->attach(array_unique([$request->user()->id, ...$user_ids]));

        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update1(UpdateGroupRequest $request, Group $group)
    {
        $data = $request->validated();
        $user_ids = $data['user_ids'] ?? [];
        $group->update($data);

        //Uklanjanje svih korisnika i dodavanje novih
        $group->users()->detach();
        $group->users()->attach(array_unique([$request->user()->id, ...$user_ids]));

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy1(Group $group)
    {
        //Provera da li je vlasnik grupe
        if ($group->owner_id !== auth()->id()) {
            abort(403);
        }

        DeleteGroupJob::dispatch($group)->delay(now()->addSeconds(10));

        return response()->json(['message' => 'Group delete was scheduled and it will be deleted soon']);
    }
}
