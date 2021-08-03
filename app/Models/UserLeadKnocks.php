<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLeadKnocks extends Model
{
    protected $table = "user_lead_knocks";

    public static function getById($id){

        $query = self::select();
        return $query->where('id', $id)
            ->first();
    }

    public static function getList($tenant_id){

        $query = self::select();
        return $query->where('tenant_id', $tenant_id)
            ->whereNull('deleted_at')
            ->orderBy('title', 'asc')
            ->get();
    }

    public static function insertLeadKnocks($params)
    {
        \DB::statement("INSERT INTO user_lead_knocks (`id`, user_id, lead_id, status_id, created_at) VALUES 
            (NULL, {$params['user_id']}, {$params['lead_id']}, {$params['status_id']}, NOW())");

        return;
    }
}
