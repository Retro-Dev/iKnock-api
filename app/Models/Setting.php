<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = "setting";

    public static function getByKey ($key)
    {
        return self::select('value')
                ->where('key', $key)
                ->first();
    }

    public static function geTenantSettingtById($setting_id, $tenant_id)//printer_email_address,
    {
        $query = \DB::table('user_setting');
        return $query->select('value')
                ->where('setting_id', $setting_id)
                ->where('tenant_id', $tenant_id)
                ->first();
    }
}
