<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class Type extends Resource
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
            'code' => ucfirst($this->code), //ucfirst($this->title[0] . $this->title[1]),
            'title' => $this->title
        ];

        return $response;
    }
}
