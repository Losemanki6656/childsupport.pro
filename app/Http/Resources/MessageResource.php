<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'organization' => $this->organization,
            'fullname' => $this->fullname,
            'comment' => $this->comment,
            'result' => $this->result,
            'comment_result' => $this->comment_result,
            'created_at' => $this->created_at,
        ];
    }
}
