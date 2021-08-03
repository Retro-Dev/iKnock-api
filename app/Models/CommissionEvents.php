<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionEvents extends Model
{
    protected $table = "commission_events";

    public static function getList($params){
        $query = self::select('commission_events.*');
        return $query->whereIn('tenant_id', [$params['company_id']])
            ->whereNull('deleted_at')
            ->get();
    }


    public static function createTenantCommissionEvents($company_id)
    {
        \DB::statement("INSERT INTO `commission_events` (title, tenant_id, is_permanent, created_at, updated_at) 
            SELECT title, $company_id, is_permanent, NOW(), NOW() FROM `commission_events` WHERE tenant_id = 0 AND deleted_at IS NULL");

        return;
    }

}
