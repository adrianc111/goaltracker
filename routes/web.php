<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\GoalNoteController;
use App\Http\Controllers\GoalTaskController;
use App\Http\Controllers\HabitController;
use App\Http\Controllers\HabitRecordsController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShoppingListController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ThisWeekController;
use App\Http\Controllers\WaitingListController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return Inertia::render('Dashboard/Welcome', [
        'canLogin'       => Route::has('login'),
        'canRegister'    => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion'     => PHP_VERSION,
    ]);
});

Route::get('/privacy-policy', function () {
    return Inertia::render('Dashboard/PrivacyPolicy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    /** Resources */
    Route::apiResource('goals', GoalController::class);
    Route::apiResource('goals.tasks', GoalTaskController::class);
    Route::apiResource('goals.notes', GoalNoteController::class);
    Route::apiResource('shopping-lists', ShoppingListController::class);
    Route::apiResource('waiting-lists', WaitingListController::class);
    Route::apiResource('tasks', TaskController::class);
    Route::apiResource('notes', NoteController::class);
    Route::apiResource('habits', HabitController::class);
    Route::apiResource('habits.records', HabitRecordsController::class);
    Route::apiResource('documents', DocumentController::class);

    Route::get('this-week', [ThisWeekController::class, 'index'])->name('this-week');

    /** Custom endpoints */
    Route::post('goals/order', [GoalController::class, 'goalReorder'])->name('goals.reorder');
    Route::post('goals/{goal}/tasks/reorder', [GoalTaskController::class, 'goalTasksReorder'])->name('goals.tasks.reorder');
    Route::post('tasks/reorder', [TaskController::class, 'tasksReorder'])->name('tasks.reorder');
});

require __DIR__ . '/auth.php';
