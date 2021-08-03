<?php

namespace App\Http\Controllers;

use App\Http\Middleware\LoginAuth;
use App\Models\Lead;
use App\Models\LeadCustomField;
use App\Models\TenantCustomField;
use App\Models\TenantQuery;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class TenantQueryController extends Controller
{

    function __construct(){

        parent::__construct();
        $this->middleware(LoginAuth::class, ['only' => ['store', 'index', 'update', 'show', 'destroy', 'storeTemplateField']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $type = (isset($request['type']))? $request['type'] : 'summary';
        $this->__is_ajax = true;
        $this->__is_paginate = false;

        if(strtolower($request['type']) == 'lead_detail') {
            $list = TenantCustomField::Select('id', 'tenant_id', \DB::raw('`key` as query'), \DB::raw("'lead_detail' as type"))
                ->where('tenant_id', $request['company_id'])
                ->whereNull('deleted_at')
                ->orderBy('order_by')
                ->get();
        }else {
            $list = TenantQuery::select('id', 'tenant_id', 'query', 'type')
                ->where('tenant_id', $request['company_id'])
                ->whereNull('deleted_at')
                ->where('type', $type)
                ->orderBy('order_by')
                ->get();
        }

        return $this->__sendResponse('TenantQuery', $list, 200,'User list retrieved successfully.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateSorting(Request $request)
    {
        $param_rules['type'] = 'required';
        $param_rules['ids'] = 'required';


        $this->__is_ajax = true;
        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        $type = (isset($request['type']))? $request['type'] : 'summary';
        $this->__is_ajax = true;
        $this->__is_paginate = false;

        $page_size = Config::get('constants.PAGINATION_PAGE_SIZE');
        $current_page = $request['current_page'];
        $request['ids'] = explode(',', $request['ids']);
        $update_model = 'App\Models\TenantQuery';
        if($request['type'] == 'lead_detail') {
            $update_model = 'App\Models\TenantCustomField';
            if(isset($request['template_id']) && !empty($request['template_id'])){
                $update_model = 'App\Models\TemplateFields';
            }
        }

        foreach($request['ids'] as $key => $id){
            $field = $id;
            $id = (int)$id;
            if($update_model =='App\Models\TemplateFields') {
                $update_model::updateOrderBy($request['template_id'], $field, $key + 1);
            }
            if(!empty($id)) {
                if($update_model !='App\Models\TemplateFields') {
                    $obj = $update_model::find($id);
                    $obj->order_by = $key + 1;
                    $obj->save();
                }
            }
        }
        if(strtolower($request['type']) == 'lead_detail') {
            $query = TenantCustomField::Select('tenant_custom_field.id', 'tenant_custom_field.tenant_id', \DB::raw('`key` as query'), \DB::raw("'lead_detail' as type"))
                ->where('tenant_id', $request['company_id']);
            $order_by = 'tenant_custom_field';
            if(isset($request['template_id']) && !empty($request['template_id'])){
                $query->join('template_fields', 'template_fields.field', 'tenant_custom_field.id');
                $query->where('template_fields.template_id', $request['template_id']);
                $order_by = 'template_fields';
            }
            $list = $query->whereNull('deleted_at')
                ->orderBy($order_by.'.order_by')
                ->get();
        }else {
            $list = TenantQuery::select('id', 'tenant_id', 'query', 'type')
                ->where('tenant_id', $request['company_id'])
                ->whereNull('deleted_at')
                ->where('type', $type)
                ->orderBy('order_by')
                ->get();
        }

        return $this->__sendResponse('TenantQuery', $list, 200,'User list retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $param_rules['company_id'] = 'required';
        $param_rules['query'] = 'required';
        $param_rules['type'] = 'required|in:summary,appointment,lead_detail';

        $formated_messages = [];
        if(isset($request['type']) && $request['type'] == 'lead_detail') {
            $param_rules['query'] = 'required|regex:/^[A-Za-z0-9][A-Za-z0-9 _\-*]+$/';
            $formated_messages = [
                'query.regex' => 'The Field format is invalid.'
            ];
        }

        $this->__is_ajax = true;
        $response = $this->__validateRequestParams($request->all(), $param_rules, $formated_messages);

        if($this->__is_error == true)
            return $response;

        $this->__is_paginate = false;
        $this->__is_collection = false;
        $id = 0;

        if(strtolower($request['type']) == 'lead_detail'){
            $params['company_id'] = $request['company_id'];
            $tenant_custom = TenantCustomField::getByKey($request['query'], $params);
            if(isset($tenant_custom['id'])){
                $id = $tenant_custom->id;
            }else {
                $tenant_fields_obj = TenantCustomField::getTenantDefaultFields($request['company_id']);
                $fields_count = count($tenant_fields_obj);
                $obj = new TenantCustomField();
                $obj->tenant_id = $request['company_id'];
                $obj->key = $request['query'];
                $obj->key_mask = $request['query'];
                $obj->order_by = $fields_count + 1;
                $obj->rule = '';
                $obj->save();
                $id = $obj->id;
            }

            if(isset($request['template_id']) && !empty($request['template_id'])){
                $data[$id] = 0;
                $data_map[$id] = $request['query'];
                $template_fields = Lead::getTemplateFields($request['template_id']);

                Lead::saveTemplateFields($request['template_id'], $data, $data_map, count($template_fields)+1);
            }


            return $this->__sendResponse('TenantQuery', TenantCustomField::getById($id), 200,'Your lead has been added successfully.');
        }else {
            $obj = new TenantQuery();
            $obj->tenant_id = $request['company_id'];
            $obj->query = $request['query'];
            $obj->type = $request['type'];
            $obj->save();

            return $this->__sendResponse('TenantQuery', TenantQuery::getById($obj->id), 200,'Your field has been added successfully.');
        }


    }

    public function storeTemplateField(Request $request)
    {
        $param_rules['company_id'] = 'required';
        $param_rules['template_id'] = 'required';
        //$param_rules['query'] = 'required|regex:/<[A-Za-z][A-Za-z0-9]*>/';
        $param_rules['query'] = 'required|regex:/^[A-Za-z0-9][A-Za-z0-9 _\-*]+$/';
        $param_rules['file_column'] = 'required';
        //$param_rules['type'] = 'required|in:summary,appointment,lead_detail';

        $this->__is_ajax = true;
        $request['file_column'] = (isset($request['index'])) ? $request['index'] : '';
        $response = $this->__validateRequestParams($request->all(), $param_rules,[
            'query.required' => 'The custom field is required.',
            'query.regex' => 'The custom field format is invalid.'
        ]);

        if ($this->__is_error == true)
            return $response;

        $this->__is_paginate = false;
        $this->__is_collection = false;

        if($request['template_id'] == 'max'){
            $max_template =   Type::getByMax($request['company_id']);
            $request['template_id'] = $max_template->id;
        }

        $params['company_id'] = $request['company_id'];
        $tenant_custom = TenantCustomField::getByKey($request['query'], $params);
        if(isset($tenant_custom['id'])){
            $id = $tenant_custom->id;
        }else {
            $tenant_custom = TenantCustomField::getTenantDefaultFields($request['company_id']);
            $obj = new TenantCustomField();
            $obj->tenant_id = $request['company_id'];
            $obj->key = $request['query'];
            $obj->key_mask = $request['query'];
            $obj->rule = '';
            $obj->order_by = count($tenant_custom) + 1;
            $obj->save();
            $id = $obj->id;
        }

        $template_fields = Lead::getTemplateFields($request['template_id']);

        $data[0]['index'] = 0;
        $data[0]['index_map'] = $request['index'];
        $data[0]['field'] = $id;
        $data[0]['order_by'] = count($template_fields)+1;

        Lead::saveTemplateField($request['template_id'], $data);
        return $this->__sendResponse('TenantQuery', TenantCustomField::getById($id), 200, 'Field has been added successfully.');

    }


    public function updateTemplate(Request $request,$id = 0,$temp_id= 0)
    {
        $tenant_custom = Lead::getByTemplateId($id,$temp_id);
        if(isset($tenant_custom[0]))
            $tenant_custom[0]->is_fixed = 0;
        //print_r($tenant_custom);exit;
        if($tenant_custom[0]->field == 'lead_name' || $tenant_custom[0]->field == 'title'){
            $tenant_custom[0]->key = Config::get('constants.LEAD_TITLE_DISPLAY');
            $tenant_custom[0]->is_fixed = 1;
        }
        return view('tenant.template.update_field',['data'=>$tenant_custom]);

    }

    public function updateTenantDefaulTemplate(Request $request,$id = 0)
    {
        $tenant_custom = Lead::getByTenantTemplateFieldDetail($id);
        return view('tenant.template.update_default_order',['data'=>$tenant_custom]);
        //return view('tenant.template.update_template',['data'=>$tenant_custom]);

    }

    public function updateTemplateField(Request $request)
    {
        $param_rules['field'] = 'required';
        $param_rules['company_id'] = 'required';
        $param_rules['template_id'] = 'required';
        $param_rules['query'] = 'required|regex:/^[A-Za-z0-9][A-Za-z0-9 _\-*]+$/';
        $param_rules['file_column'] = 'required';
        //$param_rules['type'] = 'required|in:summary,appointment,lead_detail';

        $this->__is_ajax = true;
        $request['file_column'] = (isset($request['index'])) ? $request['index'] : '';
        $response = $this->__validateRequestParams($request->all(), $param_rules, [
            'query.required' => 'The custom field is required.',
            'query.regex' => 'The custom field format is invalid.'
        ]);

        if ($this->__is_error == true)
            return $response;

        $this->__is_paginate = false;
        $this->__is_collection = false;


        $params['company_id'] = $request['company_id'];
        $tenant_custom = TenantCustomField::getByKey($request['query'], $params);

        $data['key'] = $request['query'];
        $data['key_mask'] = $request['query'];
        //$data['field'] = $id;

        $save_temp_field = [
            "index_map"=>$request->file_column,
        ];
//        Lead::saveTemplateField($request['template_id'], $data);

        DB::table('template_fields')->where('template_id',$request->template_id)->where('field',$request->field)->update($save_temp_field);
        TenantCustomField::where('id',$request->field)->update($data);

        $tenant_custom_field =  [];
        $this->__collection = false;
        if((is_int($request->field))){
            $tenant_custom_field = TenantCustomField::getById($request->field);
            $this->__collection = true;
        }

        return $this->__sendResponse('TenantQuery', $tenant_custom_field, 200, 'Your lead has been added successfully.');

    }

    public function updateLeadDefaultField(Request $request)
    {
        $param_rules['field_id'] = 'required';
        $param_rules['field'] = 'required';
        $param_rules['company_id'] = 'required';
        //$param_rules['type'] = 'required|in:summary,appointment,lead_detail';

        $this->__is_ajax = true;
        $request['file_column'] = (isset($request['index'])) ? $request['index'] : '';
        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if ($this->__is_error == true)
            return $response;

        $this->__is_paginate = false;
        $this->__is_collection = false;


        $params['company_id'] = $request['company_id'];

        $data['key'] = $request['field'];
        TenantCustomField::where('id',$request->field_id)->update($data);

        $tenant_custom_field =  [];
        $this->__is_paginate = false;
        $this->__collection = false;

        return $this->__sendResponse('TenantQuery', $tenant_custom_field, 200, 'Your lead has been added successfully.');

    }

    public function destroyTemplateField(Request $request, $field_id)
    {
        $param_rules['company_id'] = 'required';
        $param_rules['template_id'] = 'required';
        //$param_rules['field_id'] = 'required';
        //$param_rules['type'] = 'required|in:summary,appointment,lead_detail';

        $this->__is_ajax = true;
        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if ($this->__is_error == true)
            return $response;

        $this->__is_paginate = false;
        $this->__collection = false;

        Lead::deleteTemplateFields($request['template_id'], $field_id);
        return $this->__sendResponse('TenantQuery', [], 200, 'Your Custom field has been deleted successfully.');

    }

    public function destroyLeadDefaultField(Request $request, $field_id)
    {
        $param_rules['company_id'] = 'required';
        //$param_rules['field_id'] = 'required';
        //$param_rules['type'] = 'required|in:summary,appointment,lead_detail';

        $this->__is_ajax = true;
        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if ($this->__is_error == true)
            return $response;

        $this->__is_paginate = false;
        $this->__collection = false;

        Lead::deleteDefaultLeadFields($field_id);
        return $this->__sendResponse('TenantQuery', [], 200, 'Your Custom field has been deleted successfully.');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $table_map['summary'] = 'tenant_query';
        $table_map['appointment'] = 'tenant_query';
        $table_map['lead_detail'] = 'tenant_custom_field';

        $type = (isset($table_map[$request['type']]))? $table_map[$request['type']] : 'tenant_custom_field';
        $param_rules['id']       = "required|exists:$type,id,tenant_id,".$request['company_id'];

        $this->__is_ajax = true;
        $response = $this->__validateRequestParams(['id' => $id], $param_rules);

        if($this->__is_error == true)
            return $response;

        $this->__is_paginate = false;
        $this->__is_collection = false;

        if($request['type'] == 'lead_detail')
            return $this->__sendResponse('TenantQuery', TenantCustomField::getById($id), 200,'Your lead has been added successfully.');

        return $this->__sendResponse('TenantQuery', TenantQuery::getById($id), 200,'Your lead has been added successfully.');
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
        $request['id'] = $id;
        $table_map['summary'] = 'tenant_query';
        $table_map['appointment'] = 'tenant_query';
        $table_map['lead_detail'] = 'tenant_custom_field';

        $type = (isset($table_map[$request['type']]))? $table_map[$request['type']] : 'tenant_query';
        $param_rules['id'] = "required|exists:$type,id,tenant_id,".$request['company_id'];
        $param_rules['query'] = 'required';
        $param_rules['type'] = 'required|in:summary,appointment,lead_detail';

        $this->__is_ajax = true;
        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        $this->__is_paginate = false;
        $this->__is_collection = false;
        if($request['type'] == 'lead_detail'){
            $obj_query = TenantCustomField::find($id);
            $obj_query->key = $request['query'];
            $obj_query->key_mask = $request['query'];
            $obj_query->save();

            return $this->__sendResponse('TenantQuery', TenantCustomField::getById($id), 200,'Your lead detail has been updated successfully.');
        }

        $obj_query = TenantQuery::find($id);
        $obj_query->query = $request['query'];
        $obj_query->type = $request['type'];
        $obj_query->save();


        return $this->__sendResponse('TenantQuery', $obj_query, 200,'Your lead query has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $param_rules['id']       = 'required|exists:tenant_query,id,tenant_id,'.$request['company_id'];
        if($request['type'] == 'lead_detail')
            $param_rules['id']       = 'required|exists:tenant_custom_field,id,tenant_id,'.$request['company_id'];

        $this->__is_ajax = true;
        $response = $this->__validateRequestParams(['id' => $id], $param_rules);

        if($this->__is_error == true)
            return $response;

        if($request['type'] == 'lead_detail')
            TenantCustomField::destroy($request['id']);
        else
            TenantQuery::destroy($request['id']);

        $this->__is_paginate = false;
        $this->__is_collection = false;
        $this->__collection = false;
        return $this->__sendResponse('TenantQuery', [], 200,'Tenant query has been deleted successfully.');
    }
}
