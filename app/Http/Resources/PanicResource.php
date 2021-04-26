<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;

class PanicResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = User::find($this->user_id);

        return [
            
                'id' => $this->id,
                'longitude' => $this->longitude,
                'latitude' => $this->latitude,
                'panic_type' => $this->panic_type,
                'details' => $this->details,
                'created_at' => $this->created_at,
                'created_by' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ]
            
        ];
    }
}
