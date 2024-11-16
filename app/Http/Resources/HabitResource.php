<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HabitResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $weekDays = [];
        for ($i = 0; $i < 7; $i++) {
            $date = now()->startOfWeek()->addDays($i)->format('Y-m-d'); // @todo: fix
            $weekDays[] = [
                'date'   => $date,
                'status' => false,
            ];
        }

        return [
            'id'    => $this->id,
            'name'  => $this->name,
            //            'frequency'  => $this->frequency,
            //            'order'      => $this->order,
            'group' => $this->group,
            //            'archived'   => $this->archived,
            //            'start_date' => $this->start_date,
            //            'end_date'   => $this->end_date,
            'days'  => $weekDays,
        ];
    }
}
