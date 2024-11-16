<?php

namespace App\Http\Controllers;

use App\Http\Resources\ShoppingListResource;
use App\Http\Services\HabitService;
use App\Http\Services\TaskService;
use App\Models\ShoppingList;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function dashboard(): Response
    {
        $shoppingList = ShoppingList::where('completed', false)
            ->orderBy('created_at', 'DESC')
            ->get();

        return Inertia::render('Dashboard/Index', [
            'today'        => TaskService::getTasksForToday(),
            'habits'       => HabitService::getHabits(now()->startOfWeek(), now()->endOfWeek()),
            'shoppingList' => ShoppingListResource::collection($shoppingList),
            //            'notes' => $goals,
        ]);
    }
}
