<?php

namespace App\Http\Controllers;

use App\Http\Resources\HabitResource;
use App\Http\Services\HabitService;
use App\Models\Habit;
use App\Models\HabitRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class HabitController extends Controller
{
    /**
     * Store a new habit.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'frequency'  => 'nullable|integer',
            'order'      => 'integer',
            'group'      => 'in:morning,day,evening,uncategorised',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date',
        ]);

        $habit = Habit::create([
            'name'       => $request->name,
            'user_id'    => Auth::id(),
            'frequency'  => $request->frequency ?? HabitService::FREQUENCY_DAILY,
            'order'      => $request->order ?? 0,
            'group'      => $request->group ?? HabitService::GROUP_ITEM_DEFAULT,
            'start_date' => $request->start_date ?? Carbon::today(),
            'end_date'   => $request->end_date,
        ]);

        return Redirect::back();
    }

    /**
     * Update a habit's name or status.
     */
    public function update(Request $request, Habit $habit)
    {
        if ($request->has('name')) {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $habit->update(['name' => $request->name]);
        }

        if ($request->has('archived')) {
            $request->validate([
                'archived' => 'required|boolean',
            ]);

            $habit->update(['archived' => $request->archived]);
        }

        if ($request->has('group')) {
            $request->validate([
                'group' => 'in:morning,day,evening,uncategorised',
            ]);

            $habit->update(['group' => $request->group]);
        }

        if ($request->has('date') && $request->has('status')) {
            // Update the status for a specific day
            $request->validate([
                'date'   => 'required|date',
                'status' => 'required|boolean',
            ]);

            $record = HabitRecord::firstOrCreate([
                'habit_id' => $habit->id,
                'date'     => $request->date,
            ]);

            $record->status = $request->status;
            $record->save();
        }

        return Redirect::back();
    }

    public function destroy($id)
    {
        Habit::destroy($id);

        return Redirect::back();
    }
}
