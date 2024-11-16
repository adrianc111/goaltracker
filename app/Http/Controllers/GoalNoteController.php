<?php

namespace App\Http\Controllers;

use App\Http\Resources\NoteResource;
use App\Models\Goal;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class GoalNoteController extends Controller
{
    public function update(Request $request, Goal $goal, Note $note)
    {
        // Ensure the note belongs to the goal
        if ($note->goal_id !== $goal->id) {
            return response()->json(['error' => 'Note does not belong to this goal'], 403);
        }

        // Validate the request
        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        // Update the note
        $note->update([
            'content' => $validated['content'],
        ]);

        return Redirect::back();
    }
}
