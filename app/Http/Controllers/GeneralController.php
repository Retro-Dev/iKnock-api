<?php

namespace App\Http\Controllers;

use App\Http\Middleware\LoginAuth;
use App\Models\MailTemplate;
use App\Models\Setting;
use App\Models\StorageUnitSize;
use App\Models\User;
use App\Models\UserGenre;
use App\Models\UserProperty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class GeneralController extends Controller
{


    function __construct(){

        parent::__construct();
        $this->middleware(LoginAuth::class, ['only' => ['subscriptionDetail']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSettingValue(Request $request)
    {
        $this->__is_paginate =  false;
        $this->__collection =  false;
        $list = Setting::getByKey($request['key']);
        return $this->__sendResponse('Settings', [$list], 200,'Setting value retrieved successfully.');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getMarketingMailTemplate(Request $request)
    {
        //$this->__is_paginate =  false;
        $list = MailTemplate::select('id', 'identifier', 'hint')->where('type','user_marketing')->whereNull('deleted_at')->get();
        $this->__is_paginate =  false;
        $this->__collection =  false;
        return $this->__sendResponse('UserGenre', $list, 200,'User Genre retrieved successfully.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function subscriptionDetail(Request $request)
    {
        $this->__is_paginate =  false;
        $this->__collection =  false;
        $subscription_amount = Setting::getByKey('gold_member_charges')->value;

        $detail[0]['subscription']['title'] = 'Gold Member'; //GOLD_MEMBER_AMOUNT
        $detail[0]['subscription']['amount'] = $subscription_amount;
        $detail[0]['subscription']['detail'] = "Upgrade to Gold Member for $${subscription_amount} per month.";
        $user = User::getById($request['user_id']);
        //print_r($user[0]['id']);exit;

        $is_user_subscribed = (User::verifySubscription($user[0]['id'], $user[0]['user_group_id'], $user[0]['subscription_expiry_date']))? true : false;

        $detail[0]['is_user_subscribed'] = $is_user_subscribed;

        return $this->__sendResponse('Subscription', $detail, 200,'Subscription detail retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
}
