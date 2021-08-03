<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantQuery extends Model
{
    protected $table = "tenant_query";

    public static function getById($id){

        $query = self::select();
        return $query->where('id', $id)
            ->first();
    }
}
