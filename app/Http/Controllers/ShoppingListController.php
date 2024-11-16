<?php

namespace App\Http\Controllers;

use App\Models\ShoppingList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class ShoppingListController extends Controller
{
    public function store(Request $request)
    {
        foreach ($request->all() as $task) {
            ShoppingList::create($task);
        }

        return Redirect::back();
    }

    public function update(Request $request, $id)
    {
        $shoppingList = ShoppingList::findOrFail($id);
        $shoppingList->update($request->only(['title', 'completed']));

        return Redirect::back();
    }

    public function destroy($id)
    {
        ShoppingList::destroy($id);

        return Redirect::back();
    }
}
