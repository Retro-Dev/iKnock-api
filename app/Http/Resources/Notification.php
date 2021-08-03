<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class Notification extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'admin_id' => Admin::collection(\App\Models\Admin::getById($this->admin_id)),
            'user_id' => User::collection(\App\Models\User::getById($this->user_id)),
            'title' => $this->title,
            'description' => $this->description,
            'created_at' => date('m-d-Y', strtotime($this->created_at)),
        ];
    }
}
