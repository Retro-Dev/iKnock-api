<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class Admin extends Resource
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
            'name' => $this->first_name . ' ' . $this->last_name,
            'email' => $this->email,
            'created_at' => date('m-d-Y', strtotime($this->created_at)),
        ];
        //return parent::toArray($request);
    }
}
