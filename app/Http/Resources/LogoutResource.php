<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LogoutResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [];
    }

    /**
     * Customize the outgoing response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\JsonResponse  $response
     * @return void
     */
    public function with($request)
    {
        return [
            'status' => 'success',
            'message' => $this->message ?? 'Logged out successfully',
        ];
    }
}