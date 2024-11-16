<?php

namespace App\Http\Controllers;

use App\Http\Resources\WaitingListResource;
use App\Models\WaitingList;
use Illuminate\Http\Request;

class WaitingListController extends Controller
{
    public function index()
    {
        return WaitingListResource::collection(WaitingList::all());
    }

    public function store(Request $request)
    {
        $waitingList = WaitingList::create($request->all());
        return new WaitingListResource($waitingList);
    }

    public function show($id)
    {
        $waitingList = WaitingList::findOrFail($id);
        return new WaitingListResource($waitingList);
    }

    public function update(Request $request, $id)
    {
        $waitingList = WaitingList::findOrFail($id);
        $waitingList->update($request->only(['title', 'completed']));
        return new WaitingListResource($waitingList);
    }

    public function destroy($id)
    {
        WaitingList::destroy($id);
        return response()->json(null, 204);
    }
}
