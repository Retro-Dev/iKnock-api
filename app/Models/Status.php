<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $table = "status";

    public static function getById($id){

        $query = self::select();
        return $query->where('id', $id)
            ->first();
    }

    public static function getByCode($id = 0, $code, $tenant_id){

        $query = self::select();
        if(!empty($id))
            $query->where('id', '!=', $id);
        return $query->where('code', $code)
            ->where('tenant_id', $tenant_id)
            ->whereNull('deleted_at')
            ->count();
    }

    public static function getFirstTenantStatus($tenant_id)
    {

        $query = self::select();
        $result = $query->where('tenant_id', $tenant_id)
            ->orderBy('id')
            ->where('is_permanent', 1)
            ->whereNull('deleted_at')
            ->first();

        if (isset($result['id']))
            return $result['id'];

        $query = self::select();
        $result = $query->where('tenant_id', $tenant_id)
            ->orderBy('id')
            ->whereNull('deleted_at')
            ->first();

        return $result['id'];
        return 0;
    }


    public static function getList($params){
        $query = self::select('status.*',\DB::raw( "0 as lead_percentage"));
        return $query->whereIn('tenant_id', [$params['company_id']])
            ->whereNull('deleted_at')
            ->orderBy('order_by')
            ->get();
    }

    public static function incrementLeadCount($status_id){
        \DB::table('status')
            ->where('id',$status_id)
            ->increment('lead_count');
    }

    public static function decrementLeadCount($status_id, $count = 1){
        \DB::table('status')
            ->where('id',$status_id)
            ->decrement('lead_count', $count);
    }

    public static function createTenantStatus($company_id)
    {
        \DB::statement("INSERT INTO status (`code`, title, color_code, lead_count, tenant_id, is_permanent, created_at, updated_at) 
            SELECT `code`, title, color_code, 0, $company_id, is_permanent, NOW(), NOW() FROM status WHERE tenant_id = 0 AND deleted_at IS NULL");

        return;
    }

    public static function deleteStatus($id)
    {
        if(is_array($id))
            $id = implode(',', $id);

        \DB::statement("Update status SET deleted_at = NOW() WHERE id IN ($id)");
        return true;
    }

    public static function updateOrderBy($tenant_id, $status_id, $order_by)
    {
        \DB::statement("UPDATE status SET order_by = $order_by WHERE tenant_id = $tenant_id AND id = '$status_id'");
        return 1;
    }
}
