<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class LeadQuery extends Resource
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
            'id' => empty($this->id) ? '' : $this->id,
            'type' => $this->type,
            'query' => $this->query,
            'response' => empty($this->response) ? '' : $this->response,
            'created_at' => date('m-d-Y', strtotime($this->created_at))
        ];

        return $response;
    }
}
