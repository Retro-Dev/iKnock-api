<?php

namespace App\Http\Controllers;

use App\Http\Middleware\LoginAuth;
use App\Models\CommissionEvents;
use App\Models\Lead;
use App\Models\LeadHistory;
use App\Models\Status;
use App\Models\Type;
use App\Models\UserLeadKnocks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class CompanyController extends Controller
{
    function __construct(){

        parent::__construct();
        $this->middleware(LoginAuth::class, ['only' => ['storeStatus', 'storeType', 'statusList', 'typeList', 'updateStatus', 'deleteType'
                            , 'updateStatusValue', 'updateTypeValue', 'deleteStatus', 'storeCommissionEvent', 'getCommissionEventList']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function storeStatus(Request $request)
    {
        //$param_rules['title']       = 'required|string|max:100|regex:/(?!^\d+$)^.+$/';
        $param_rules['title']       = 'required|string|max:100|regex:/(?!^\d+$)^.+$/|unique:type,NULL,deleted_at,id,tenant_id,'.$request['company_id'];
        $param_rules['code']        = 'required|string|max:2|unique:status,code,NULL,id,deleted_at,NULL,tenant_id,'.$request['company_id'];
        $param_rules['color_code']  = 'required';
        $this->__is_ajax = true;
        $response = $this->__validateRequestParams($request->all(), $param_rules, [
            'title.unique' => 'The Lead status has already been taken.'
        ]);

        if($this->__is_error == true)
            return $response;

        $status_count = Status::where('tenant_id', $request['company_id'])->whereNull('deleted_at')->count();

        $obj_status = new Status();
        $obj_status->title      = $request['title'];
        $obj_status->tenant_id = $request['company_id'];
        $obj_status->code = $request['code'];
        $obj_status->color_code = $request['color_code'];
        $obj_status->order_by = $status_count + 1;
        $obj_status->save();

        $this->__is_paginate = false;
        $this->__is_collection = false;
        return $this->__sendResponse('Status', Status::getById($obj_status->id), 200,'Status has been added successfully.');
    }

    public function updateStatusValue(Request $request)
    {
        $param_rules['id']       = 'required|exists:status,id,tenant_id,'.$request['company_id'];
        $param_rules['title']       = 'required|string|max:100';
        $param_rules['code']        = 'required|string|max:2';
        $param_rules['color_code']  = 'required';
        $this->__is_ajax = true;
        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        $status_count = Status::getByCode($request['id'], $request['code'], $request['company_id']);

        if($status_count > 0){
            $errors['code'] = 'Code is already been taken';
            return $this->__sendError('Validation Error.', $errors);
        }

        $obj_status = Status::find($request['id']);
        $obj_status->title      = $request['title'];
        $obj_status->code = $request['code'];
        $obj_status->color_code = $request['color_code'];
        $obj_status->save();

        $this->__is_paginate = false;
        $this->__is_collection = false;
        $this->__collection = false;
        return $this->__sendResponse('Status', Status::getById($obj_status->id), 200,'Status has been added successfully.');
    }

    public function updateTypeValue(Request $request)
    {
        $param_rules['id']       = 'required|exists:type,id,tenant_id,'.$request['company_id'];
        $param_rules['code']        = 'required|string|max:2';
        $param_rules['title']       = 'required|string|max:100';

        $this->__is_ajax = true;
        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        $type_count = Type::getByCode($request['id'], $request['code'], $request['company_id']);

        if($type_count > 0){
            $errors['code'] = 'Code is already been taken';
            return $this->__sendError('Validation Error.', $errors);
        }


        $obj_type = Type::find($request['id']);
        $obj_type->code      = $request['code'];
        $obj_type->title      = $request['title'];

        $obj_type->save();

        $this->__is_paginate = false;
        $this->__is_collection = false;
        $this->__collection = false;
        return $this->__sendResponse('Status', Status::getById($obj_type->id), 200,'Status has been added successfully.');
    }

    public function deleteStatus(Request $request)
    {
        $param_rules['id']       = 'required|exists:status,id,tenant_id,'.$request['company_id'];

        $this->__is_ajax = true;
        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        $status_detail = Status::where('id',$request['id'])->where('tenant_id', $request['company_id'])->first();
        if($status_detail->is_permanent){
            $errors['code'] = 'Unable to delete default status';
            return $this->__sendError('Validation Error.', $errors);
        }

        Status::deleteStatus($request['id']);
        $this->__is_paginate = false;
        $this->__is_collection = false;
        $this->__collection = false;
        return $this->__sendResponse('Status', [], 200,'Status has been deleted successfully.');
    }

    public function deleteType(Request $request)
    {
        $param_rules['id']       = 'required|exists:type,id,tenant_id,'.$request['company_id'];

        $this->__is_ajax = true;
        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        Type::deleteType($request['id']);

        $this->__is_paginate = false;
        $this->__is_collection = false;
        $this->__collection = false;
        return $this->__sendResponse('Type', [], 200,'Status has been deleted successfully.');
    }

    public function storeType(Request $request)
    {
        //$param_rules['code']        = 'required|string|max:2|unique:type,NULL,deleted_at,id,tenant_id,'.$request['company_id'];
        $param_rules['code']        = 'required|string|max:2';
        $param_rules['title']        = 'required|string|max:100|regex:/(?!^\d+$)^.+$/|unique:type,NULL,deleted_at,id,tenant_id,'.$request['company_id'];
        $this->__is_ajax = true;
        $response = $this->__validateRequestParams($request->all(), $param_rules,[
                'title.unique' => 'The Lead type has already been taken.'
            ]);
        
        if($this->__is_error == true)
            return $response;

        $obj_type = new Type();
        $obj_type->title      = $request['title'];
        $obj_type->tenant_id = $request['company_id'];
        $obj_type->code = $request['code'];
        $obj_type->save();
        //$obj_type->id;

        $this->__is_paginate = false;
        $this->__is_collection = false;
        return $this->__sendResponse('Type', Type::getById($obj_type->id), 200,'Type has been added successfully.');
    }

    public function statusList(Request $request)
    {
        $order_by_map['status'] = 'title';
        $order_by_map['code'] = 'code';

        $request['company_id'];

        $status = [$request['company_id']];
        if($this->call_mode != 'api')
            $status = [$request['company_id']];

        $request['order_type'] = (isset($request['order_type'])) ? $request['order_type'] : 'desc';
        $order_by = (isset($order_by_map[$param['order_by']]))? $order_by_map[$request['order_by']] : 'id';

        $response = Status::whereIn('tenant_id', $status)->whereNull('deleted_at')
            //->orderBy($order_by, $request['order_type'])
            ->orderBy('order_by')
            ->get();
        $this->__is_paginate = false;
        $this->__is_ajax = true;
        return $this->__sendResponse('Status', $response, 200,'Status list retrieved successfully.');
    }

    public function typeList(Request $request)
    {
        $order_by_map['status'] = 'title';
        $order_by_map['code'] = 'code';

        $type = [$request['company_id']];
        if($this->call_mode != 'api')
            $type = [$request['company_id']];

        $request['order_type'] = (isset($request['order_type'])) ? $request['order_type'] : 'asc';
        $order_by = (isset($order_by_map[$param['order_by']]))? $order_by_map[$request['order_by']] : 'id';

        $response = Type::whereIn('tenant_id', $type)->whereNull('deleted_at')
            ->orderBy($order_by, $request['order_type'])
            ->get();

        $this->__is_ajax = true;
        $this->__is_paginate = false;
        return $this->__sendResponse('Type', $response, 200,'Type list retrieved successfully.');
    }


    public function getStatusDetail(Request $request, $id)
    {
        $param_rules['id'] = 'required|exists:status,id';

        $this->__is_ajax = true;
        $response = $this->__validateRequestParams(['id' => $id], $param_rules);

        if($this->__is_error == true)
            return $response;


        $this->__is_paginate = false;
        $this->__is_collection = false;
        $this->__collection = false;

        //$detail = Status::find($id)->whereIn('tenant_id', ['0', $request['company_id']])->whereNull('deleted_at')->get();
        $detail = Status::where('id',$id)->whereIn('tenant_id', ['0', $request['company_id']])->whereNull('deleted_at')->first();
        return $this->__sendResponse('Status', $detail, 200,'Status list retrieved successfully.');
    }

    public function getTypeDetail(Request $request, $id)
    {
        $request['company_id'];
        $param_rules['id'] = 'required|exists:type,id';

        $this->__is_ajax = true;
        $response = $this->__validateRequestParams(['id' => $id], $param_rules);

        if($this->__is_error == true)
            return $response;


        $this->__is_paginate = false;
        $this->__is_collection = false;
        $this->__collection = false;

        return $this->__sendResponse('Type', Type::where('id',$id)->whereIn('tenant_id', ['0', $request['company_id']])->whereNull('deleted_at')->first(), 200,'Type list retrieved successfully.');
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

    public function updateStatus(Request $request)
    {

        $param_rules['lead_id'] = 'required|exists:lead,id';
        $param_rules['status_id'] = 'required|exists:status,id';

        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        $obj_lead = Lead::find($request['lead_id']);
        $status_id = $obj_lead->status_id;
        $obj_lead->assignee_id = $request['user_id'];
        $obj_lead->status_id = $request['status_id'];
        $obj_lead->save();

        if($status_id != $request['status_id']) {
            $obj_lead_history = LeadHistory::create([
                'lead_id' => $request['lead_id'],
                'title' => '',
                'assign_id' => $request['user_id'],
                'status_id' => $request['status_id'] //$obj_lead->status_id
            ]);

            Status::incrementLeadCount($obj_lead->status_id);
            Status::decrementLeadCount($status_id);
            UserLeadKnocks::insertLeadKnocks($request->all());
        }


        $this->__is_paginate = false;
        $this->__is_collection = false;
        return $this->__sendResponse('Lead', Lead::getById($request['lead_id']), 200,'Lead has been retrieved successfully.');
    }

    public function updateStatusSorting(Request $request)
    {
        $param_rules['type'] = 'nullable';
        $param_rules['ids'] = 'required';


        $this->__is_ajax = true;
        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        $type = (isset($request['type']))? $request['type'] : 'status';
        $this->__is_ajax = true;
        $this->__is_paginate = false;

        $request['ids'] = explode(',', $request['ids']);
        $update_model = 'App\Models\Status';
        if($request['type'] == 'type') {
            $update_model = 'App\Models\Type';
            if(isset($request['template_id']) && !empty($request['template_id'])){
                $update_model = 'App\Models\TemplateFields';
            }
        }

        foreach($request['ids'] as $key => $id){
            $field = $id;
            $id = (int)$id;
            if($update_model =='App\Models\Status') {
                $update_model::updateOrderBy($request['company_id'], $id, $key + 1);
            }
            if(!empty($id)) {
                if($update_model !='App\Models\Status') {
                    $obj = $update_model::find($id);
                    $obj->order_by = $key + 1;
                    $obj->save();
                }
            }
        }

        return $this->__sendResponse('Status', [], 200,'User list retrieved successfully.');
    }

    public function storeCommissionEvent(Request $request)
    {
        $param_rules['title']        = 'required|string|max:100';
        $this->__is_ajax = true;
        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        $obj_type = new CommissionEvents();
        $obj_type->title      = $request['title'];
        $obj_type->tenant_id = $request['company_id'];
        $obj_type->save();

        $this->__is_ajax = true;
        $this->__is_paginate = false;
        $this->__collection = false;
        return $this->__sendResponse('Type', CommissionEvents::find($obj_type->id), 200,'Event has been added successfully.');
    }

    public function getCommissionEventList(Request $request)
    {
        $type = [$request['company_id']];
        $order_type = (isset($request['order_type']))? $request['order_type'] : 'desc';
        $response = CommissionEvents::whereIn('tenant_id', $type)->whereNull('deleted_at')
            ->orderBy('title', $order_type)
            ->get();

        $this->__is_ajax = true;
        $this->__is_paginate = false;
        return $this->__sendResponse('Type', $response, 200,'Type list retrieved successfully.');
    }

    public function updateCommissionEvent(Request $request, $id)
    {
        $param_rules['id']       = 'required|exists:commission_events,id,tenant_id,'.$request['company_id'];
        $param_rules['title']       = 'required|string|max:100';

        $this->__is_ajax = true;
        $request['id'] = $id;
        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;


        $obj_type = CommissionEvents::find($request['id']);
        if($obj_type->is_permanent == 0)
            $obj_type->title = $request['title'];

        $obj_type->save();

        $this->__is_paginate = false;
        $this->__is_collection = false;
        $this->__collection = false;
        return $this->__sendResponse('Status', CommissionEvents::find($id), 200,'Commission Event has been updated successfully.');
    }

    public function getCommissionEventDetail(Request $request, $id)
    {
        $request['company_id'];
        $param_rules['id'] = 'required|exists:commission_events,id';

        $this->__is_ajax = true;
        $response = $this->__validateRequestParams(['id' => $id], $param_rules);

        if($this->__is_error == true)
            return $response;


        $this->__is_paginate = false;
        $this->__is_collection = false;
        $this->__collection = false;

        return $this->__sendResponse('Type', CommissionEvents::where('id',$id)->whereIn('tenant_id', [$request['company_id']])->whereNull('deleted_at')->first(), 200,'Type list retrieved successfully.');
    }

    public function deleteCommissionEvent(Request $request)
    {
        $param_rules['id']       = 'required|exists:commission_events,id,tenant_id,'.$request['company_id'];

        $this->__is_ajax = true;
        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        CommissionEvents::destroy($request['id']);

        $this->__is_paginate = false;
        $this->__is_collection = false;
        $this->__collection = false;
        return $this->__sendResponse('CommissionEvents', [], 200,'Commission Event has been deleted successfully.');
    }

}
