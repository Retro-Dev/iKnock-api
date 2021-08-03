<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class TenantQuery extends Resource
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
            'query' => ucfirst($this->query),
            'tenant_id' => $this->tenant_id,
            'type' => $this->type,
            ];

        return $response;
    }
}
