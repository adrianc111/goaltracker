<?php

namespace App\Http\Services;

use App\Models\Habit;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class HabitService
{
    public const FREQUENCY_DAILY = 1;
    public const FREQUENCY_WEEKLY = 2;
    public const FREQUENCY_MONTHLY = 3;
    public const FREQUENCY_YEARLY = 4;

    public const GROUP_ITEM_MORNING = 'morning';
    public const GROUP_ITEM_DAY = 'day';
    public const GROUP_ITEM_EVENING = 'evening';
    public const GROUP_ITEM_DEFAULT = 'uncategorised';
    public const GROUPS = [self::GROUP_ITEM_MORNING, self::GROUP_ITEM_DAY, self::GROUP_ITEM_EVENING, self::GROUP_ITEM_DEFAULT];

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function getHabits(
        \Illuminate\Support\Carbon|Carbon $startOfWeek,
        \Illuminate\Support\Carbon|Carbon $endOfWeek
    ): Collection|\Illuminate\Database\Eloquent\Collection {
        $habits = Habit::with([
            'records' => function ($query) use ($startOfWeek, $endOfWeek) {
                $query->whereBetween('date', [$startOfWeek, $endOfWeek]);
            },
        ])->where('user_id', Auth::id())->get();

        $today = Carbon::now()->toDateString();

        // Prepare a structure where each habit has data for all days of the week
        $habits = $habits->map(function ($habit) use ($today, $startOfWeek) {
            $weekDays = [];
            for ($i = 0; $i < 7; $i++) {
                $date = $startOfWeek->clone()->addDays($i)->format('Y-m-d');

                // Check if there is a record for this day
                $record = $habit->records->firstWhere('date', $date);
                $weekDays[] = [
                    'date'    => $date,
                    'isToday' => $date === $today,
                    'status'  => $record ? $record->status : false, // Default to false if no record
                ];
            }

            return [
                'id'    => $habit->id,
                'name'  => $habit->name,
                'group' => $habit->group,
                'archived' => $habit->archived,
                'days'  => $weekDays,
            ];
        });

        // Sort habits by custom group order
        return $habits->sortBy(function ($habit) {
            // Get the index of the group in the custom order array
            $group = strtolower($habit['group']);
            $index = array_search($group, self::GROUPS);

            // If group is not found, assign it a large number to push it to the end
            return $index === false ? count(self::GROUPS) : $index;
        })->values(); // Reset keys to avoid gaps
    }
}
