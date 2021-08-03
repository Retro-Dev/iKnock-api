<?php

namespace  App\Libraries;

//defined('BASEPATH') OR exit('No direct script access allowed');

//require_once 'braintree_sdk/lib/Braintree.php';

use App\Models\LeadHistory;
use App\Models\Type;
use App\Models\User;
use Illuminate\Support\Facades\Config;

class History
{
    public $history_trigger_map, $history_trigger_prefx;
    private $old_node,$new_node,$_bulk_query;


    function __construct()
    {
        $this->_bulk_query = [];
    }

    public function initiate($old_data, $new_data)
    {
        $this->history_trigger_prefx = strtolower($this->history_trigger_prefx);
        $this->old_node = $old_data;
        $this->new_node = $new_data;
        foreach($this->history_trigger_map as $trigger){
            $fn = $this->history_trigger_prefx . ucfirst($trigger);
            $this->$fn();
        }
    }

    private function leadAddress()
    {
        $address = explode(',', $this->new_node['address']);
        $this->new_node['address'] = (isset($address[0])) ? $address[0] : $address;
        if($this->old_node['address'] != $this->new_node['address']) {
            $lead_id = $this->new_node['id'];
            $user_id = $this->new_node['user_id'];

            $obj_lead_history = LeadHistory::create([
                'lead_id' => $lead_id,
                'title' => "Address \"{$this->old_node['address']}, {$this->old_node['zip_code']}, {$this->old_node['city']}\" updated",
                'assign_id' => $user_id,
                'status_id' => 0
            ]);
        }


    }

    private function leadTitle()
    {
        if($this->old_node['title'] != $this->new_node['title']) {
            $lead_id = $this->new_node['id'];
            $user_id = $this->new_node['user_id'];

            $obj_lead_history = LeadHistory::create([
                'lead_id' => $lead_id,
                'title' => Config::get('constants.LEAD_TITLE_DISPLAY')." \"{$this->old_node['title']}\" updated",
                'assign_id' => $user_id,
                'status_id' => 0
            ]);
        }


    }

    private function leadAssignee()
    {
        if($this->old_node['assignee_id'] != $this->new_node['target_id'] && !empty($this->old_node['assignee_id'])) {
            $lead_id = $this->new_node['id'];
            $user_id = $this->new_node['user_id'];

            $lead_assignee = User::getById($this->old_node['assignee_id']);
            $lead_assignee_name = "{$lead_assignee->first_name} {$lead_assignee->last_name}";

            $obj_lead_history = LeadHistory::create([
                'lead_id' => $lead_id,
                'title' => "Agent \"$lead_assignee_name\" updated",
                'assign_id' => $user_id,
                'status_id' => 0
            ]);
        }


    }

    private function leadType()
    {
        if($this->old_node['type_id'] != $this->new_node['type_id']) {
            $lead_id = $this->new_node['id'];
            $user_id = $this->new_node['user_id'];

            $lead_type = Type::getById($this->old_node['type_id']);
            $lead_type_name = "{$lead_type->title}";

            $obj_lead_history = LeadHistory::create([
                'lead_id' => $lead_id,
                'title' => "Lead type \"$lead_type_name\" updated",
                'assign_id' => $user_id,
                'status_id' => 0
            ]);
        }


    }

    private function leadExpired()
    {
        if($this->old_node['is_expired'] != $this->new_node['is_expired']) {
            $lead_id = $this->new_node['id'];
            $user_id = $this->new_node['user_id'];

            $lead_expired = ($this->old_node['is_expired'])? 'enabled' : 'disabled';

            $obj_lead_history = LeadHistory::create([
                'lead_id' => $lead_id,
                'title' => "Lead expiry \"$lead_expired\"",
                'assign_id' => $user_id,
                'status_id' => 0
            ]);
        }


    }

    private function leadStatus()
    {
        if($this->old_node['status_id'] != $this->new_node['status_id']) {
            $lead_id = $this->new_node['id'];
            $user_id = $this->new_node['user_id'];

            $obj_lead_history = LeadHistory::create([
                'lead_id' => $lead_id,
                'title' => '',
                'assign_id' => $user_id,
                'status_id' => $this->new_node['status_id'] //$this->old_node['status_id']
            ]);
        }


    }


    private function leadBulkExpired()
    {
        if($this->old_node['is_expired'] != $this->new_node['is_expired']) {
            $lead_id = $this->new_node['id'];
            $user_id = $this->new_node['user_id'];

            $lead_expired = ($this->old_node['is_expired'])? 'enabled' : 'disabled';
            $this->_bulk_query[] = "($lead_id, 'Lead expiry \"$lead_expired\"', $user_id, 0, NOW())"; //$this->new_node['status_id']
        }


    }

    private function leadBulkAssignee()
    {
        if($this->old_node['assignee_id'] != $this->new_node['target_id'] && !empty($this->old_node['assignee_id'])) {
            $lead_id = $this->new_node['id'];
            $user_id = $this->new_node['user_id'];

            $lead_assignee = User::getById($this->old_node['assignee_id']);
            $lead_assignee_name = "{$lead_assignee->first_name} {$lead_assignee->last_name}";

            $this->_bulk_query[] = "($lead_id, 'Agent \"$lead_assignee_name\" updated', $user_id, 0, NOW())";
        }


    }

    private function leadBulkStatus()
    {
        if($this->old_node['status_id'] != $this->new_node['status_id']) {
            $lead_id = $this->new_node['id'];
            $user_id = $this->new_node['user_id'];

            $this->_bulk_query[] = "($lead_id, '', $user_id, {$this->old_node['status_id']}, NOW())"; //$this->new_node['status_id']
        }
    }

    public function bulkExecute()
    {
        if(count($this->_bulk_query)) {
            \DB::statement("INSERT INTO lead_history (lead_id, title, assign_id, status_id, created_at) VALUES " . implode(',', $this->_bulk_query));
        }
    }

}
