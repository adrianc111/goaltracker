<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NoteResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'goal_id'        => $this->goal_id,
            'week_reference' => $this->week_reference,
            'content'        => $this->content,
            'goal'           => new GoalResource($this->whenLoaded('goal')),
        ];
    }
}
