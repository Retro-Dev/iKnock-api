<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class AdminController extends Controller
{
    function __construct(){

        Parent::__construct();

        $this->__module = 'admin/';
        
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $products = Admin::all();
        return $this->__sendResponse($products->toArray(), 200,'Products retrieved successfully.');
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
    public function store(Request $request)
    {
        $param_rules['name']        = 'required|string|max:100';
        $param_rules['email']       = 'required|string|email|max:150|unique:admin';
        //$param_rules['mobile_no']   = 'required|string|max:20|unique:admin';
        $param_rules['password']    = 'required|string|min:6';

        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        $request['password'] = $this->__encryptedPassword($request->password);

        $admin_id = Admin::create($request->all());

        $this->__is_paginate = false;
        //$this->__collection = false;

        return $this->__sendResponse('Admin', Admin::getById($admin_id), 200,'Admin has been added successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
    public function update(Request $request, $id)
    {
        //
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
    public function login(Request $request)
    {
        $param_rules['email']       = 'required|string|email|max:150';
        $param_rules['password']    = 'required|string';

        $this->__view = 'login/login';

        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response ;
           // return $this->__sendError('Validation Error.', $response) ;

        $admin = Admin::login($request->email, $this->__encryptedPassword($request->password));

        if(count($admin) <= 0){
            $errors['email'] = Lang::get('auth.failed');
            return $this->__sendError('Validation Error.', $errors);
        }

        $this->__view = 'dashboard';
        $this->__is_paginate = false;
        $this->__is_redirect = true;
        //$this->__collection = false;
        return $this->__sendResponse('Admin', $admin, 200,'Admin has been logged in successfully.');
    }

    public function forgotPassword(Request $request)
    {
        $param_rules['email']   = 'required|string|email|max:150';

        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        // get data against email address
        $admin = Admin::getByEmail($request->email);

        if(count($admin) <= 0){
            $errors['email'] = Lang::get('passwords.user');
            return $this->__sendError('Validation Error.', $errors);
        }

        // update forgot password hash and update hash date
        $hash = $this->__generateUserHash($request->email);


        Admin::updateByEmail($request->email, [
            'forgot_password_hash' => $hash,
            'forgot_password_hash_date' => Carbon::now()
        ]);


        $mail_params['USER_NAME'] = $admin[0]->first_name . ' ' . $admin[0]->last_name;
        $mail_params['CONFIRMATION_LINK'] = env('APP_URL')."/user/forgot/password/$hash";
        $mail_params['USER_LINK'] = env('APP_URL').'/user/login';
        $mail_params['APP_NAME'] = env('APP_NAME');

        // make forgot password url and implement its email configuration.
        $this->__sendMail('user_forgot_password', $request->email, $mail_params);

        // send email to user

        $this->__is_paginate = false;
        return $this->__sendResponse('Admin', $admin, 200,$errors['email'] = Lang::get('passwords.sent'));
    }

}
