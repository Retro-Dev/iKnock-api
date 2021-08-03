<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSettings extends Model
{
    protected $table = "user_setting";

    public static function updateSettingvalue($params)
    {
        \DB::statement("INSERT INTO user_setting (setting_id, tenant_id, `value`, created_at) VALUES 
                    ({$params['setting_id']}, '{$params['company_id']}', '{$params['value']}', NOW())
                    ON DUPLICATE KEY UPDATE `value` = '{$params['value']}'");

        return true;
    }
}
