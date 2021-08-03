<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table = "admin";

    public static function create($admin)
    {
        $obj = new static();

        $name = explode(' ', $admin['name']);

        $obj->first_name    = $name[0];
        $obj->last_name     = isset($name[1]) ? $name[1] : '';
        $obj->email         = $admin['email'];
        $obj->password      = $admin['password'];
        $obj->forgot_password_hash      = '';
        $obj->remember_login_token      = '';

        $obj->save();

        return $obj->id;
    }

    public static function getById($id){

        $query = self::select();
        return $query->where('id', $id)
            ->get();
    }

    public static function getByEmail($email){

        $query = self::select();
        return $query->where('email', $email)
            ->get();
    }

    public static function updateByEmail($email, $data){

        $qry_params = [];

        foreach($data as $column => $row){
            $qry_params[] = " $column = '$row' ";
        }

        \DB::statement('UPDATE admin SET ' . implode(', ', $qry_params) . " WHERE email = '$email'");
        return true;
    }

    public static function login($email, $password){

        $query = self::select();
        return $query->where('email', $email)
            ->where('password', $password)
            ->get();
    }
}
