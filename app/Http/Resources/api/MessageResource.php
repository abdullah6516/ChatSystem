<?php

namespace App\Http\Resources\api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'sender_id' => $this?->from,
            "receiver_id" => $this?->to,
            "message_id" => $this->id,
            "message" => $this->message,
            "type" => $this->type,
            "message_time" => $this->created_at->since(),
            "read_at" => $this?->read_at
        ];
    }
}
