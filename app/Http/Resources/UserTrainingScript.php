<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class UserTrainingScript extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $response = [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'media' => Media::collection(\App\Models\Media::getBySourceType($this->id, 'training')),
            'created_at' => date('m-d-Y', strtotime($this->created_at))
        ];

        return $response;
    }
}
