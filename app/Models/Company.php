<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = "company";

    public static function create($company)
    {
        $name = $company['first_name'];
        if(!empty($company['last_name']))
            $name .= $company['last_name'];

            $obj = new static();

        $obj->title             = $name;
        $obj->image_url         = $company['image_url'];
        $obj->primary_user_id   = $company['primary_user_id'];
        //$obj->website           = $company['website'];
        //$obj->description       = $company['description'];

        $obj->save();

        return $obj->id;
    }

}
