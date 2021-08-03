<?php

namespace App\Http\Controllers;

use App\Http\Middleware\LoginAuth;
use App\Models\CommissionEvents;
use App\Models\Lead;
use App\Models\Status;
use App\Models\Type;
use App\Models\User;
use App\Models\UserCommission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class UserCommissionController extends Controller
{
    function __construct(){

        parent::__construct();
        $this->middleware(LoginAuth::class, ['only' => ['index', 'store', 'update', 'commissionReport', 'indexView', 'show', 'destroy','updateView','viewUserReport'
                                            , 'commissionView', 'exportCSV'
        ]]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $param['company_id'] = $request['company_id'];
        $param['agent_ids'] = isset($request['agent_ids']) ? $request['agent_ids'] : '';
        $param['commission_events'] = isset($request['commission_events']) ? $request['commission_events'] : '';
        $param['start_date'] = isset($request['start_date']) ? $request['start_date'] : '';
        $param['end_date'] = isset($request['end_date']) ? $request['end_date'] : '';
        $param['order_by'] = isset($request['order_by']) ? $request['order_by'] : 'id';
        $param['order_type'] = isset($request['order_type']) ? $request['order_type'] : 'desc';

        $list = UserCommission::getList($param);
        $this->__is_ajax = true;
        return $this->__sendResponse('UserCommission', $list, 200,'User commission list retrieved successfully.');
    }

    public function exportCSV(Request $request)
    {
        $param['company_id'] = $request['company_id'];
        $param['agent_ids'] = isset($request['agent_ids']) ? $request['agent_ids'] : '';
        $param['commission_events'] = isset($request['commission_events']) ? $request['commission_events'] : '';
        $param['start_date'] = isset($request['start_date']) ? $request['start_date'] : '';
        $param['end_date'] = isset($request['end_date']) ? $request['end_date'] : '';
        $param['order_by'] = isset($request['order_by']) ? $request['order_by'] : 'id';
        $param['order_type'] = isset($request['order_type']) ? $request['order_type'] : 'desc';
        $param['is_all'] = 1;

        $data = [];
        $columns = ['user_name', 'title', 'commission_event', 'commission', 'month'];
        $result = UserCommission::getList($param);
        foreach($result as $row){
            $tmp = new \stdClass();
            $tmp->user_name = "{$row->first_name} {$row->last_name}";
            $tmp->title = $row->title;
            $tmp->commission_event = $row->commission_event;
            $tmp->commission = $row->commission;
            $tmp->month = date('d,M Y', strtotime($row->target_month));
            $data[] = $tmp;
        }
       
        return $this->__exportCSV($columns, $data);
    }

    public function viewUserReport(Request $request)
    {
        $param['user_id'] = $request['user_id'];
        $param['company_id'] = $request['company_id'];
        $param['is_paginate'] = false;

        $response['status'] = Status::getList($param);
        $response['agent'] = User::getTenantUserList($param);
        $response['type'] = Type::whereIn('tenant_id', [$request['company_id']])->whereNull('deleted_at')->get();

        $this->__view = 'tenant.team-performance.user_report';
        $this->__is_paginate = false;
        $this->__collection = false;

        return $this->__sendResponse('LeadReport', $response, 200, 'Lead Report view retrieved successfully.');

    }

    public function indexView(Request $request)
    {
        $param['user_id'] = $request['user_id'];
        $param['company_id'] = $request['company_id'];
        $param['is_paginate'] = false;

        $response['lead'] = Lead::getList($param);
        $response['agent'] = User::getTenantUserList($param);
        $response['commission_event'] = CommissionEvents::getList($param);

        $this->__view = 'tenant.commission.add_comm';
        $this->__is_paginate = false;
        $this->__is_collection = false;

        return $this->__sendResponse('UserCommission', $response, 200, 'Assigned lead list retrieved successfully.');
    }

    public function commissionView(Request $request)
    {
        $param['user_id'] = $request['user_id'];
        $param['company_id'] = $request['company_id'];
        $param['is_paginate'] = false;

        $response['agent'] = User::getTenantUserList($param);
        $response['commission_event'] = CommissionEvents::getList($param);

        $this->__view = 'tenant.commission.commission_mgmt';
        $this->__is_paginate = false;
        $this->__is_collection = false;

        return $this->__sendResponse('UserCommission', $response, 200, 'Assigned lead list retrieved successfully.');
    }

    public function updateView(Request $request)
    {
        $param['user_id'] = $request['user_id'];
        $param['company_id'] = $request['company_id'];
        $param['is_paginate'] = false;

        $response['lead'] = Lead::getList($param);
        $response['agent'] = User::getTenantUserList($param);
        $response['commission_event'] = CommissionEvents::getList($param);

        $this->__view = 'tenant.commission.edit_comm';
        $this->__is_paginate = false;
        $this->__is_collection = false;

        return $this->__sendResponse('UserCommission', $response, 200, 'Assigned lead list retrieved successfully.');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function viewCommissionReport(Request $request){

        $param['user_id'] = $request['user_id'];
        $param['company_id'] = $request['company_id'];
        $param['is_paginate'] = false;


        $response['status'] = Status::getList($param);
        $response['agent'] = User::getTenantUserList($param);
        $response['type'] = Type::whereIn('tenant_id', [$request['company_id']])->whereNull('deleted_at')->get();

        $this->__view = 'tenant.team-performance.comm-report';
        $this->__is_paginate = false;
        $this->__collection = false;
        return $this->__sendResponse('Lead', $response, 200,'Lead has been retrieved successfully.');
    }

    public function commissionReport(Request $request)
    {
        //$time_slot_map['today'] = 'INTERVAL 1 MONTH';
        //$time_slot_map['yesterday'] = 'INTERVAL 1 MONTH';
        //$time_slot_map['week'] = 'INTERVAL 1 MONTH';
        //$time_slot_map['last_week'] = 'INTERVAL 1 MONTH';
        $time_slot_map['month'] = 'INTERVAL 1 MONTH';
        $time_slot_map['last_month'] = 'INTERVAL 1 MONTH';
        $time_slot_map['bi_month'] = 'INTERVAL 15 DAY';
        $time_slot_map['bi_year'] = 'INTERVAL 6 MONTH';
        $time_slot_map['year'] = 'INTERVAL 1 YEAR';
        $time_slot_map['last_year'] = 'INTERVAL 1 YEAR';
        $time_slot_map['all_time'] = '';

        $graph_type['percentage'] = 'percentage';
        $graph_type['amount'] = 'amount';

        $param['is_web'] = (strtolower($this->call_mode) == 'api') ? 0 : 1;
        $default_time_slot = ($param['is_web']) ? 'all_time' : 'year';

        $param['company_id'] = $request['company_id'];
        $param['time_slot'] = isset($request['time_slot']) ? (isset($time_slot_map[$request['time_slot']])) ? $time_slot_map[$request['time_slot']] : $time_slot_map['month'] : $time_slot_map['month'];
        $param['slot'] = isset($request['time_slot']) ? (isset($time_slot_map[$request['time_slot']])) ? $request['time_slot'] : $default_time_slot : $default_time_slot;
        $param['user_id'] = isset($request['target_user_id']) ? trim($request['target_user_id'],' ,') : '';
        $param['type'] = isset($request['type']) ? (isset($graph_type[$request['type']])) ? $graph_type[$request['type']] : $graph_type['percentage'] : $graph_type['percentage'];
        $param['lead_type_id'] = isset($request['lead_type_id']) ? trim($request['lead_type_id'],' ,') : '';
        $param['lead_type_id'] = isset($request['type_id']) ? trim($request['type_id'],' ,') : $param['lead_type_id'];

        $this->__is_ajax = true;
        $list = UserCommission::getCommissionReport($param);

        $this->__is_paginate = false;
        $this->__collection = false;
        return $this->__sendResponse('UserCommission', $list, 200,'User commission list retrieved successfully.');
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
        $param_rules['company_id'] = 'required|exists:user,company_id';
        $param_rules['target_id'] = 'required|exists:user,id';
        $param_rules['lead_id'] = 'required|exists:lead,id';
        $param_rules['month'] = 'required|date_format:"Y-m-d"';
        $param_rules['commission'] = 'required|numeric|between:0.01,9999999.99';
        $param_rules['commission_event'] = 'required';
        $param_rules['comments'] = 'nullable';
        //Property Sold,Profit,Apointment,Special Instance,Contracts
        $this->__is_ajax = true;

        $param_rules_messages = array(
            'target_id.required' => 'The user name field is required.'
        );

        $response = $this->__validateRequestParams($request->all(), $param_rules, $param_rules_messages);

        if ($this->__is_error == true)
            return $response;

        $commission_count = UserCommission::where('user_id', $request['target_id'])
            ->where('target_month', $request['month'])
            ->where('lead_id', $request['lead_id'])
            ->count();

        if($commission_count){
            $errors['commission'] = 'Already lead commission is added';
            return $this->__sendError('Validation Error.', $errors);
        }


        $obj_commission = new UserCommission();
        $obj_commission->tenant_id = $request['company_id'];
        $obj_commission->user_id = $request['target_id'];
        $obj_commission->lead_id = $request['lead_id'];
        $obj_commission->commission = $request['commission'];
        $obj_commission->commission_event = $request['commission_event'];
        $obj_commission->target_month = $request['month'];
        $obj_commission->comments = $request['comments'];
        $obj_commission->save();

        $param['id'] = $obj_commission->id;
        $param['company_id'] = $request['company_id'];

        $this->__is_paginate = false;
        $this->__is_collection = false;
        return $this->__sendResponse('UserCommission', UserCommission::getDetail($param), 200, 'User commission has been added successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)

    {
        $param_rules['id']       = 'required|exists:user_commission,id,tenant_id,'.$request['company_id'];

        $this->__is_ajax = true;
        $response = $this->__validateRequestParams(['id' => $id], $param_rules);

        if ($this->__is_error == true)
            return $response;

        $param['id'] = $id;
        $param['company_id'] = $request['company_id'];

        $this->__is_paginate = false;
        $this->__is_collection = false;
        return $this->__sendResponse('UserCommission', UserCommission::getDetail($param), 200, 'User commission has been retrieved successfully.');

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
        $param_rules['id'] = 'required|exists:user_commission,id';
        $param_rules['company_id'] = 'required|exists:user,company_id';
        $param_rules['target_id'] = 'required|exists:user,id';
        $param_rules['lead_id'] = 'required|exists:lead,id';
        $param_rules['target_month'] = 'required|date_format:"Y-m-d"';
        $param_rules['commission'] = 'required|numeric|between:0.01,9999999.99';
        $param_rules['commission_event'] = 'required';
        //Property Sold,Profit,Apointment,Special Instance,Contracts

        $request['id']= $id;
        $this->__is_ajax = true;
        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if ($this->__is_error == true)
            return $response;

        $commission_count = UserCommission::where('user_id', $request['user_id'])
            ->where('target_month', $request['target_month'])
            ->where('id', '<>',$id)
            ->count();

        if($commission_count){
            $errors['commission'] = 'Already commission is added';
            return $this->__sendError('Validation Error.', $errors);
        }

        $obj_commission = UserCommission::where('tenant_id', $request['company_id'])->find($id);
        $obj_commission->user_id = $request['target_id'];
        $obj_commission->lead_id = $request['lead_id'];
        $obj_commission->commission = $request['commission'];
        $obj_commission->commission_event = $request['commission_event'];
        $obj_commission->target_month = $request['target_month'];
        $obj_commission->comments = $request['comments'];
        $obj_commission->save();


        $this->__is_paginate = false;
        $this->__is_collection = false;
        return $this->__sendResponse('UserCommission', UserCommission::find($id), 200, 'User commission has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $param_rules['id']       = 'required|exists:user_commission,id,tenant_id,'.$request['company_id'];

        $this->__is_ajax = true;
        $response = $this->__validateRequestParams(['id' => $id], $param_rules);

        if($this->__is_error == true)
            return $response;

        UserCommission::destroy($request['id']);

        $this->__is_paginate = false;
        $this->__is_collection = false;
        $this->__collection = false;
        return $this->__sendResponse('UserCommission', [], 200,'User commission has been deleted successfully.');
    }
}
