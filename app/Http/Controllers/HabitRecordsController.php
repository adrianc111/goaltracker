<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use App\Models\HabitRecord;
use Illuminate\Http\Request;

class HabitRecordsController extends Controller
{
    public function update(Request $request, Habit $habit)
    {
        $request->validate([
            'date' => 'required|date',
            'status' => 'required|boolean',
        ]);

        $record = HabitRecord::updateOrCreate(
            ['habit_id' => $habit->id, 'date' => $request->date],
            ['status' => $request->status]
        );

        return response()->json($record);
    }
}
