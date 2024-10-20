<?php

namespace App\Http\Resources\api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $receiver = User::findOrFail($this->to);
        return [
            "id" => $this->id,
            "name" => $this?->name ?? 'Unknown',
            "message" =>
                [
                    "message" => $this?->message,
                    "type" => $this?->type,
                    "sender_id" => $this?->from,
                    "receiver_id" => $this?->to,
                    "message_time" => $this?->created_at->since(),
                    "read_at" => $this?->read_at
                ],

        ];
    }
}
