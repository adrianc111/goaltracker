<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class DocumentController extends Controller
{
    public function index() {
        return Inertia::render('Documents/Index', [
            'documents' => Document::orderBy('created_at', 'desc')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|nullable|string',
        ]);

        $document = Document::create($request->all());

        return response()->json($document);
    }

    /**
     * Update a goal
     * @param Request $request
     * @param $id
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|nullable|string',
        ]);

        $document = Document::findOrFail($id);
        $document->update($request->only(['title', 'content']));
    }

    public function destroy($id)
    {
        Document::destroy($id);

        return Redirect::back();
    }
}
