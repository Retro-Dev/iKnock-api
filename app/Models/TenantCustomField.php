<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantCustomField extends Model
{
    protected $table = "tenant_custom_field";

    public static function insertTenantDefaultFields($tenant_id, $columns){
        $order_by = 1;
        foreach ($columns as $column) {
            $statements[] = "($tenant_id, 0, '$column','$column', $order_by, NOW(), NOW())";
            $order_by++;
        }

        \DB::statement("INSERT INTO tenant_custom_field (tenant_id, template_id, `key`, key_mask, order_by, created_at, updated_at) VALUES " .
            implode(',', $statements));
        return true;
    }

    public static function getTenantDefaultFields($tenant_id){
        $query = self::select();
        return $query->where('tenant_id', $tenant_id)
            //->where('template_id', 0)
            ->orderBy('order_by','asc')
            ->get()
            ->toArray();
    }

    public static function getById($id){
        $query = self::select('id', 'tenant_id', 'key as query', \DB::raw('"lead_detail" as type'));
        return $query->where('id', $id)
            ->first();
    }

    public static function getByKey($key, $params){
        $query = self::select('id', 'tenant_id', 'key as query', \DB::raw('"lead_detail" as type'));
        $query->where('tenant_id', $params['company_id']);
        return $query->where('key', $key)
            ->first();
    }

    public static function getList($params)
    {
        $query = self::select('id', 'key');
        $query->join('template_fields', 'template_fields.field', 'tenant_custom_field.id');
        $query->where('tenant_id', $params['company_id']);
        $query->whereNull('deleted_at');
        $query->groupBy('tenant_custom_field.id');
        $query->orderBy('tenant_custom_field.order_by', 'ASC');
        if(!empty($params['ids']))
            $query->whereIn('id', $params['ids']);
        return $query->get();

    }
}
