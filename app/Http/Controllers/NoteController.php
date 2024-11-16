<?php

namespace App\Http\Controllers;

use App\Http\Resources\NoteResource;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class NoteController extends Controller
{
    public function update(Request $request, $id)
    {
        $note = Note::findOrFail($id);
        $note->update($request->only(['content']));
    }
}
