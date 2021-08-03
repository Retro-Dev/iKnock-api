<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiUser extends Model
{
    protected $table = "api_user";

    public static function api_auth($token){

        $query = self::select();
        $result = $query->where('password', $token)
                        ->first();
        if(!is_null($result) && $result->count())
            return true;

        return false;

    }
}
