<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    protected $table = "type";

    public static function getById($id){

        $query = self::select();
        return $query->where('id', $id)
            ->first();
    }

    public static function getByMax($company_id){

        $query = self::select();
        return $query->where('tenant_id', $company_id)
            ->orderBy('id', 'desc')
            ->first();
    }

    public static function getList($tenant_id){

        $query = self::select();
        return $query->where('tenant_id', $tenant_id)
            ->whereNull('deleted_at')
            ->orderBy('title', 'asc')
            ->get();
    }

    public static function createTenantType($company_id)
    {
        \DB::statement("INSERT INTO `type` (`code`, title, tenant_id, is_permanent, created_at, updated_at) 
            SELECT `code`, title, $company_id, is_permanent, NOW(), NOW() FROM `type` WHERE tenant_id = 0 AND deleted_at IS NULL");

        return;
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

    public static function deleteType($id)
    {
        if(is_array($id))
            $id = implode(',', $id);

        \DB::statement("Update type SET deleted_at = NOW() WHERE id IN ($id)");
        return true;
    }

}
