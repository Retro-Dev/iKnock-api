<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $list = Notification::paginate(Config::get('constants.PAGINATION_PAGE_SIZE'));
        return $this->__sendResponse('Notification', $list, 200,'Notification retrieved successfully.');
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
        $param_rules['admin_id'] = 'required';
        $param_rules['user_id'] = 'required';
        $param_rules['title'] = 'required';
        $param_rules['description'] = 'required';

        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        $obj = new Notification();

        $obj->admin_id = $request->admin_id;
        $obj->user_id = $request->user_id;
        $obj->title = $request->title;
        $obj->description = $request->description;
        $obj->send_type = isset($request->send_type) ? $request->send_type: 'user';
        $obj->is_send = isset($request->is_send) ? $request->is_send : 0;

        $obj->save();

        $this->__is_paginate = false;
        return $this->__sendResponse('Notification', Notification::getById($obj->id), 200,'Your notification has been added successfully.');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $param_rules['id'] = 'required|exists:notification';

        $response = $this->__validateRequestParams(['id' => $id], $param_rules);

        if($this->__is_error == true)
            return $response;

        $this->__is_paginate = false;
        return $this->__sendResponse('Notification', Notification::getById($id), 200,'notification retrieved successfully.');
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
