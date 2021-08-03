<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class Status extends Resource
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
            'code' => $this->code,
            'lead_count' => $this->lead_count,
            'lead_percentage' => $this->lead_percentage,
            'color_code' => (empty($this->color_code)) ? '#00FF00': $this->color_code
        ];

        return $response;
    }
}
