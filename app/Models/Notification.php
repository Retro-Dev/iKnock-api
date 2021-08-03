<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = "notification";

    public static function getById($id){

        $query = self::select();
        return $query->where('id', $id)
            ->get();
    }
}
