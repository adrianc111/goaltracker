<?php

namespace App\Http\Services;

use App\Http\Resources\NoteResource;
use App\Models\Note;
use App\Models\Task;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class NoteService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function notesForWeek($week)
    {
        $notes = Note::forWeek($week)->get();
        return NoteResource::collection($notes);
    }
}
