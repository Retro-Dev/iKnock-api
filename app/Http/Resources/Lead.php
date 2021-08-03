<?php

namespace App\Http\Resources;

use App\Models\Status;
use App\Models\Type;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Config;

class Lead extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $query['query'] = [];
        $is_query = (is_numeric(\Request::segment(3))) ? true : false;
        $query = ($is_query)? \App\Models\LeadQuery::getByLeadId($this->id, $this->tenantQuery) : [];
        $query_summary = (isset($query['summary'])) ? $query['summary'] : [];
        $query_appointment = (isset($query['appointment'])) ? $query['appointment'] : [];

        $address = (empty($this->zip_code))? $this->address : $this->address .', ' . $this->zip_code;
        $address = (empty($this->city))? $address : $address .', ' . $this->city;
        //$address = (empty($this->formatted_address))? $address : $this->formatted_address;

        $media_map = [[
            'id' => 1,
            'media_type' => 'image',
            'path' => env('BASE_URL').Config::get('constants.MEDIA_IMAGE_PATH').'5d3a973ece99b88a4f80d33539c08a67.jpeg',
            'thumb' => env('BASE_URL').Config::get('constants.MEDIA_IMAGE_PATH').'5d3a973ece99b88a4f80d33539c08a67.jpeg',
        ]];

        $owner = (empty($this->owner)) ? '' : $this->owner;
        $owner = explode(' ', $owner);
        $first_name = $owner[0];
        unset($owner[0]);
        $last_name = implode(' ', $owner);

        $response = [
            'id' => $this->id,
            'title' => $this->title,
            'owner' => "$first_name $last_name",
            'first_name' => $first_name,
            'last_name' => $last_name,
            'creator_id' => $this->creator_id,
            'assignee' => (!empty($this->assignee_id))?new User(\App\Models\User::getById($this->assignee_id)) : (object)[],
            'address' => $address,
            'city' => $this->city,
            'county' => $this->county,
            'foreclosure_date' => $this->foreclosure_date,
            'admin_notes' => $this->admin_notes,
            'state' => $this->state,
            'zip_code' => $this->zip_code,
            'appointment_date' => (empty($this->appointment_date))? '' : date('m-d-Y', strtotime($this->appointment_date)),
            'appointment_end_date' => (empty($this->appointment_end_date))? '' : date('m-d-Y', strtotime($this->appointment_end_date)),
            'appointment_result' => (!empty($this->appointment_result)) ? $this->appointment_result : [],
            //'query' => LeadQuery::collection($this->tenantQuery),
            'query_summary' => $query_summary,
            'query_appointment' => $query_appointment,
            'coordinate' => ['latitude' => floatval($this->latitude),
                        'longitude' => floatval($this->longitude)],
            'custom' =>  $this->leadCustom,
            'is_verfied' =>  (isset($this->is_verified))? ($this->is_verified == 1)? true : false : false,
            'is_expired' =>  $this->is_expired,
            'status' =>  new \App\Http\Resources\Status($this->leadStatus),
            'lead_status' => (isset($this->leadStatus->title))? $this->leadStatus->title : '',
            'type' => new \App\Http\Resources\Type($this->leadType),
            'media' => ($this->leadMedia->count())? Media::collection($this->leadMedia) : $media_map,
            //'media' => $media_map,

            'created_at' => date('m-d-Y', strtotime($this->created_at)),
            'updated_at' => date('m-d-Y H:i', strtotime($this->updated_at))
        ];


        if(isset($this->lead_type)){
            $response['lead_type'] = $this->lead_type;
        }

        if(in_array($request['call_mode'],['admin', 'web'])){
            //$response['updated_by'] = (!empty($this->assignee_id))?new User(\App\Models\LeadHistory::getLastHistoryByLeadId($this->id)) : (object)[];
            $response['updated_by'] = new User(\App\Models\LeadHistory::getLastHistoryByLeadId(['lead_id' => $this->id]));

            $response['custom'] = [];
            foreach ($this->leadCustom as $field) {
                $field['value'] = str_replace(["'"],['&#039;'], $field['value']);
                $field['key'] = str_replace(["'"],['&#039;'], $field['key']);
                $field['key'] = str_replace(Config::get('constants.SPECIAL_CHARACTERS.IGNORE'), Config::get('constants.SPECIAL_CHARACTERS.REPLACE'), $field['key']);
                $response[$field['key']] = $field['value'];
                //$response['custom'][] = json_decode(json_encode($field));
            }

        }

        return $response;
    }
}
