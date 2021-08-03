<?php

namespace App\Http\Controllers;

use App\Http\Middleware\LoginAuth;
use App\Libraries\Payment\BrainTree;
use App\Models\Company;
use App\Models\Donation;
use App\Models\Notification;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\Transactions;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\UserWishlist;
use Carbon\Carbon;
use Couchbase\UserSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;

class UserController extends Controller
{

    function __construct(){

        parent::__construct();
        $this->middleware(LoginAuth::class, ['only' => ['show', 'edit', 'updateBusiness', 'updateListner', 'changePassword', 'getSetting'
                                                                    , 'profile', 'updateSetting', 'updateLocation', 'userSubscription', 'subscription', 'increaseDealQuota', 'addCompanyDonation'
                                                                    , 'paymentProcess', 'tenantUserList', 'storeAgent', 'updateAgent', 'deleteAgent', 'showView'
                                                                    , 'resetAgentPassword', 'showPrinterEmail', 'updatePrinterEmailAddress'
        ]]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $param['user_group_id'] = isset($request['user_group_id']) ? $request['user_group_id'] : 3;
        $param['name'] = isset($request['name']) ? $request['name'] : '';
        $param['latitude'] = isset($request['latitude']) ? $request['latitude'] : '';
        $param['longitude'] = isset($request['longitude']) ? $request['longitude'] : '';
        $param['radius'] = isset($request['radius']) ? $request['radius'] : 500;

        $list = User::getUserList($param);

        return $this->__sendResponse('User', $list, 200,'User list retrieved successfully.');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function tenantUserList(Request $request)
    {
        $param_rules['search'] = 'sometimes';
        $param['company_id'] = $request['company_id'];
        $param['name'] = isset($request['name']) ? $request['name'] : '';
        $param['order_by'] = isset($request['order_by']) ? $request['order_by'] : 'id';
        $param['order_type'] = isset($request['order_type']) ? $request['order_type'] : 'desc';
        $param['is_all'] = isset($request['is_all']) ? $request['is_all'] : '0';

        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        $param['user_id'] = $request['user_id'];
        

        $list = User::getTenantUserList($param);

        $this->__is_ajax = true;
        $this->__is_paginate = false;
        return $this->__sendResponse('User', $list, 200,'User list retrieved successfully.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeBusiness(Request $request)
    {
        $param_rules['name']            = 'required|string|max:100';
        $param_rules['email']           = 'required|unique:user|string|email|max:150|unique:user,deleted_at,NULL';

        $param_rules['password']        = 'required|string|min:6';
        $param_rules['device_type']     = 'required|string';
        $param_rules['device_token']    = 'required|string';
        $param_rules['device']          = 'required|nullable|string';

        $param_rules['image_url']       = 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048';

        $param_rules['city']            = 'nullable|string';
        $param_rules['state']           = 'nullable|string';


        $param_rules['website']         = 'nullable|string';
        $param_rules['description']     = 'nullable|string';
        $param_rules['address']         = 'required|string';

        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        $request['password'] = $this->__encryptedPassword($request['password']);

        //$obj_user = User::find($request['user_id']);
        if ($request->hasFile('image_url')) {
            // $obj is model
            $request['system_image_url'] = $this->__moveUploadFile(
                $request->file('image_url'),
                md5($request['email'].$request['device_token']),
                Config::get('constants.USER_IMAGE_PATH')
            );
            //$obj_user->image_url = $request['system_image_url'];
        }

        User::updateFields(
                        ['device_token' => $request['device_token'].'_old'],
                        ['device_token' => $request['device_token']]
        );

        $user_id = User::createBusiness($request->all());

        /*$name = explode(' ', $request->name);

        $obj_user->first_name    = $name[0];
        $obj_user->last_name     = isset($name[1]) ? $name[1] : '';

        $obj_user->email = $request['email'];

        $obj_user->city = $request['city'];
        $obj_user->state = $request['state'];
        $obj_user->user_group_id = 1;

        $obj_user->website = $request['website'];
        $obj_user->about_me = $request['description'];

        $obj_user->password = $request['password'];
        $obj_user->save();*/

        //$user_id = $request['user_id'];

        $request['primary_user_id'] = $user_id;
        $company_id = Company::create($request->all());
        //User::createUserSetting($user_id);
        $obj_user = User::find($user_id);
        $this->_btAddCustomer($request, $obj_user);

        User::where('id', $user_id)->Update(['company_id' => $company_id]);

        $this->__is_paginate = false;
        //$this->__collection = false;

        return $this->__sendResponse('User', User::getById($user_id), 200,'Business user has been added successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeAgent(Request $request)
    {
        $param_rules['name']        = 'required|string|max:100|regex:/(?!^\d+$)^.+$/';
        $param_rules['email']       = 'required|email|max:150|unique:user,email,NULL,id,deleted_at,NULL';
        //$param_rules['mobile_no'] = 'nullable|string|max:20|unique:user,mobile_no,NULL,id,deleted_at,NULL';
        $param_rules['mobile_no']   = 'nullable';
        $param_rules['image_url']       = 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        //$param_rules['password']    = 'required|string|min:6';
        //$param_rules['confirm_password'] = 'required_with:password|same:password|string|min:6';
        $param_rules['date_of_join'] = 'required|date_format:"Y-m-d"';
        //$param_rules['device_type'] = 'required|string';
        //$param_rules['device_token'] = 'required|string';
        //$param_rules['device']      = 'required|string';
        $param_rules['user_group_id']      = 'required|int|in:2,3';
        //$param_rules['gender']      = 'required|in:male,female';

        $this->__is_ajax = true;
        //print_r($request->all());
        //exit;
        $response = $this->__validateRequestParams($request->all(), $param_rules, [
            'user_group_id.required' => 'The user type field is required.',
            'user_group_id.in' => 'The selected user type is invalid.'
        ]);

        if($this->__is_error == true)
            return $response;

        /*$user = User::getByDeviceToken($request['device_token']);
        if($user->count()){
            $this->__is_paginate = false;
            return $this->__sendResponse('User', $user, 200,'User has been added successfully.');
        }*/

        if($request['user_group_id'] == 3){
            $user_obj = User::userList($request['company_id'], [$request['user_group_id']]);
            if($user_obj->count() >= Config::get('constants.SUB_ADMIN_QUOTA') ){
                $errors['code'] = Lang::get('messages.subadmin_quota_error');
                return $this->__sendError('Validation Error.', $errors);
            }
        }


        if ($request->hasFile('image_url')) {
            // $obj is model
            $request['system_image_url'] = $this->__moveUploadFile(
                $request->file('image_url'),
                md5($request['email'].$request['device_token']),
                Config::get('constants.USER_IMAGE_PATH')
            );
            //$obj_user->image_url = $request['system_image_url'];
        }
        $request['password'] = 'agent_123456';
        $request['date_of_join'] = date('Y-m-d', strtotime($request['date_of_join']));
        $request['password'] = $this->__encryptedPassword($request['password']);
        
        
        // update forgot password hash and update hash date
        $request['forgot_password_hash'] = $this->__generateUserHash($request->email);
        $request['forgot_password_hash_date'] = Carbon::now();

        $user_id = User::createAgent($request->all());
        $hash = $request['forgot_password_hash'];
        //User::createUserSetting($user_id);

        $this->__is_paginate = false;
        $this->__is_collection = false;
        //$this->__collection = false;

        $user = User::getById($user_id);
        //$this->_btAddCustomer($request, $user[0]);
        
        
        $mail_params['USER_NAME'] = $request['name'];
        $mail_params['CONFIRMATION_LINK'] = env('APP_URL')."/user/registration/$hash";
        //$mail_params['USER_LINK'] = env('APP_URL').'/user/login';
        $mail_params['APP_NAME'] = env('APP_NAME');

        // make forgot password url and implement its email configuration.
        $this->__sendMail('user_registration_email', $request->email, $mail_params);

        // send email to user


        return $this->__sendResponse('User', $user, 200,'User has been added successfully.');
    }

    public function resetAgentPassword(Request $request, $id)
    {

        $param_rules['user'] = 'required|exists:user,id,company_id,'.$request['company_id'];


        $user_id = $request['user'] = $id;
        $this->__is_ajax = true;
        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        /*$request['password'] = '123456';
        $request['password'] = $this->__encryptedPassword($request['password']);*/

        // update forgot password hash and update hash date
        $user = User::find($user_id);
        $user->forgot_password_hash = $this->__generateUserHash($user->email);
        $user->forgot_password_hash_date = Carbon::now();
        $user->save();

        $hash = $user->forgot_password_hash;

        $this->__is_paginate = false;
        $this->__is_collection = false;


        $mail_params['USER_NAME'] = $user->first_name . ' ' . $user->last_name;
        $mail_params['CONFIRMATION_LINK'] = env('APP_URL')."/user/registration/$hash";
        //$mail_params['USER_LINK'] = env('APP_URL').'/user/login';
        $mail_params['APP_NAME'] = env('APP_NAME');

        // make forgot password url and implement its email configuration.
        $this->__sendMail('user_reset_password', $user->email, $mail_params);

        // send email to user
        return $this->__sendResponse('User', $user, 200,'Reset password link has been sent to user successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showView(Request $request)
    {
        $param_rules['user_id'] = 'required|exists:user,id';

        $response = $this->__validateRequestParams(['user_id' => $request['user_id']], $param_rules);

        $this->__view = 'tenant.agent.edit_user';
        if($this->__is_error == true)
            return $response;
        $user = User::getById($request['user_id']);
        $user_email = Setting::geTenantSettingtById(7,$request['company_id']);
        $user['printer_email_address'] = (isset($user_email->value)) ? $user_email->value : '';

        $this->__is_paginate = false;
        $this->__is_collection = false;
        return $this->__sendResponse('User', $user, 200,'User retrieved successfully.');
    }

    public function showPrinterEmail(Request $request)
    {
        $param_rules['user_id'] = 'required|exists:user,id';

        $response = $this->__validateRequestParams(['user_id' => $request['user_id']], $param_rules);

        $this->__view = 'tenant.printer.printer_email';
        if($this->__is_error == true)
            return $response;
        $user = User::getById($request['user_id']);
        $user_email = Setting::geTenantSettingtById(7,$request['company_id']);
        $user['printer_email_address'] = (isset($user_email->value)) ? explode(',', $user_email->value) : [];
        $this->__is_paginate = false;
        $this->__is_collection = false;
        return $this->__sendResponse('User', $user, 200,'User retrieved successfully.');
    }

    public function show(Request $request)
    {
        $param_rules['id'] = 'required|exists:user';

        $response = $this->__validateRequestParams(['id' => $request['user_id']], $param_rules);

        if($this->__is_error == true)
            return $response;

        $this->__is_paginate = false;
        return $this->__sendResponse('User', User::getById($request['user_id']), 200,'User retrieved successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function profile(Request $request)
    {
        $param_rules['target_id'] = 'required|exists:user,id';

        if(!isset($request['target_id']))
            $request['target_id'] = $request['user_id'];

        $this->__is_ajax = true;
        $response = $this->__validateRequestParams(['target_id' => $request['target_id']], $param_rules);

        if($this->__is_error == true)
            return $response;

        $this->__is_paginate = false;
        $this->__is_collection = false;

        $user = User::getById($request['target_id']);

        $user_email = Setting::geTenantSettingtById(7,$request['company_id']);
        $email_address = (explode(',', $user_email->value));
        $user['printer_email_address'] = (!empty($user_email->value)) ?  $email_address : [];

        return $this->__sendResponse('User', $user, 200,'User retrieved successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getSetting(Request $request)
    {

        $this->__is_paginate = false;

        return $this->__sendResponse('Settings', User::getUserSetting($request['user_id']), 200,'User retrieved successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateBusiness(Request $request)
    {
        $param_rules['id']          = 'required|exists:user';
        //$param_rules['address']     = 'nullable|string';
        $param_rules['name']        = 'required|string|max:100|regex:/(?!^\d+$)^.+$/';
        $param_rules['printer_email_address'] = 'nullable|string';
        //$param_rules['email']       = 'required|string|email|max:150|unique:user';
        //$param_rules['mobile_no'] = 'nullable|string|max:20|unique:user,mobile_no,' . $id;
        $param_rules['password']  = 'nullable|alpha_num|between:6,20';
        //$param_rules['image_url']   = 'nullable';


        $this->__is_ajax = true;
        $request['id'] = $request['user_id'];
        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if ($this->__is_error == true)
            return $response;

        $obj = User::find($request['user_id']);

        if ($request->hasFile('image_url')) {
            // $obj is model
            $obj->image_url = $this->__moveUploadFile(
                $request->file('image_url'),
                $request->user_id.time(),
                Config::get('constants.USER_IMAGE_PATH')
            );
        }

        $name = explode(' ', $request->name);

        $obj->first_name    = $name[0];
        unset($name[0]);
        $obj->last_name     = isset($name[1]) ? implode(' ',$name) : '';
        if(!empty($request->password))
            $obj->password = $this->__encryptedPassword($request['password']);

        $obj->save();

        Company::where('id', $obj->company_id)
            ->where('primary_user_id', $request['user_id'])
            ->Update([
                'title' => $request['name']
            ]);

        if(isset($request['printer_number']) && !empty($request['printer_number'])) {
            \App\Models\UserSettings::updateSettingvalue([
                'setting_id' => 6,
                'company_id' => $obj->company_id,
                'value' => trim($request['printer_number'],' ,'),
            ]);
        }

        if(isset($request['printer_email_address']) && !empty($request['printer_email_address'])) {
            \App\Models\UserSettings::updateSettingvalue([
                'setting_id' => 7,
                'company_id' => $obj->company_id,
                'value' => $request['printer_email_address'],
            ]);
        }

        $this->__is_paginate = false;
        $this->__is_collection = false;
        return $this->__sendResponse('User', User::getById($request['user_id']), 200,'User updated successfully.');

    }

    public function updatePrinterEmailAddress(Request $request)
    {
        $param_rules['id'] = 'required|exists:user';

        if(!isset($request['is_delete'])) {
            $param_rules['printer_email_address'] = 'required|email';
        }


        $this->__is_ajax = true;
        $request['id'] = $request['user_id'];
        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if ($this->__is_error == true)
            return $response;

        $obj = User::find($request['user_id']);

        if(isset($request['printer_number']) && !empty($request['printer_number'])) {
            \App\Models\UserSettings::updateSettingvalue([
                'setting_id' => 6,
                'company_id' => $obj->company_id,
                'value' => trim($request['printer_number'],' ,'),
            ]);
        }

        if(isset($request['printer_email_address'])) {
            if(!isset($request['is_delete'])) {
                $user_email = Setting::geTenantSettingtById(7, $request['company_id']);
                if(!empty($user_email->value))
                    $printer_email_address = explode(',', $user_email->value);
                $result_printer_email_address = [];
                if(!empty($printer_email_address)) {
                    foreach ($printer_email_address as $printer_email)
                        $result_printer_email_address[$printer_email] = $printer_email;
                }

                $result_printer_email_address[$request['printer_email_address']] = $request['printer_email_address'];
                $request['printer_email_address'] = implode(',', $result_printer_email_address);
            }

            \App\Models\UserSettings::updateSettingvalue([
                'setting_id' => 7,
                'company_id' => $obj->company_id,
                'value' => $request['printer_email_address'],
            ]);
        }

        $this->__is_paginate = false;
        $this->__is_collection = false;
        return $this->__sendResponse('User', User::getById($request['user_id']), 200,'Printer email has been added successfully.');

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateAgent(Request $request)
    {
        $request['id'] = $user_id = (isset($request->target_id))? $request->target_id : $request['user_id'];
        $param_rules['id'] = 'required|exists:user';
        $param_rules['name'] = 'required|string|max:100';
        $param_rules['email']       = "required|email|max:150|unique:user,email,$user_id,id,deleted_at,NULL";
        $param_rules['date_of_join'] = 'required|date_format:"Y-m-d"';
        $param_rules['user_status_id'] = 'required|int';
        $param_rules['user_group_id'] = 'required|int|in:2,3';
        //$param_rules['mobile_no'] = 'nullable|string|max:20|unique:user,mobile_no,' . $user_id;
        $param_rules['mobile_no'] = 'nullable';
        //$param_rules['password'] = 'nullable|alpha_num|between:6,20';
        $param_rules['image_url'] = 'nullable';
        //$param_rules['age']      = 'required|int';
        //$param_rules['gender']      = 'required|in:male,female';

        $this->__is_ajax = true;
        $response = $this->__validateRequestParams($request->all(), $param_rules, [
            'user_group_id.required' => 'The user type field is required.',
            'user_group_id.in' => 'The selected user type is invalid.'
        ]);


        if ($this->__is_error == true)
            return $response;

        if($request['user_group_id'] == 3){
            $user_obj = User::userList($request['company_id'], [$request['user_group_id']], [$user_id]);
            if($user_obj->count() >= Config::get('constants.SUB_ADMIN_QUOTA') ){
                $errors['code'] = Lang::get('messages.subadmin_quota_error');
                return $this->__sendError('Validation Error.', $errors);
            }
        }

        //$obj = User::where(['company_id' => 15, 'id' => $user_id]); //$request['user_id']

        $name = explode(' ', $request['name']);
        $data = [
            'first_name'    => $name[0],
            'last_name'     => isset($name[1]) ? $name[1] : '',
            'email'         => $request['email'],
            'mobile_no'     => $request['mobile_no'],
            'date_of_join'  => $request['date_of_join'],
            'status_id'  => $request['user_status_id'],
            'user_group_id'  => $request['user_group_id'],
        ];

        if ($request->hasFile('image_url')) {
            // $obj is model
            $data['image_url'] = $this->__moveUploadFile(
                $request->file('image_url'),
                $request['user_id'].time(),
                Config::get('constants.USER_IMAGE_PATH')
            );
        }

        $obj = User::where(['company_id' => $request['company_id'], 'id' => $user_id])->update($data);


        /*$obj->first_name    = $name[0];
        $obj->last_name     = isset($name[1]) ? $name[1] : '';
        $obj->email        = $request->email;*/

        /*if(!empty($request->password))
            $obj->password  = $this->__encryptedPassword($request->password);*/

        //$obj->save();

        $this->__is_paginate = false;
        $this->__is_collection = false;
        return $this->__sendResponse('User', User::getById($user_id), 200,'User updated successfully.');

    }

    public function deleteAgent(Request $request)
    {
        $param_rules['id']       = 'required|exists:user,id,company_id,'.$request['company_id'];

        $this->__is_ajax = true;
        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        User::deleteUser($request['id']);

        $this->__is_paginate = false;
        $this->__is_collection = false;
        $this->__collection = false;
        return $this->__sendResponse('user', [], 200,'User has been deleted successfully.');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateSetting(Request $request)
    {
        //$param_rules['id'] = 'required|exists:user';
        $param_rules['setting_id'] = 'required';
        $param_rules['value']      = 'required';


        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if ($this->__is_error == true)
            return $response;

        User::updateUserSetting($request->all());

        $this->__is_paginate = false;
        return $this->__sendResponse('Settings', User::getUserSetting($request['user_id']), 200,'User setting updated successfully.');

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateLocation(Request $request)
    {
        //$param_rules['id'] = 'required|exists:user';
        $param_rules['latitude'] = 'required';
        $param_rules['longitude']      = 'required';


        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if ($this->__is_error == true)
            return $response;

        $obj = User::find($request['user_id']);

        $obj->latitude  = $request->latitude;
        $obj->longitude = $request->longitude;

        $obj->Save();

        $this->__is_paginate = false;
        return $this->__sendResponse('User', User::getById($request['user_id']), 200,'User location updated successfully.');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Login  the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function loginIndex(Request $request){
        $data = [];

        if ($request->hasSession('error')) {
            $data['error'] = $request->session()->pull('error');
        }

        return view('tenant.login.index', $data);
    }

    public function logout(Request $request){
        $request->session()->flush();

        $this->__is_redirect = true;
        $this->__view = 'tenant/login';

        $this->__is_paginate = false;
        $this->__collection = false;
        return $this->__sendResponse('User', [], 200,'User has been logged out successfully.');
    }

    public function login(Request $request)
    {
        $param_rules['email']       = 'required|string|email|max:150';
        $param_rules['password']    = 'required|string';

        $this->__is_redirect = true;
        $this->__view = 'tenant/login';

        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        $user = User::login($request->email, $this->__encryptedPassword($request->password));

        if(count($user) <= 0){
            $errors['email'] = Lang::get('auth.failed');
            return $this->__sendError('Validation Error.', $errors);
        }

        if($this->call_mode != 'api')
            $this->__setSession('user', $user[0]);

        $this->__is_paginate = false;
        //$this->__collection = false;
        $this->__view = 'tenant/dashboard';
        return $this->__sendResponse('User', $user, 200,'User has been logged in successfully.');
    }

    public function loginWeb(Request $request)
    {   
        $param_rules['email']       = 'required|string|email|max:150';
        $param_rules['password']    = 'required|string';

       

        $this->__is_redirect = true;
        $this->__view = 'tenant/login';

        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        $user = User::loginWeb($request->email, $this->__encryptedPassword($request->password));
        if(count($user) <= 0){
            $errors['email'] = Lang::get('auth.failed');
            return $this->__sendError('Validation Error.', $errors);
        }

        if($this->call_mode != 'api')
            $this->__setSession('user', $user[0]);

        $this->__is_paginate = false;
        //$this->__collection = false;
        $this->__view = 'tenant/dashboard';
        return $this->__sendResponse('User', $user, 200,'User has been logged in successfully.');
    }

    public function forgotPassword(Request $request)
    {
        $param_rules['email']   = 'required|string|email|max:150';

        $response = $this->__validateRequestParams($request->all(), $param_rules);
        $this->__is_ajax   = true;
        if($this->__is_error == true)
            return $response;

        // get data against email address
        $user = User::getByEmail($request->email);

        if(count($user) <= 0){
            $errors['email'] = Lang::get('passwords.user');
            return $this->__sendError('Validation Error.', $errors);
        }

        // update forgot password hash and update hash date
        $hash = $this->__generateUserHash($request->email);


        User::updateByEmail($request->email, [
                            'forgot_password_hash' => $hash,
                            'forgot_password_hash_date' => Carbon::now()
                        ]);


        $mail_params['USER_NAME'] = $user[0]->first_name . ' ' . $user[0]->last_name;
        $mail_params['CONFIRMATION_LINK'] = env('APP_URL')."/user/forgot/password/$hash";
        $mail_params['USER_LINK'] = env('APP_URL').'/tenant/login';
        $mail_params['APP_NAME'] = env('APP_NAME');

        // make forgot password url and implement its email configuration.
        $this->__sendMail('user_forgot_password', $request->email, $mail_params);

        // send email to user

        $this->__is_paginate = false;
        return $this->__sendResponse('User', $user, 200,$errors['email'] = Lang::get('passwords.sent'));
    }

    public function changePasswordByHash(Request $request)
    {
        $param_rules['hash']   = 'required';
        $param_rules['password']   = 'required|confirmed|min:6';
        $param_rules['password_confirmation']   = '';

        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        // get data against email address
        $user = User::getByPasswordHash($request->hash);

        if(count($user) <= 0){
            $errors['email'] = Lang::get('passwords.hash');
            return $this->__sendError('Validation Error.', $errors);
        }

        $user[0]->password = $this->__encryptedPassword($request->password);
        $user[0]->forgot_password_hash = '';

        $user[0]->save();

        $this->__is_paginate = false;
        return $this->__sendResponse('User', $user, 200,Lang::get('passwords.reset'));
    }

    public function changePassword(Request $request)
    {
        $param_rules['old_password']   = 'required';
        $param_rules['password']   = 'required|confirmed|min:6';
        $param_rules['password_confirmation']   = '';

        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        // get data against email address
        $user = User::loginById($request->user_id, $this->__encryptedPassword($request->old_password));
        if(count($user) <= 0){
            $errors['password'] = Lang::get('passwords.change_password');
            return $this->__sendError('Validation Error.', $errors);
        }

        $user[0]->password = $this->__encryptedPassword($request->password);

        $user[0]->save();

        $this->__is_paginate = false;
        return $this->__sendResponse('User', $user, 200,Lang::get('passwords.reset'));
    }

    public function changePasswordWeb(Request $request){
        $data = [
            'request'=>$request
        ];
        if(isset($request->hash)){
            $this->__call_mode   = 'web';
            $this->__is_ajax   = true;
            $response = $this->changePasswordByHash($request);
            if($response->original['code'] == 200){
                return view('thankyou',$data);
            }else{
                $data['error'] = $response->original['data'][0];
            }
            //echo "<pre>";print_r($data['error']);die();
        }

        return view('forgotpassword',$data);
    }


    public function contactUs(Request $request)
    {
        $param_rules['name']        = 'required|string';
        $param_rules['email']       = 'required|string|email|max:150';
        $param_rules['mobile_no']   = 'required|string';
        //$param_rules['subject']     = 'required|string';
        $param_rules['message']     = 'required|string';


        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        $mail_params['USER_NAME'] = $request['name'];
        $mail_params['EMAIL'] = $request['email'];
        $mail_params['FROM'] = $request['email'];
        $mail_params['MOBILE_NO'] = $request['mobile_no'];
        $mail_params['SUBJECT'] = 'Application'; //$request['subject'];
        $mail_params['MESSAGE'] = $request['message'];
        $mail_params['APP_NAME'] = env('APP_NAME');

        // send email for contact us.
        $this->__sendMail('admin_contact_us', Setting::getByKey('receive_email')->value, $mail_params);


        $this->__is_paginate = false;
        $this->__collection = false;
        return $this->__sendResponse('User', [], 200,$errors['email'] = Lang::get('messages.contact'));

    }

    public function social(Request $request)
    {
        //$param_rules['name']        = 'required|string|max:100';
        //$param_rules['email']       = 'required|string|email|max:150';
        $param_rules['social_id']     = 'required';
        $param_rules['social_type']   = 'required|in:facebook,google_plus,twitter';
        //$param_rules['age']      = 'required|int';
        //$param_rules['gender']      = 'required|in:male,female';

        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        $user_response = User::getBySocial($request->all());

        if(count($user_response)) {
            $this->__is_paginate = false;
            return $this->__sendResponse('User', $user_response, 200, 'User already exists.');
        }

        $param_rules['device_type']     = 'required|string';
        $param_rules['device_token']    = 'required|string';
        $param_rules['device']          = 'required|string';
        //$param_rules['user_group_id']   = 'required|string';

        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        $obj_user = new User();

        $name = explode(' ', isset($request['name'])? $request['name'] : '');

        $obj_user->first_name       = $name[0];
        $obj_user->first_name       = $name[0];
        $obj_user->last_name        = isset($name[1]) ? $name[1] : '';

        $obj_user->email            = isset($request['email'])? $request['email'] : '';
        $obj_user->image_url        = isset($request['image_url'])? $request['image_url'] : '';

        $obj_user->social_id        = $request['social_id'];
        $obj_user->social_type      = $request['social_type'];

        $obj_user->user_group_id    = 1;
        $obj_user->token            = User::getToken();

        $obj_user->save();
        User::createUserSetting($obj_user->id);

        $this->__is_paginate = false;
        //$this->__collection = false;

        $user = User::getById($obj_user->id);
        $this->_btAddCustomer($request, $user[0]);

        return $this->__sendResponse('User', $user, 201, 'Social user has been added successfully.');
    }

    public function paymentProcess(Request $request)
    {

        $param_rules['payment_process'] = 'required|in:subscribe';

        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        $map_payment_process['subscription'] = 'subscription';

        $fn = $map_payment_process[$request['payment_process']];
        return $this->$fn($request);

        $this->__is_paginate = false;
        $this->__collection = false;
        return $this->__sendResponse('User', $result, 200, 'Subscribed to gold membership successfully.');
    }

    public function subscription(Request $request)
    {
        $param_rules['subscription_id']    = 'required';
        $param_rules['payment_token']    = 'required';

        $this->__module = 'admin/';
        $this->__view = 'view/'.$request['user_id'];

        $this->__is_redirect = true;

        if(!isset($request['payment_token']))
            $request['payment_token'] = $request['payment_method_nonce'];

        $response = $this->__validateRequestParams($request->all(), $param_rules);
        if($this->__is_error == true)
            return $response;

        $obj_user = UserSubscription::getByUserId($request['user_id']);
        $last_subscription_id = (isset($obj_user->subscription_id))? $obj_user->subscription_id : 1;

        $current_date = new Carbon;
        //if(($current_date <= $obj_user['subscription_expiry_date'] && ($obj_user['total_user_deals']) && ($obj_user['total_user_featured_deals']) || $request['subscription_id'] == 1)){
        if(($current_date <= $obj_user['subscription_expiry_date'] && $last_subscription_id != 1)){
            //$errors['message'] = 'Already a subscribed member and still have deals in quota';
            $errors['message'] = 'Already a subscribed member';
            /*ignore this condition if customer wants to upgrade its subscription*/
            return $this->__sendError('Validation Error.', $errors);
        }

        $customer = ['test'];

        $ob_braintree = new BrainTree();
        $subscription_detail = Subscription::getById($request['subscription_id']);

        $params = [];
        $params['paymentMethodNonce'] = $request['payment_token'];
        $params['planId'] = $subscription_detail->key;

        $customer = $ob_braintree->customerSubscription($params);
        if($ob_braintree->is_error == true){
            $errors['message'] = $ob_braintree->message;
            return $this->__sendError('Validation Error.', $errors);
        }

        $obj_trans = new Transactions();

        $obj_trans->sender_id = $request['user_id'];
        $obj_trans->amount = $subscription_detail->amount;
        $obj_trans->gateway_request = json_encode($request->all());
        $obj_trans->gateway_response = json_encode($customer);
        //$obj_trans->gateway_type = 'payal';

        $obj_trans->save();

        /*$obj_user already define up*/
        $subscription_expiry_date = Carbon::now()->addMonth($subscription_detail->duration)->format('Y-m-d');
        //$obj_user->user_group_id = $request['subscription_id']; //group_id
        //$obj_user->save();

        // user subscription entry
        $user_subscription['user_id'] = $request['user_id'];
        $user_subscription['subscription_id'] = $request['subscription_id'];
        $user_subscription['subscription_expiry_date'] = $subscription_expiry_date;
        $user_subscription['total_user_deals'] = $subscription_detail->total_deals;
        $user_subscription['total_user_featured_deals'] = $subscription_detail->total_featured_deals;
        $obj_user_subscription = UserSubscription::updateSubscription($user_subscription);

        $this->__view = 'view';
        $this->__is_redirect = true;

        $this->__is_paginate = false;
        //$this->__collection = false;
        return $this->__sendResponse('Subscription', Subscription::getByUserId($request['user_id'],$request['subscription_id'] ), 200, 'Subscribed to '.$subscription_detail->title.' membership successfully.');
    }

    public function increaseDealQuota(Request $request)
    {
        $param_rules['amount']              = 'required';
        $param_rules['payment_token']       = 'required';

        if(!isset($request['payment_token']))
            $request['payment_token'] = $request['payment_method_nonce'];

        $this->__module = 'admin/';
        $this->__view = 'donation/charge-manually/'.$request['user_id'];


        //$this->__module = '';
        //$this->__view = 'custom_charge_manually_detail_page';

        $this->__is_redirect = true;
        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        $request['amount'] = Setting::getByKey('featured_deal_charges')->value;
        $params['paymentMethodNonce'] = $request['payment_token'];
        $params['amount'] = $request['amount'];


        $ob_braintree = new BrainTree();
        $re_bt = $ob_braintree->charge($params);

        if($ob_braintree->is_error == true){
            $errors['message'] = $ob_braintree->message;
            return $this->__sendError('Validation Error.', $errors);
        }


        $obj_trans = new Transactions();

        $obj_trans->sender_id = $request['user_id'];
        $obj_trans->receiver_id = 0;
        $obj_trans->amount = $request['amount'];
        $obj_trans->transaction_head = 'featured deal';
        $obj_trans->gateway_request = json_encode($request->all());
        $obj_trans->gateway_response = json_encode($re_bt);
        //$obj_trans->gateway_type = 'payal';

        $obj_trans->save();

        UserSubscription::where('user_id',$request['user_id'])->increment('total_user_featured_deals');

        $this->__view = 'donation';
        $this->__is_redirect = true;

        $this->__is_paginate = false;
        $this->__collection = false;
        return $this->__sendResponse('UserSubscription', [UserSubscription::getByUserId($request['user_id'])], 200, 'User quota is incremented successfully.');
    }

    public function addCompanyDonation(Request $request)
    {
        $param_rules['amount']              = 'required';
        $param_rules['payment_token']       = 'required';

        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        $params['paymentMethodNonce'] = $request['payment_token'];
        $params['amount'] = $request['amount'];

        $ob_braintree = new BrainTree();
        $re_bt = $ob_braintree->charge($params);

        if($ob_braintree->is_error == true){
            $errors['message'] = $ob_braintree->message;
            return $this->__sendError('Validation Error.', $errors);
        }

        $obj_trans = new Transactions();

        $obj_trans->sender_id = $request['user_id'];
        $obj_trans->amount = $request['amount'];
        $obj_trans->transaction_head = 'company donation';
        $obj_trans->gateway_request = json_encode($request->all());
        $obj_trans->gateway_response = json_encode($re_bt);
        //$obj_trans->gateway_type = 'payal';

        $obj_trans->save();


        $obj_donation = new Donation();

        $obj_donation->user_id = $request['user_id'];
        $obj_donation->wishlist_id = 0;
        $obj_donation->target_id = 0;
        $obj_donation->target_type = 'company';
        $obj_donation->amount = $request['amount'];
        $obj_donation->save();

        $this->__is_paginate = false;
        $this->__collection = false;

        return $this->__sendResponse('Transactions', $re_bt, 200, 'Company donation submitted successfully.');
    }

    private function _btAddCustomer(Request $request, $obj_user){
        $ob_braintree = new BrainTree();

        $params['id'] = $obj_user->id;
        $params['firstName'] = $obj_user->first_name;
        $params['lastName'] = $obj_user->last_name;
        $params['email'] = $obj_user->email;

        return $ob_braintree->addCustomer($params);
    }

    public function updateSubscription(Request $request)
    {
        $obj_noti = new Notification();

        $obj_noti->actor_id = 1;
        $obj_noti->target_id = 1;
        $obj_noti->reference_id = 1;
        $obj_noti->type = 'push';
        $obj_noti->title = 'hook';
        $obj_noti->description = json_encode($request->all());
        $obj_noti->is_notify = 1;
        $obj_noti->is_read = 1;
        $obj_noti->is_viewed = 1;

        $obj_noti->save();

        $param_rules['user_id']         = 'required';

        $this->__is_ajax = true;

        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;



        $this->__is_paginate = false;
        $this->__collection = false;

        return $this->__sendResponse('Transactions', $re_bt, 200, 'Company donation submitted successfully.');
    }

    public function userSubscription(Request $request)
    {
        $param_rules['user_id']         = 'required';

        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        $re = UserSubscription::getByUserId($request['user_id']);

        $this->__is_paginate = false;
        //$this->__collection = false;

        return $this->__sendResponse('UserSubscription', $re, 200, 'User Subscription retrieved successfully.');
    }


}
