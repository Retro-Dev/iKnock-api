<?php

namespace App\Models;

//use Illuminate\Notifications\Notifiable;
//use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class User extends Model
{
    protected $table = "user";

    public static function  createAgent($user)
    {
        $obj = new static();
        //print_r($user);exit;

        $name = explode(' ', $user['name']);

        $obj->first_name    = $name[0];
        $obj->last_name     = isset($name[1]) ? $name[1] : '';
        $obj->email         = $user['email'];
        $obj->image_url     = $user['system_image_url'];
        $obj->date_of_join  = $user['date_of_join'];
        $obj->mobile_no  = $user['mobile_no'];
        /*$obj->age           = $user['age'];
        $obj->gender        = $user['gender'];*/
        $obj->password      = $user['password'];
        $obj->user_group_id = $user['user_group_id'];
        $obj->company_id    = $user['company_id'];
        $obj->forgot_password_hash    = $user['forgot_password_hash'];
        $obj->forgot_password_hash_date    = $user['forgot_password_hash_date'];
        $obj->token         = self::getToken();
        /*$obj->device_type   = $user['device_type'];
        $obj->device_token  = $user['device_token'];
        $obj->device        = $user['device'];*/

        $obj->save();

        return $obj->id;
    }

    public static function createBusiness($user)
    {

        $obj = new static();

        $name = explode(' ', $user['name']);

        $obj->first_name    = $name[0];
        $obj->last_name     = isset($name[1]) ? $name[1] : '';
        $obj->email         = $user['email'];
        $obj->password      = $user['password'];
        $obj->image_url      = $user['system_image_url'];

        $obj->user_group_id = 1;

        $obj->city = $user['city'];
        $obj->state = $user['state'];

        $obj->website = $user['website'];
        $obj->about_me = $user['description'];

        $obj->token         = self::getToken();
        $obj->device_type   = $user['device_type'];
        $obj->device_token  = $user['device_token'];
        $obj->device        = $user['device'];
        $obj->address       = $user['address'];

        $obj->save();

        return $obj->id;
    }

    public static function createUserSetting($user_id)
    {
        \DB::statement("INSERT INTO user_setting (SELECT id, $user_id, `value`, NOW(), NOW() FROM setting
                        WHERE key_type = 'user')");
        return true;
    }

    public static function getUserSetting($user_id)
    {
        $query = \DB::table('user_setting');
        $query->select('user_setting.*', 'setting.key');
        $query->leftJoin('setting', 'setting.id', 'user_setting.setting_id');
        $query->where('user_id', $user_id);
        return $query->get();
    }

    public static function updateUserSetting($params)
    {
        $qry_params = [];

        $user_id = $params['user_id'];
        $setting_id = $params['setting_id'];
        $value = $params['value'];


        foreach($params as $column => $row){
            $qry_params[] = " $column = '$row' ";
        }

        \DB::statement("UPDATE user_setting SET value = $value WHERE user_id = $user_id AND setting_id = $setting_id");
        return true;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    /*protected $fillable = [
        'name', 'email', 'password',
    ];*/

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    /*protected $hidden = [
        'password', 'remember_token',
    ];*/

    public static function getToken()
    {
        return md5(Config::get('constants.APP_SALT').time());
    }

    public static function getById($id){

        $query = self::select();
        return $query->where('id', $id)
            ->first();
    }

    public static function getByDeviceToken($device_token){

        $query = self::select();
        return $query->where('device_token', $device_token)
            ->limit(1)
            ->get();
    }

    public static function getByEmail($email){

        $query = self::select();
        return $query->where('email', $email)
            ->limit(1)
            ->get();
    }

    public static function getBySocial($params){

        $query = self::select();
        return $query->where('social_id', $params['social_id'])
            ->where('social_type', $params['social_type'])
            ->whereNull('deleted_at')
            ->get();
    }

    public static function getByPasswordHash($hash){

        $query = self::select();
        return $query->where('forgot_password_hash', $hash)
            ->orderBy('id', 'desc')
            ->whereNull('deleted_at')
            ->get();
    }

    public static function auth($token){

        if(empty($token))
            return false;

        $query = self::select();
        $result = $query->whereRaw("token = '$token'")
            ->where('status_id',1)
            ->whereNull('deleted_at')
            ->first();

        if(!is_null($result) && $result->count())
            return $result;

        return false;
    }

    public static function updateByEmail($email, $data){

        $qry_params = [];

        foreach($data as $column => $row){
            $qry_params[] = " $column = '$row' ";
        }

        \DB::statement('UPDATE user SET ' . implode(', ', $qry_params) . " WHERE email = '$email'");
        return true;
    }

    public static function login($email, $password){

        $query = self::select();
        return $query->where('email', $email)
            ->where('password', $password)
            ->where('status_id', 1)
            ->where('user_group_id', 2)
            ->whereNull('deleted_at')
            ->get();
    }

    public static function loginWeb($email, $password){

        $query = self::select();
        return $query->where('email', $email)
            ->where('password', $password)
            ->where('status_id', 1)
            ->whereIn('user_group_id', [1,3])
            ->whereNull('deleted_at')
            ->get();
    }

    public static function userList($company_id, $group_ids = [], $exclude_ids = []){

        $query = self::select();
        $query->where('status_id', 1)
            ->where('company_id', $company_id)
            ->whereIn('user_group_id', $group_ids)
            ->whereNull('deleted_at');
        if(!empty($exclude_ids)){
            $query->whereNotIn('id', $exclude_ids);
        }
        return $query->get();
    }

    public static function loginById($user_id, $password){

        $query = self::select();
        return $query->where('id', $user_id)
            ->where('password', $password)
            ->get();
    }

    public static function getUserList($param)
    {
        $lat = $param['latitude'];
        $lng = $param['longitude'];
        $radius = $param['radius'];

        $haversine = "(3959 * acos (
                    cos ( radians($lat) )
                    * cos( radians(`latitude`) )
                    * cos( radians(`longitude`) - radians($lng) )
                    + sin ( radians($lat) )
                    * sin( radians(`latitude`) )
                ))";


        $query = \DB::table('user');
        $query->select('user.*');

        if(!empty($param['user_group_id']))
            $query->where('user_group_id', $param['user_group_id']);

        if(!empty($param['name'])) {
            $query->whereRaw("((first_name like '%" . $param['name'] . "%' OR last_name like '%" . $param['name'] . "%') OR " .
            "CONCAT(`first_name`, ' ', `last_name`)" . ' LIKE '. "'%".$param['name']."%')");
        }
        if(!empty($param['latitude']) && !empty($param['longitude']))
            $query->selectRaw("{$haversine} AS distance")
                ->whereRaw("{$haversine} < ?", [$radius]);

        // HAVING distance < 30 ORDER BY distance
        if(!isset($param['order_by'])){
            $param['order_by'] = 'id';
            $param['order_type'] = 'desc';
        }
        $order_by = 'user.'.$param['order_by'];
        $query->orderBy($order_by, $param['order_type']);

        return $query->paginate(Config::get('constants.PAGINATION_PAGE_SIZE'));
    }

    public static function getTenantUserList($param)
    {
        //print_r($param);exit;
        $order_by_map['name'] = 'first_name';
        $order_by_map['email'] = 'email';
        $order_by_map['contact_number'] = 'mobile_no';
        $order_by_map['joining_date'] = 'date_of_join';
        $order_by_map['status'] = 'status_id';

        $query = \DB::table('user');
        $query->select('user.*',\DB::raw( "0 as lead_percentage"),\DB::raw("0 as lead_count"));
        $query->where('company_id', $param['company_id']);
        $query->whereNull('deleted_at');

        $user_group_ids[] = 2;
        if(isset($param['is_all']) && !empty($param['is_all']))
            $user_group_ids[] = 3;

        $query->whereIn('user_group_id', $user_group_ids); // agent user group id

        if(!empty($param['name']))
            $query->whereRaw("(first_name like '%". $param['name']."%' OR last_name like '%". $param['name']."%')");

        if(!isset($param['order_by'])){
            $param['order_by'] = 'id';
            $param['order_type'] = 'desc';
        }
        $order_by = (isset($order_by_map[$param['order_by']]))? $order_by_map[$param['order_by']] : 'id';
        $order_by = 'user.'. $order_by; //$order_by_map[$param['order_by']];

        $query->orderBy($order_by, $param['order_type']);

        return $query->get();
    }

    public static function verifySubscription($user_id, $user_group_id, $subscription_expiry_date)
    {
        if($user_group_id != 2)
            return 0;

        $date_now = date("Y-m-d");

        if ($date_now <= $subscription_expiry_date)
            return 1;

        \DB::statement("UPDATE user SET user_group_id = 1 WHERE id = $user_id");
        return 0;
    }

    public static function getSubscriptionStatus($user_id)
    {
        $user = self::getById($user_id);

        $user_group_id = ($user[0]['user_group_id']);

        if($user_group_id != 2)
            return false;

        return true;
    }

    public static function updateFields($fields, $where_clause)
    {
        $field_value = [];
        foreach ($fields as $key => $field){
            $field_value[] = "$key = '$field'";
        }

        $clause_field_value = [];
        foreach ($where_clause as $key => $field){
            $clause_field_value[] = "$key = '$field'";
        }

        \DB::statement('Update user set ' . implode(', ', $field_value) . ' WHERE ' . implode(' AND ', $clause_field_value));

        return true;
    }

    public static function deleteAgentByTenantId($tenant_id)
    {
        if(is_array($tenant_id))
            $tenant_id = implode(',', $tenant_id);

        \DB::statement("Update `user` SET deleted_at = NOW() WHERE company_id IN ($tenant_id)");
        \DB::statement("Update company SET deleted_at = NOW() WHERE id IN ($tenant_id)");
        return true;
    }

    public static function deleteUser($user_id)
    {
        if(is_array($user_id))
            $user_id = implode(',', $user_id);

        \DB::statement("Update user SET deleted_at = NOW() WHERE id IN ($user_id)");
        return true;
    }
}
