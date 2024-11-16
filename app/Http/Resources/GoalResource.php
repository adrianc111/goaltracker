<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GoalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'completed'   => $this->completed,
            'due_date'    => $this->due_date ? $this->due_date->format('Y-m-d'): null,
            'year'        => $this->year,
            'tasks'       => TaskResource::collection($this->whenLoaded('tasks')),
            'notes'       => NoteResource::collection($this->whenLoaded('notes')),
            'url'         => url("/goals/$this->id"), // Generate URL with goal ID
        ];
    }
}
