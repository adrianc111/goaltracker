<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'goal_id'        => $this->goal_id, // @todo: probably not needed
            'parent_id'      => $this->parent_id,
            'title'          => $this->title,
            'completed'      => $this->completed,
            'due_date'       => $this->due_date ? $this->due_date->format('Y-m-d'): null,
            'week_reference' => $this->week_reference,
            'subtasks'       => TaskResource::collection($this->whenLoaded('subtasks')),
            'goal'           => new GoalResource($this->whenLoaded('goal')),
        ];
    }
}
