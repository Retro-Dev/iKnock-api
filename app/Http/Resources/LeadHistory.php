<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class LeadHistory extends Resource
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
            'owner' => (empty($this->owner)) ? '' : $this->owner,
            'creator_id' => $this->creator_id,
            'address' => $this->address,
            'assign_id' => $this->assign_id,
            'assign' => new User((\App\Models\User::getById($this->assign_id))),
            'query' =>  [],
            'custom' =>  [],
            //'status' =>  new Status(\App\Models\Status::getById($this->status_id)), //new \App\Http\Resources\Status($this->leadStatus),
            'status' =>  new \App\Http\Resources\Status($this->leadStatus),
            'type' => new \App\Http\Resources\Type($this->leadType),
            'media' => Media::collection($this->leadMedia),
            //'created_at' => $this->created_at->diffForHumans()
            'created_at' => date('m-d-Y H:i A', strtotime($this->created_at))
        ];
        if(!empty($this->leadStatus) && !empty($this->lead_history_title)){
            $this->leadStatus->title = $this->lead_history_title;
        }
        if(empty($this->leadStatus)){
            $response['status'] = [
                'id' => $this->id,
                'title' => $this->lead_history_title,
                'code' => $this->code,
                'lead_count' => 0,
                'lead_percentage' => 0,
                'color_code' => '#00FF00'
            ];
        }
        return $response;
    }
}
