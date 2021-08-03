<?php

namespace App\Http\Controllers;

use App\Http\Middleware\LoginAuth;
use App\Models\Media;
use App\Models\UserTrainingScript;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class UserTrainingScriptController extends Controller
{

    function __construct(){

        parent::__construct();
        $this->middleware(LoginAuth::class, ['only' => ['index', 'store', 'update', 'deleteTrainingScript', 'show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $order_by_map['training_title'] = 'title';
        $order_by = (isset($order_by_map[$request['order_by']]))? $order_by_map[$request['order_by']] : 'created_at';
        $order_type = (isset($request['order_type']))? $request['order_type'] : 'desc';

        $search = $this->isArray($request['search']);
        $search = isset($search) ? $search : '';
        $query = UserTrainingScript::where('tenant_id',$request['company_id']);
        $list = $query->where(function($query_clause)use ($search) {
                    $query_clause->orWhere('title', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
                })
            ->orderBy($order_by, $order_type)
            ->paginate(Config::get('constants.PAGINATION_PAGE_SIZE'));
        $this->__is_ajax = true;
        return $this->__sendResponse('UserTrainingScript', $list, 200,'User training script list retrieved successfully.');
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
    
        $param_rules['title'] = 'required|max:100';
        $param_rules['description'] = 'required';
        $param_rules['image_url']  = 'nullable';
        $param_rules['image_url.*'] = 'nullable|mimes:pdf,jpeg,png,jpg,gif,svg|max:8192';

        //print_r($request->all());exit;

        $this->__is_ajax = true;
        $this->__is_redirect = true;
        $this->__view = 'tenant/training/add_script';
        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if ($this->__is_error == true)
            return $response;

        $obj_training = new UserTrainingScript();
        $obj_training->tenant_id = $request['company_id'];
        $obj_training->title = $request['title'];
        $obj_training->description = $request['description'];
        $obj_training->save();

        $system_image_url = [];

        if ($request->hasFile('image_url')) {
            foreach ($request->image_url as $image_url) {
                // $obj is model
                //$image_url->getClientOriginalExtension();
                $system_image_url[] = $this->__moveUploadFile(
                    $image_url,
                    md5($request->title . 'training' . time().rand(10,99)),
                    Config::get('constants.MEDIA_IMAGE_PATH')
                );
            }
            Media::createBulk($obj_training->id, 'training', 'image', $system_image_url);
        }



        $this->__is_paginate = false;
        $this->__is_collection = false;
        $this->__view = 'tenant/training';
        return $this->__sendResponse('UserTrainingScript', UserTrainingScript::find($obj_training->id), 200, 'Your training script has been added successfully.');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $param_rules['id']       = 'required|exists:user_training_script,id,tenant_id,'.$request['company_id'];

        $this->__is_ajax = true;
        $response = $this->__validateRequestParams(['id' => $id], $param_rules);

        if ($this->__is_error == true)
            return $response;
        $this->__is_paginate = false;
        $this->__is_collection = false;

        return $this->__sendResponse('UserTrainingScript', UserTrainingScript::find($id), 200, 'user training script has been retrieved successfully.');

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
        $param_rules['id'] = 'required|exists:user_training_script,id';
        $param_rules['title'] = 'required|max:100';
        $param_rules['description'] = 'required';
        $param_rules['image_url']  = 'nullable';
        $param_rules['image_url.*'] = 'nullable|mimes:pdf,jpeg,png,jpg,gif,svg|max:2048';

        $this->__is_redirect = true;
        $this->__is_ajax = true;
        $this->__view = "tenant/training/edit/$id";
        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if ($this->__is_error == true)
            return $response;

        $obj_training = UserTrainingScript::find($id);
        //$obj_training->tenant_id = $request['company_id'];
        $obj_training->title = $request['title'];
        $obj_training->description = $request['description'];
        $obj_training->save();

        $system_image_url = [];

        if ($request->hasFile('image_url')) {
            foreach ($request->image_url as $image_url) {
                // $obj is model
                $system_image_url[] = $this->__moveUploadFile(
                    $image_url,
                    md5($request->title . 'training' . time().rand(10,99)),
                    Config::get('constants.MEDIA_IMAGE_PATH')
                );
            }
            Media::createBulk($obj_training->id, 'training', 'image', $system_image_url);
        }

        if(isset($request['delete_media']) && !empty($request['delete_media']))
            Media::deleteByIds($request['delete_media'], $id);

        $this->__is_paginate = false;
        $this->__is_collection = false;
        $this->__view = 'tenant/training';
        return $this->__sendResponse('UserTrainingScript', UserTrainingScript::find($id), 200, 'Training script has been updated successfully.');


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function deleteTrainingScript(Request $request)
    {
        $param_rules['id']       = 'required|exists:user_training_script,id,tenant_id,'.$request['company_id'];

        $this->__is_ajax = true;
        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        UserTrainingScript::destroy($request['id']);

        $this->__is_paginate = false;
        $this->__is_collection = false;
        $this->__collection = false;
        return $this->__sendResponse('UserTraining', [], 200,'Status has been deleted successfully.');
    }

}
