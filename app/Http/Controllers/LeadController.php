<?php

namespace App\Http\Controllers;

use App\Http\Middleware\LoginAuth;
use App\Libraries\History;
use App\Models\Lead;
use App\Models\LeadCustomField;
use App\Models\LeadHistory;
use App\Models\Media;
use App\Models\LeadQuery;
use App\Models\Status;
use App\Models\TemplateFields;
use App\Models\TenantCustomField;
use App\Models\TenantTemplate;
use App\Models\Type;
use App\Models\User;
use App\Models\UserLeadAppointment;
use App\Models\UserLeadKnocks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class LeadController extends Controller
{

    function __construct(){
        
        parent::__construct();
        $this->middleware(LoginAuth::class, ['only' => ['index', 'store', 'update', 'edit', 'show', 'userAssignLead', 'userList'
            , 'history', 'updateQuery', 'createAppointment', 'createOutBoundAppointment', 'leadReport', 'indexView', 'addView', 'listView'
            , 'uploadMedia', 'leadStatsReport', 'bulkUpdate', 'leadUserReport', 'uploadLeads', 'dashboard', 'templateList', 'templateShow'
            , 'templateDestroy', 'statusListView', 'templateFieldList', 'addTemplate', 'updateTemplateFieldIndex', 'executeAppointment'
            , 'deleteTemplate', 'historyExport', 'leadStatusUserReport','destroy', 'leadsHistoryExport'
        ]]);
       
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $param_rules['search'] = 'sometimes';

        $time_slot_map['today'] = 'INTERVAL 1 MONTH';
        $time_slot_map['yesterday'] = 'INTERVAL 1 MONTH';
        $time_slot_map['week'] = 'INTERVAL 1 MONTH';
        $time_slot_map['last_week'] = 'INTERVAL 1 MONTH';
        $time_slot_map['month'] = 'INTERVAL 1 MONTH';
        $time_slot_map['last_month'] = 'INTERVAL 1 MONTH';
        $time_slot_map['year'] = 'INTERVAL 1 YEAR';
        $time_slot_map['last_year'] = 'INTERVAL 1 YEAR';

        $param['search'] = isset($request['search']) ? $request['search'] : '';
        $param['latitude'] = isset($request['latitude']) ? $request['latitude'] : '';
        $param['longitude'] = isset($request['longitude']) ? $request['longitude'] : '';
        $param['lead_type_id'] = isset($request['lead_type_id']) ? $request['lead_type_id'] : '';
        $param['user_ids'] = isset($request['target_user_id']) ? trim($request['target_user_id']) : '';
        $param['status_ids'] = isset($request['status_id']) ? trim($request['status_id']) : '';
        $param['radius'] = isset($request['radius']) ? $request['radius'] : 500;
        $param['time_slot'] = isset($request['time_slot']) ? (isset($time_slot_map[$request['time_slot']])) ? $time_slot_map[$request['time_slot']] : '' : '';
        $param['slot'] = isset($request['time_slot']) ? (isset($time_slot_map[$request['time_slot']])) ? $request['time_slot'] : '' : '';

        $this->__is_ajax = true;
        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

//        $param['user_ids'] = empty($param['user_ids']) ? [] : explode(',',$param['user_ids']);
//        $param['status_ids'] = empty($param['status_ids']) ? [] : explode(',',$param['status_ids']);

        $param['user_id'] = $request['user_id'];
        $param['company_id'] = $request['company_id'];

        $response = Lead::getList($param);

        return $this->__sendResponse('Lead', $response, 200, 'Lead list retrieved successfully.');

    }

    public function leadsMap(Request $request){
        
        return view('tenant.lead.leads_map');

    }

    public function dashboard(Request $request){

        $param['user_id'] = $request['user_id'];
        $param['company_id'] = $request['company_id'];
        $param['is_paginate'] = false;


        $response['status'] = Status::getList($param);
        $response['agent'] = User::getTenantUserList($param);
        $response['type'] = Type::whereIn('tenant_id', [$request['company_id']])->whereNull('deleted_at')->get();

        $this->__view = 'tenant.dashboard.home';

        $this->__is_paginate = false;
        $this->__collection = false;
        return $this->__sendResponse('Lead', $response, 200,'Lead has been retrieved successfully.');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexView(Request $request){

        $param['user_id'] = $request['user_id'];
        $param['company_id'] = $request['company_id'];
        $param['is_paginate'] = false;


        $param['search'] = isset($request['search']) ? $request['search'] : '';
        $param['latitude'] = isset($request['latitude']) ? $request['latitude'] : '';
        $param['longitude'] = isset($request['longitude']) ? $request['longitude'] : '';
        $param['radius'] = isset($request['radius']) ? $request['radius'] : 500;
        $param['user_ids'] = isset($request['user_ids']) ? trim($request['user_ids']) : '';
        $param['status_ids'] = isset($request['status_ids']) ? trim($request['status_ids']) : '';
        $param['start_date'] = isset($request['start_date']) ? $request['start_date'] : '';
        $param['end_date'] = isset($request['end_date']) ? $request['end_date'] : '';
        $param['order_by'] = isset($request['order_by']) ? $request['order_by'] : 'id';
        $param['order_type'] = isset($request['order_type']) ? $request['order_type'] : 'desc';
        $param['is_status_group_by'] = 1;
        $param['is_web'] = 1;

        $param['export'] = isset($request['export']) ? $request['export'] : FALSE;
        // status list , with count of leads, percentage of total leads
        // search on created at date range

        $lead_response = Lead::getList($param);
        $lead_status_count = [];
        foreach ($lead_response as $lead){
            $lead_status_count[$lead['status_id']] = $lead['lead_count'];
        }


        $response['status'] = Status::getList($param);

        $status_count = 0;
        $status_total = 0;
        foreach ($response['status'] as $key => $status){
            $status['lead_count'] = (isset($lead_status_count[$status->id]))? $lead_status_count[$status->id] : 0;
            $status_count++;
            $status_total += $status['lead_count'];
        }

        if($status_total) {
            foreach ($response['status'] as $key => $status) {
                $response['status'][$key]['lead_percentage'] = round((($status['lead_count'] / $status_total) * 100),1);
            }
        }

        $response['agent'] = User::getTenantUserList($param);
        $response['type'] = Type::whereIn('tenant_id', [$request['company_id']])->whereNull('deleted_at')->orderBy('title')->get();
        $response['templates'] = Lead::getTemplate($request['company_id']);

        // , 'owner','first_name', 'last_name' // removing owner into first and last name
        //$response['columns'] = Config::get('constants.LEAD_DEFAULT_COLUMNS');
        $default_columns = [];
        $result_default_columns = TenantCustomField::getTenantDefaultFields($request['company_id']);

        foreach ($result_default_columns as $result_default_column){
            if($result_default_column['key'] == 'lead_name')
                $result_default_column['key'] = 'title';
            $default_columns[] = str_replace(Config::get('constants.SPECIAL_CHARACTERS.IGNORE'), Config::get('constants.SPECIAL_CHARACTERS.REPLACE'),$result_default_column['key']);
        }
        $response['columns'] = (count($default_columns)) ? $default_columns : Config::get('constants.LEAD_DEFAULT_COLUMNS');
        $response['orderable_columns'] = Config::get('constants.LEAD_DEFAULT_COLUMNS');
        $response['orderable_columns'][] = 'title';
        //$response['column_ids'] = ['title','lead_type','address', 'city', 'zip_code'];
        /*$custom_fields =TenantCustomField::getList($param);
        foreach($custom_fields as $field)
            $response['columns'][] = str_replace(["'"],['&#039;'],$field['key']);*/


        $this->__view = 'tenant.lead.lead_mgmt';

        $this->__is_paginate = false;
        $this->__collection = false;
        $this->__is_collection = false;

        return $this->__sendResponse('lead', $response, 200, 'Lead list retrieved successfully.');

    }

    public function listView(Request $request)
    {
        $param_rules['search'] = 'sometimes';

        $param['search'] = isset($request['search']) ? $request['search'] : '';
        $param['latitude'] = isset($request['latitude']) ? $request['latitude'] : '';
        $param['longitude'] = isset($request['longitude']) ? $request['longitude'] : '';
        $param['radius'] = isset($request['radius']) ? $request['radius'] : 500;
        $param['user_ids'] = isset($request['user_ids']) ? trim($request['user_ids']) : '';
        $param['status_ids'] = isset($request['status_ids']) ? trim($request['status_ids']) : '';
        $param['start_date'] = isset($request['start_date']) ? $request['start_date'] : '';
        $param['end_date'] = isset($request['end_date']) ? $request['end_date'] : '';
        $param['order_by'] = isset($request['order_by']) ? $request['order_by'] : 'id';
        $param['order_type'] = isset($request['order_type']) ? $request['order_type'] : 'desc';
        $param['lead_type_id'] = isset($request['lead_type_id']) ? $request['lead_type_id'] : '';

        $param['export'] = isset($request['export']) ? $request['export'] : FALSE;


        // status list , with count of leads, percentage of total leads

        // search on created at date range


        $this->__is_ajax = true;
        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        $param['user_id'] = $request['user_id'];
        $param['company_id'] = $request['company_id'];

//        $param['user_ids'] = empty($param['user_ids']) ? [] : explode(',',$param['user_ids']);
//        $param['status_ids'] = empty($param['status_ids']) ? [] : explode(',',$param['status_ids']);
        $columns = [];
        if ($param['export'] === 'true') {
            $request['lead_ids'] = isset($request['lead_ids']) ? empty($request['lead_ids']) ? '' : $request['lead_ids'] : '';
            $is_download = isset($request['is_download']) ? $request['is_download'] : 1;
            $param['lead_ids'] = explode(',', $request['lead_ids']);

            $response_data = Lead::getLeadWCustomField($param);
            $leadsCF = $response_data['data'];
            $leadsCFArr = $leadsCF; //->toArray();

            $leadIds = array_column($leadsCFArr, 'lead_id');
            if (isset($response_data['field_columns'])) {
                $columns = array_keys($response_data['field_columns']);
                $columns[] = 'first_name';
                $columns[] = 'last_name';
            }

            $request['ids'] = $leadIds;
            /*$first_key = [];
            foreach ($leadsCFArr as $item) {
                $first_key = $item;
                break;
            }*/

            foreach ($columns as $key => $item) {
                if($item == 'title' || $item == 'lead_titile' ) {
                    $columns[$key] = Config::get('constants.LEAD_TITLE_DISPLAY');
                    //$key = Config::get('constants.LEAD_TITLE_DISPLAY');
                }

                /*if (!in_array($key, $columns)) {
                    $columns[] = $key;
                }*/
                /*$leadsCFArr[$key][$item['key']] = $item['value'];
                unset($leadsCFArr[$key]['key']);
                unset($leadsCFArr[$key]['value']);*/
            }

            $ignore_column_names = explode(',', $request['ignore_column_names']);
            $ignoreCols = [
                'id',
                'formatted_address',
                'type_id',
                'status_id',
                'creator_id',
                'company_id',
                'assignee_id',
                'appointment_date',
                'appointment_result',
                'created_at',
                'updated_at',
                'deleted_at',
                'lead_id',
                'latitude',
                'longitude',
                'is_permanent',
                'code',
                'tenant_id',
                'tenant_custom_field_id',
                'key',
                'value',
                'owner',
                'first_name',
                'last_name',
                '',
            ];
            foreach ($ignore_column_names as $ignore_column_name)
                $ignoreCols[] = $ignore_column_name;

            $export = $this->export($columns, $leadsCFArr, 'Leads.csv', $ignoreCols, $is_download);
            if(!$is_download) {
                $this->__collection = false;
                $this->__is_paginate = false;
                return $this->__sendResponse('Lead', [], 200, 'Caching lead list export successfully.');
            }
            return $export;
        }
        else{
            $param['is_web'] = 1;
            if((strtolower($this->call_mode) == 'api') ) {
                $param['is_web'] = 0;
            }
            $response= \App\Http\Resources\Lead::collection(Lead::getList($param));

        }

        $this->__collection = false;
        return $this->__sendResponse('Lead', $response, 200, 'Lead list retrieved successfully.');

    }


    function custom_array_merge(&$array1, &$array2) {
        $result = Array();
        foreach ($array1 as $key_1 => &$value_1) {
            // if($value['name'])
            foreach ($array2 as $key_1 => $value_2) {
                if($value_1['id'] ==  $value_2['lead_id']) {
                    $result[] = array_merge($value_1,$value_2);
                }
            }

        }
        return $result;
    }

    public function export($columns = [], $data,$filename = "",$ignoreCols = [], $is_download = 1, $ignoe_column_map = 0)
    {
        $filename = (empty($filename))? 'temp2.csv' : $filename;
        $tmp_file_name = "{$this->__params['user_id']}_$filename";
        if(!isset($this->__params['page']))
            $this->__params['page'] = 1;

        $file_mode = 'a+';
        if($this->__params['page'] == 1)
            $file_mode = 'w';

        //print public_path($tmp_file_name);exit;

        $file = fopen(public_path($tmp_file_name), $file_mode);
        $columns = array_diff( $columns,$ignoreCols);
        if($this->__params['page'] == 1)
            fputcsv($file, $columns);
        foreach ($data as $dataRow) {
            if($ignoe_column_map){
                fputcsv($file, $dataRow);
                continue;
            }
            
            $csvRow = [];
            foreach ($columns as $column) {
                if(!in_array($column,$ignoreCols)){
                    if($column == Config::get('constants.LEAD_TITLE_DISPLAY')){
                        $column = 'title';
                    }
                    $csvRow[] = $dataRow[$column];
                }
            }
//            print_r($csvRow);
            fputcsv($file, $csvRow);
        }
        fclose($file);
        if(!$is_download)
            return false;

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=file.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
        return response()->download(public_path($tmp_file_name),$filename,$headers);

        //return Response::stream($callback, 200, $headers);

    }

    public function statusListView(Request $request)
    {
        $param_rules['search'] = 'sometimes';

        $param['search'] = isset($request['search']) ? $request['search'] : '';
        $param['latitude'] = isset($request['latitude']) ? $request['latitude'] : '';
        $param['longitude'] = isset($request['longitude']) ? $request['longitude'] : '';
        $param['radius'] = isset($request['radius']) ? $request['radius'] : 500;
        $param['user_ids'] = isset($request['user_ids']) ? trim($request['user_ids']) : '';
        $param['status_ids'] = isset($request['status_ids']) ? trim($request['status_ids']) : '';
        $param['start_date'] = isset($request['start_date']) ? $request['start_date'] : '';
        $param['end_date'] = isset($request['end_date']) ? $request['end_date'] : '';
        $param['order_by'] = isset($request['order_by']) ? $request['order_by'] : 'id';
        $param['order_type'] = isset($request['order_type']) ? $request['order_type'] : 'desc';
        $param['company_id'] = $request['company_id'];

        $param['export'] = isset($request['export']) ? $request['export'] : FALSE;
        $param['is_status_group_by'] = 1;
        // status list , with count of leads, percentage of total leads
        // search on created at date range

        $this->__is_ajax = true;
        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        $lead_response = Lead::getList($param);

        $param['user_id'] = $request['user_id'];
        $param['company_id'] = $request['company_id'];
        $param['user_ids'] = empty($param['user_ids']) ? [] : explode(',',$param['user_ids']);
        $param['status_ids'] = empty($param['status_ids']) ? [] : explode(',',$param['status_ids']);

        $response = \App\Http\Resources\Status::collection(Status::getList($param));

        $lead_status_count = [];
        foreach ($lead_response as $lead){
            $lead_status_count[$lead['status_id']] = $lead['lead_count'];
        }

        $status_count = 0;
        $status_total = 0;

        foreach ($response as $key => $status){
            if(!in_array($status['id'], $param['status_ids']) && !empty($param['status_ids'])) {
                $response[$key]['lead_count'] = 0;
                continue;
            }

            $status_count++;
            $status['lead_count'] = (isset($lead_status_count[$status['id']]))? $lead_status_count[$status['id']] : 0;
            $status_total += $status['lead_count'];
        }

        if($status_total) {
            foreach ($response as $key => $status) {

                if (!in_array($status['id'], $param['status_ids']) && !empty($param['status_ids'])) {
                    $response[$key]['lead_percentage'] = 0;
                    continue;
                }
                $response[$key]['lead_percentage'] = round((($status['lead_count'] / $status_total) * 100),1);
            }
        }
        $this->__is_paginate = false;
        $this->__collection = false;
        return $this->__sendResponse('Lead', $response, 200, 'Lead list retrieved successfully.');

    }

    public function addView(Request $request)
    {
        $param['user_id'] = $request['user_id'];
        $param['company_id'] = $request['company_id'];
        $param['is_paginate'] = false;

        $response['status'] = Status::whereIn('tenant_id', [$request['company_id']])->whereNull('deleted_at')->orderBy('order_by')->get();
        $response['type'] = Type::whereIn('tenant_id', [$request['company_id']])->orderBy('title')->whereNull('deleted_at')->get();
        $response['custom_fields'] =TenantCustomField::getList($param);

        $this->__view = 'tenant.lead.add_lead';
        $this->__is_paginate = false;
        $this->__is_collection = false;

        return $this->__sendResponse('Lead', $response, 200, 'Lead list retrieved successfully.');
    }

    public function userList(Request $request)
    {
        $param_rules['search'] = 'sometimes';

        $time_slot_map['today'] = 'INTERVAL 1 MONTH';
        $time_slot_map['yesterday'] = 'INTERVAL 1 MONTH';
        $time_slot_map['week'] = 'INTERVAL 1 MONTH';
        $time_slot_map['last_week'] = 'INTERVAL 1 MONTH';
        $time_slot_map['month'] = 'INTERVAL 1 MONTH';
        $time_slot_map['last_month'] = 'INTERVAL 1 MONTH';
        $time_slot_map['year'] = 'INTERVAL 1 YEAR';
        $time_slot_map['last_year'] = 'INTERVAL 1 YEAR';

        $param['search'] = isset($request['search']) ? $request['search'] : '';
        $param['latitude'] = isset($request['latitude']) ? $request['latitude'] : '';
        $param['longitude'] = isset($request['longitude']) ? $request['longitude'] : '';
        $param['lead_type_id'] = isset($request['lead_type_id']) ? $request['lead_type_id'] : '';
        $param['user_ids'] = isset($request['target_user_id']) ? trim($request['target_user_id']) : '';
        $param['status_ids'] = isset($request['status_id']) ? trim($request['status_id']) : '';
        $param['radius'] = isset($request['radius']) ? $request['radius'] : 500;
        $param['time_slot'] = isset($request['time_slot']) ? (isset($time_slot_map[$request['time_slot']])) ? $time_slot_map[$request['time_slot']] : '' : '';
        $param['slot'] = isset($request['time_slot']) ? (isset($time_slot_map[$request['time_slot']])) ? $request['time_slot'] : '' : '';


        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        $param['user_id'] = $request['user_id'];
        $param['company_id'] = $request['company_id'];

        $response = Lead::getUserList($param);

        return $this->__sendResponse('Lead', $response, 200, 'Assigned lead list retrieved successfully.');

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
        $param_rules['user_id'] = 'required';
        $param_rules['title'] = 'required';
        $param_rules['address'] = 'required';
        $param_rules['city'] = 'required'; //'|regex:/^[a-zA-Z]+$/';
        $param_rules['county'] = 'required';
        $param_rules['state'] = 'required';
        $param_rules['zip_code'] = 'required';
        $param_rules['type_id'] = 'required';
        $param_rules['status_id'] = 'required';
        $param_rules['image_url'] = 'nullable';
        $param_rules['image_url.*'] = 'image|mimes:jpeg,png,jpg,gif,svg|max:2048';

        //$param_rules['latitude']  = 'required';
        //$param_rules['longitude']  = 'required';
        $this->__is_ajax = true;
        $response = $this->__validateRequestParams($request->all(), $param_rules, ['title.required' => 'The ' . Config::get('constants.LEAD_TITLE_DISPLAY') . ' field is required']);

        if ($this->__is_error == true)
            return $response;

        $system_image_url = [];
        if ($request->hasFile('image_url')) {
            foreach ($request->image_url as $image_url) {
                // $obj is model
                $system_image_url[] = $this->__moveUploadFile(
                    $image_url,
                    md5($request->title . time() . rand(10, 99)),
                    Config::get('constants.MEDIA_IMAGE_PATH')
                );
            }
        }
        // .',' . $request->zip_code //remove from the end
        $lat_long_response = $this->getLatLongFromAddress($request->address . ',' . $request->city);

        $obj = new Lead();
        $obj->creator_id = $request->user_id;
        $obj->company_id = $request->company_id;
        $obj->title = $request->title;
        $obj->foreclosure_date = (isset($request->foreclosure_date))? $request->foreclosure_date : '';
        $obj->admin_notes = (isset($request->admin_notes))? $request->admin_notes : '';
        $obj->owner = $request->first_name . ' ' . $request->last_name;
        $obj->address = $request->address;

        $obj->type_id = $request->type_id;
        $obj->status_id = $request->status_id;
        $obj->city = $request->city;
        $obj->state = $request->state;
        $obj->county = $request->county;
        $obj->zip_code = $request->zip_code;

        $obj->latitude = $lat_long_response['lat'];
        $obj->longitude = $lat_long_response['long'];
        $obj->formatted_address = $lat_long_response['formatted_address'];
        //$obj->city = $lat_long_response['city'];
        //$obj->zip_code = $lat_long_response['zip_code'];

        $obj->save();
        if (count($system_image_url))
            Media::createBulk($obj->id, 'lead', 'image', $system_image_url);

        // insert lead queries
        LeadQuery::insertBulk($obj->id, $request->company_id);
        // dump status on tenant creation, get first status id of tenant and pass to lead count
        //$status_id = Status::getFirstTenantStatus($request->company_id);
        $status_id = $request->status_id;
        Status::incrementLeadCount($status_id);

        $obj_lead_history = LeadHistory::create([
            'lead_id' => $obj->id,
            'title' => 'Lead created',
            'assign_id' => $request['user_id'],
            'status_id' => 0
        ]);

        $obj_lead_history = LeadHistory::create([
            'lead_id' => $obj->id,
            'title' => '',
            'lead_status_title' => 'Lead status initialized.',
            'assign_id' => $request['user_id'],
            'status_id' => $status_id
        ]);

        // insert lead custom fields
        $ignore_fields = ['_token', 'first_name', 'last_name', 'user_id', 'company_id', 'title', 'foreclosure_date','admin_notes','address', 'type_id', 'status_id', 'image_url'];
        $custom_field = $request->custom_field;
        $custom_field['company_id'] = $request['company_id'];
        LeadCustomField::insert($obj->id, $ignore_fields, $custom_field);

        $this->__is_paginate = false;
        $this->__is_collection = false;
        return $this->__sendResponse('Lead', Lead::getById($obj->id), 200, 'Your lead has been added successfully.');
    }

    public function wizardView(Request $request){

        $response['template'] =Lead::getTemplate($request['company_id']);
        $response['lead_types'] = Type::whereIn('tenant_id', [$request['company_id']])->orderBy('title')->whereNull('deleted_at')->get();
        $response['lead_status'] = Status::whereIn('tenant_id', [$request['company_id']])->orderBy('order_by')->whereNull('deleted_at')->get();

        $response['fields'] = TenantCustomField::getList(['company_id' => $request['company_id']]);


        $this->__view = 'tenant.lead.wizard';
        $this->__is_paginate = false;
        $this->__collection = false;
        return $this->__sendResponse('Wizard', $response, 200,'Your leads file has been added in process.');

    }

    public function uploadLeads(Request $request)
    {
        $param_rules['user_id'] = 'required';
        //$param_rules['file']  = 'required|mimes:xls,xlsx,csv|max:1024'4096;
        $param_rules['file']  = 'required|min:0.15,max:5120';
        $param_rules['extension']  = 'required|in:xls,xlsx,csv';

        $this->__is_ajax = true;
        $request['extension']  = strtolower($request->file->getClientOriginalExtension());
        $response = $this->__validateRequestParams($request->all(), $param_rules, [
            'file.min' => 'The file must be at least 150 bytes.'
        ]);

        if($this->__is_error == true)
            return $response;

        $system_image_url = [];
        if ($request->hasFile('file')) {
            // $obj is model
            $system_file_url = $this->__moveUploadFile(
                $request->file,
                md5($request['company_id']),
                Config::get('constants.MEDIA_FILE_PATH'),
                false
            );
        }

        $param['tenant_id'] = $request['company_id'];
        $param['media_url'] = $system_file_url;
        Lead::saveTempFile($param);


        $this->__is_paginate = false;
        $this->__collection = false;
        return $this->__sendResponse('Lead', [], 200,'Your leads file has been added in process.');
    }

    public function uploadMedia(Request $request, $lead_id)
    {
        $param_rules['user_id'] = 'required';
        $param_rules['lead_id']  = 'required|exists:lead,id';
        $param_rules['image_url']  = 'required';
        $param_rules['image_url.*'] = 'image|mimes:jpeg,png,jpg,gif,svg|max:6144';

        $request['lead_id'] = $lead_id;
        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        $system_image_url = [];
        $system_image_url = [];
        if ($request->hasFile('image_url')) {
            foreach ($request->image_url as $image_url) {
                // $obj is model
                $system_image_url[] = $this->__moveUploadFile(
                    $image_url,
                    md5($lead_id . time().rand(10,99)),
                    Config::get('constants.MEDIA_IMAGE_PATH')
                );
            }
        }

        Media::deleteBySourceId($lead_id);
        Media::createBulk($lead_id, 'lead', 'image', $system_image_url);


        $this->__is_paginate = false;
        $this->__is_collection = false;
        return $this->__sendResponse('Lead', Lead::getById($lead_id), 200,'Your lead has been updated successfully');
    }

    public function updateTemplateFieldIndex(Request $request)
    {
        $param_rules['template_id'] = 'required';
        $param_rules['field_id'] = 'required|exists:tenant_custom_field,NULL,deleted_at';
        $param_rules['field_id'] = 'required';
        $param_rules['indexs'] = 'required';

        $this->__is_ajax = true;
        $response = $this->__validateRequestParams($request->all(), $param_rules);


        if ($this->__is_error == true)
            return $response;

        TemplateFields::where('field', $request['field_id'])
            ->where('template_id', $request['template_id'])
            ->Update(['index' => $request['indexs']]);

        $this->__is_paginate = false;
        $this->__collection = false;
        return $this->__sendResponse('Template', [], 200, 'Your lead template field index has been updated successfully.');
    }

    public function updateTemplateFieldIndexClear(Request $request)
    {
        $param_rules['template_id'] = 'required';

        $this->__is_ajax = true;
        $response = $this->__validateRequestParams($request->all(), $param_rules);


        if ($this->__is_error == true)
            return $response;

        TemplateFields::where('template_id', $request['template_id'])
            ->Update(['index' => '', 'index_map' => '']);

        $this->__is_paginate = false;
        $this->__collection = false;
        return $this->__sendResponse('Template', [], 200, 'Your lead template field index has been updated successfully.');
    }

    public function addTemplate(Request $request)
    {
        $param_rules['user_id'] = 'required';
        $param_rules['title'] = 'required|string|max:100|regex:/(?!^\d+$)^.+$/|unique:tenant_template,NULL,deleted_at,id,tenant_id,'.$request['company_id'];

        $this->__is_ajax = true;
        $response = $this->__validateRequestParams($request->all(), $param_rules);


        if ($this->__is_error == true)
            return $response;

        $param['tenant_id'] = $request['company_id'];
        $param['title'] = $request['title'];
        $param['description'] = $request['title'];
        $template_id = Lead::saveTemplate($param);

        $template_fields = Config::get('constants.LEAD_DEFAULT_COLUMNS');
        //TenantCustomField::insertTenantDefaultFields($request['company_id'], $template_fields);

        $count = 0;
        $data = [];
        foreach ($template_fields as $template_field) {
            $data[$count]['index'] = 0;
            $data[$count]['index_map'] = str_replace('_', ' ', $template_field);
            $data[$count]['field'] = $template_field;
            $data[$count]['order_by'] = $count + 1;

            $count++;
        }

        Lead::saveTemplateField($template_id, $data);


        $this->__is_paginate = false;
        $this->__collection = false;
        return $this->__sendResponse('Template', [], 200, 'Your lead template has been created successfully.');
    }

    public function wizardTemplate(Request $request)
    {
        $param_rules['user_id'] = 'required';

        $this->__is_ajax = true;

        $response = $this->__validateRequestParams($request->all(), $param_rules);


        if($this->__is_error == true)
            return $response;

        if(empty($request['template_id']) && empty( $request['template'])){
            $errors['template'] = 'Template is required';
            return $this->__sendError('Validation Error.', $errors);
        }
        //print_r($request->all());exit;
        $response = [];
        $response['template_id'] = 0;
        if(isset( $request['template_id']) && !empty($request['template_id']))
            $response['template_id'] = $request['template_id'];

        if(isset($request['template']) && !empty(trim($request['template']))) {
            $param['tenant_id'] = $request['company_id'];
            $param['title'] = $request['template'];
            $param['description'] = $request['template'];
            $response['template_id'] = Lead::saveTemplate($param);
        }

        $response['fields'] = TenantCustomField::getList(['company_id' => $request['company_id'], 'template_id' => $response['template_id']]);

        $temp_file =Lead::getTempfile($request['company_id']);
        $file_leads = $response['file_header'] = $this->__getFileContent(storage_path(Config::get('constants.MEDIA_FILE_PATH').$temp_file->media_url), 1);

        $response['template_fields'] = Lead::getTemplateById($response['template_id']);
        $response['custom_fields'] = Lead::getTemplateById($response['template_id'], 'INNER');
        foreach($response['template_fields'] as $key => $template_fields){
            if(empty($template_fields->index)) {
                $case_1 = strtolower($template_fields->index_map);
                $case_2 = ucwords($template_fields->index_map);
                $response['template_fields'][$key]->index = false;
                if(!empty($file_leads))
                    $response['template_fields'][$key]->index = ($this->__array_search([$case_1, $case_2], $file_leads));
            }
        }
        foreach($response['custom_fields'] as $key => $template_fields){
            if(empty($template_fields->index)) {
                $template_fields->index = false;
                if(!empty($file_leads))
                    $response['custom_fields'][$key]->index = (array_search($template_fields->index_map, $file_leads));
            }
        }

        if(!count($response['template_fields'])){
            $response['template_fields'][0]['template_id'] = $response['template_id'];
            $response['template_fields'][0]['field'] = 'lead_name';
            $response['template_fields'][0]['index'] = $this->__array_search (['lead_name','name','title'], $response['file_header']);

            $response['template_fields'][1]['template_id'] = $response['template_id'];
            $response['template_fields'][1]['field'] = 'address';
            $response['template_fields'][1]['index'] = $this->__array_search (['Address','Street Address'], $response['file_header']);

            $response['template_fields'][2]['template_id'] = $response['template_id'];
            $response['template_fields'][2]['field'] = 'city';
            $response['template_fields'][2]['index'] = $this->__array_search (['City'], $response['file_header']);

            $response['template_fields'][3]['template_id'] = $response['template_id'];
            $response['template_fields'][3]['field'] = 'zip_code';
            $response['template_fields'][3]['index'] = $this->__array_search (['Zip Code', 'zip_code', 'zip'], $response['file_header']);

            $response['template_fields'][4]['template_id'] = $response['template_id'];
            $response['template_fields'][4]['field'] = 'lead_type';
            $response['template_fields'][4]['index'] = $this->__array_search (['Lead Type', 'lead_type'], $response['file_header']);

            $response['template_fields'][5]['template_id'] = $response['template_id'];
            $response['template_fields'][5]['field'] = 'first_name';
            $response['template_fields'][5]['index'] = $this->__array_search (['Mortgagor First Name','owner','First Name'], $response['file_header']);

            $response['template_fields'][6]['template_id'] = $response['template_id'];
            $response['template_fields'][6]['field'] = 'last_name';
            $response['template_fields'][6]['index'] = $this->__array_search (['Mortgagor Last Name','owner','Last Name'], $response['file_header']);

            $response['template_fields'][7]['template_id'] = $response['template_id'];
            $response['template_fields'][7]['field'] = 'county';
            $response['template_fields'][7]['index'] = $this->__array_search (['County','county'], $response['file_header']);

            $response['template_fields'][8]['template_id'] = $response['template_id'];
            $response['template_fields'][8]['field'] = 'state';
            $response['template_fields'][8]['index'] = $this->__array_search (['state'], $response['file_header']);


            $response['template_fields'][9]['template_id'] = $response['template_id'];
            $response['template_fields'][9]['field'] = 'lead_status';
            $response['template_fields'][9]['index'] = $this->__array_search (['Lead Status', 'lead_status', 'status'], $response['file_header']);

        }

        $params['is_all'] = (isset($request['is_all']))? $request['is_all'] : 2;
        $params['company_id'] =$request['company_id'];
        //$response['fix_template_fields'] = Lead::getFieldsTemplateById($request['template_id'], $params);
        //$response['fix_template_fields'] = Lead::getTemplateById($request['template_id'], 'INNER');

        $this->__is_paginate = false;
        $this->__collection = false;
        //print_r($response);exit;
        return $this->__sendResponse('Template', $response, 200,'Your lead template has been created successfully.');
    }

    public function wizardFields(Request $request)
    {
        $param_rules['user_id']     = 'required';
        $param_rules['template_id'] = 'required';
        $param_rules['lead_name']   = 'required';
        $param_rules['lead_type_id']   = 'required';
        $param_rules['lead_status_id']   = 'required';
        $param_rules['address']     = 'required';
        $param_rules['city']        = 'required';
        $param_rules['zip_code']    = 'required';

        $this->__is_ajax = true;

        $response = $this->__validateRequestParams($request->all(), $param_rules,
            ['lead_name.required' => 'The '.Config::get('constants.LEAD_TITLE_DISPLAY').' field is required',
            'lead_type_id.required' => 'The Lead Type field is required',
            'lead_status_id.required' => 'The Lead Status field is required'
            ]
        );

        if($this->__is_error == true)
            return $response;

        $status_id = $request['lead_status_id']; //Status::getFirstTenantStatus($request->company_id);
        if(empty($status_id)){
            $errors['code'] = 'Default status is not defined.';
            return $this->__sendError('Validation Error.', $errors);
        }
        $lead_statuses = [];
        $lead_status_result = []; //Status::whereIn('tenant_id', [$request['company_id']])->whereNull('deleted_at')->get();
        foreach($lead_status_result as $lead_status) {
            $lead_statuses[strtolower($lead_status->title)] = $lead_status->id;
        }
        $lead_types = [];
        //$lead_type_result = Type::whereIn('tenant_id', [$request['company_id']])->where('id',$request['template_id'])->whereNull('deleted_at')->get();
        //$lead_type_result = Type::whereIn('tenant_id', [$request['company_id']])->whereNull('deleted_at')->get();
        $users = User::getTenantUserList(['company_id' => $request['company_id']]);
        $user_codes = [];
        foreach ($users as $user)
            $user_codes['IN-'. str_pad($user->company_id, 3, '0', STR_PAD_LEFT) .'-' .str_pad($user->id, 4, '0', STR_PAD_LEFT)] = $user->id;

        $lead_type_id = $request['lead_type_id'];
        $count = 0;
        /*foreach($lead_type_result as $lead_type) {
            if($count == 0)
                $lead_type_id = $lead_type->id;
            $lead_types[strtolower($lead_type->title)] = $lead_type->id;
            $count++;
        }*/
        $temp_file =Lead::getTempfile($request['company_id']);
        $file_leads = $this->__getFileContent(storage_path(Config::get('constants.MEDIA_FILE_PATH').$temp_file->media_url));

        // get file name
        // insert bulk lead
        // insert bulk custom field
        // insert template fields
        $temp_fields = [];
        $file_header_index[] = $file_leads[0];
        $user_code_index = ($this->__array_search(['user_code'],$file_leads[0]));
        for($i = 1; $i<=count($file_leads); $i++) {
            $user_code = $file_leads[$i][$user_code_index];
            if(!isset($file_leads[$i][$request['address']]) || $file_leads[$i][$request['address']] == '1970-01-01'  || $file_leads[$i][$request['address']] == '1970-01-01 00:00:00' || empty($file_leads[$i][$request['address']]) || !isset($file_leads[$i]))
                continue;
            //print_r($file_leads[$i]);exit;
            $address = $file_leads[$i][$request['address']]. ',' . $file_leads[$i][$request['city']] . ',' . $file_leads[$i][$request['zip_code']];
            $lat_long_response = $this->getLatLongFromAddress($address);

            $obj = new \stdClass(); //Lead();
            $obj->creator_id = $request->user_id;
            $obj->company_id = $request->company_id;
            $obj->assignee_id = (isset($user_codes[$file_leads[$i][$user_code_index]]))? $user_codes[$file_leads[$i][$user_code_index]] : '';

            $lead_title = '';
            foreach ($request['lead_name'] as $title_index)
                $lead_title .= $file_leads[$i][$title_index] .' ';

            $lead_owner = '';
            if(!empty($request['owner'])) {
                foreach ($request['owner'] as $title_index)
                    $lead_owner .= $file_leads[$i][$title_index] . ' ';
            }
            $owner_name = '';
            if(!empty($request['first_name'])) {
                $owner_name = $file_leads[$i][$request['first_name']];
            }

            if(!empty($request['last_name'])) {
                $owner_name = (!empty($owner_name))? $owner_name . ' '.$file_leads[$i][$request['last_name']] : $file_leads[$i][$request['last_name']];
            }

            $county = '';
            if(!empty($request['county'])) {
                $county = $file_leads[$i][$request['county']];
            }

            $state = '';
            if(!empty($request['state'])) {
                $state = $file_leads[$i][$request['state']];
            }

            $foreclosure_date = '';
            if(!empty($request['foreclosure_date'])) {
                $foreclosure_date = $file_leads[$i][$request['foreclosure_date']];
            }

            $admin_notes = '';
            if(!empty($request['admin_notes'])) {
                $admin_notes = $file_leads[$i][$request['admin_notes']];
            }

            $lead_owner = trim($owner_name);
            $obj->title = trim($lead_title);
            $obj->owner = trim($lead_owner);
            $obj->county = $county;
            $obj->state = $state;
            $obj->foreclosure_date = $foreclosure_date;
            $obj->admin_notes = $admin_notes;
            $obj->address = $file_leads[$i][$request['address']];

            $file_leads[$i][$request['lead_type']] = strtolower($file_leads[$i][$request['lead_type']]);
            /*if(!isset($lead_types[$file_leads[$i][$request['lead_type']]])) {
                //continue;
                //$obj->type_id = $lead_types[$file_leads[$i][$request['lead_type']]] : $lead_type_id;

                $lead_title_length = strlen($file_leads[$i][$request['lead_type']]);
                $lead_title_max = rand(1,$lead_title_length -1);
                $code = $file_leads[$i][$request['lead_type']][0];
                $code = $code . $file_leads[$i][$request['lead_type']][$lead_title_max];

                $obj_type = new Type();
                $obj_type->title      = $file_leads[$i][$request['lead_type']];
                $obj_type->tenant_id = $request['company_id'];
                $obj_type->code = $code;
                $obj_type->save();

                $lead_types[strtolower($file_leads[$i][$request['lead_type']])] = $obj_type->id;
            }*/
            //$obj->type_id = $lead_types[$file_leads[$i][$request['lead_type']]];
            $obj->type_id = $lead_type_id;
            //$file_leads[$i][$request['lead_status']] = strtolower($file_leads[$i][$request['lead_status']]);
            //$obj->status_id = (isset($lead_statuses[$file_leads[$i][$request['lead_status']]]))? $lead_statuses[$file_leads[$i][$request['lead_status']]] : $status_id;
            $obj->status_id = $status_id;

            $obj->latitude = $lat_long_response['lat'];
            $obj->longitude = $lat_long_response['long'];
            $obj->formatted_address = $lat_long_response['formatted_address'];
            $obj->city = (!empty($file_leads[$i][$request['city']]))? $file_leads[$i][$request['city']]: $lat_long_response['city'];
            $obj->zip_code = (!empty($file_leads[$i][$request['zip_code']]))? $file_leads[$i][$request['zip_code']]: $lat_long_response['zip_code'];
            $obj->id = Lead::saveLead($obj);
            //$obj->save();
            $temp_fields['lead_name']['index'] = implode(',', $request['lead_name']);
            $temp_fields['lead_name']['field'] = 'lead_name';
            $tmp_lead_name_index = [];
            foreach ($request['lead_name'] as $lead_name_index) {
                $tmp_lead_name_index[] = $file_leads[0][$lead_name_index];
            }

            $temp_fields['lead_name']['index_map'] = implode(',', $tmp_lead_name_index);

            /*if(!empty($request['owner'])) {
                $temp_fields['owner']['index'] = implode(',', $request['owner']);
                $temp_fields['owner']['field'] = 'owner';
                $tmp_lead_name_index = [];
                foreach ($request['owner'] as $lead_name_index) {
                    $tmp_lead_name_index[] = $file_leads[0][$lead_name_index];
                }
                $temp_fields['owner']['index_map'] = implode(',', $tmp_lead_name_index);
            }*/
            if(!empty($request['first_name'])) {
                $temp_fields['first_name']['index'] = $request['first_name'];
                $temp_fields['first_name']['field'] = 'first_name';
                $temp_fields['first_name']['index_map'] = $file_leads[0][$request['first_name']];
            }

            if(!empty($request['last_name'])) {
                $temp_fields['last_name']['index'] = $request['last_name'];
                $temp_fields['last_name']['field'] = 'last_name';
                $temp_fields['last_name']['index_map'] = $file_leads[0][$request['last_name']];
            }

            if(!empty($request['county'])) {
                $temp_fields['county']['index'] = $request['county'];
                $temp_fields['county']['field'] = 'county';
                $temp_fields['county']['index_map'] = $file_leads[0][$request['county']];
            }

            $temp_fields['address']['index'] = $request['address'];
            $temp_fields['address']['field'] = 'address';
            $temp_fields['address']['index_map'] = $file_leads[0][$request['address']];

            $temp_fields['foreclosure_date']['index'] = $request['foreclosure_date'];
            $temp_fields['foreclosure_date']['field'] = 'foreclosure_date';
            $temp_fields['foreclosure_date']['index_map'] = $file_leads[0][$request['foreclosure_date']];


            $temp_fields['admin_notes']['index'] = $request['admin_notes'];
            $temp_fields['admin_notes']['field'] = 'admin_notes';
            $temp_fields['admin_notes']['index_map'] = $file_leads[0][$request['admin_notes']];
            

            $temp_fields['county']['index'] = $request['county'];
            $temp_fields['county']['field'] = 'county';
            $temp_fields['county']['index_map'] = $file_leads[0][$request['county']];

            $temp_fields['state']['index'] = $request['state'];
            $temp_fields['state']['field'] = 'state';
            $temp_fields['state']['index_map'] = $file_leads[0][$request['state']];

            $temp_fields['lead_type']['index'] = $request['lead_type'];
            $temp_fields['lead_type']['field'] = 'lead_type';
            $temp_fields['lead_type']['index_map'] = $file_leads[0][$request['lead_type']];

            $temp_fields['lead_status']['index'] = $request['lead_status'];
            $temp_fields['lead_status']['field'] = 'lead_status';
            $temp_fields['lead_status']['index_map'] = $file_leads[0][$request['lead_status']];

            $temp_fields['city']['index'] = $request['city'];
            $temp_fields['city']['field'] = 'city';
            $temp_fields['city']['index_map'] = $file_leads[0][$request['city']];

            $temp_fields['zip_code']['index'] = $request['zip_code'];
            $temp_fields['zip_code']['field'] = 'zip_code';
            $temp_fields['zip_code']['index_map'] = $file_leads[0][$request['zip_code']];

            // insert lead queries
            LeadQuery::insertBulk($obj->id, $request->company_id);

            // dump status on tenant creation, get first status id of tenant and pass to lead count
            Status::incrementLeadCount($status_id);
            $obj_lead_history = LeadHistory::create([
                'lead_id' => $obj->id,
                'title' => 'Lead created',
                'assign_id' => $request['user_id'],
                'status_id' => 0
            ]);

            $assign_id = (isset($user_codes[$file_leads[$i][$user_code_index]]))? $user_codes[$file_leads[$i][$user_code_index]] : $request['user_id'];
            $obj_lead_history = LeadHistory::create([
                'lead_id' => $obj->id,
                'title' => '',
                'lead_status_title' => 'Lead status initialized.',
                'assign_id' => $assign_id,
                'status_id' => $status_id
            ]);

            // insert lead custom fields
            $custom_fields = [];
            $ignore_fields = ['company_id'];
            $custom_fields['company_id'] = $request->company_id;
            if(isset($request['custom_field'])) {
                foreach ($request['custom_field'] as $key => $value) {
                    //$multi_key = [];
                    $multi_value = [];
                    $multi_index_map = [];
                    foreach ($value as $each_value){
                        if (!empty($file_leads[$i][$each_value]) &&
                            (strtolower($file_leads[$i][$each_value]) != 'n/a' && strtolower($file_leads[$i][$each_value]) && '1970-01-01 00:00:00' && strtolower($file_leads[$i][$each_value]) != '1970-01-01')){
                                $multi_value[] = $file_leads[$i][$each_value];
                                $multi_index_map[] = $file_leads[0][$each_value];
                        }
                    }
                    $multi_value_c = implode(',', $multi_value);
                    $value_c = implode(',', $value);
                    if (!empty($multi_value_c) && strtolower($value_c) != 'n/a') {
                        $custom_fields[$key] =  $multi_value_c;//$file_leads[$i][$value];
                        $temp_fields[$key]['index'] = $value_c;
                        $temp_fields[$key]['field'] = $key;
                        $temp_fields[$key]['index_map'] = implode(',', $multi_index_map); //$file_leads[0][$value];
                        //$temp_field_index_map[$key] = $file_leads[0][$value];
                    }
                }
                LeadCustomField::insert($obj->id, $ignore_fields, $custom_fields);
            }
        }

        if(count($temp_fields)){
            Lead::saveTemplateField($request['template_id'], $temp_fields);
        }
        $this->__is_paginate = false;
        $this->__collection = false;
        return $this->__sendResponse('Lead', [], 200,'Your lead bulk has been added successfully.');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $param_rules['id'] = 'required|exists:lead,id';
        $this->__is_ajax = true;
        $response = $this->__validateRequestParams(['id' => $id], $param_rules);

        if($this->__is_error == true)
            return $response;

        $this->__is_paginate = false;
        $this->__is_collection = false;
        return $this->__sendResponse('Lead', Lead::getById($id), 200,'Lead has been retrieved successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $param_rules['id'] = 'required|exists:lead,id';
        $param['user_id'] = $request['user_id'];
        $param['company_id'] = $request['company_id'];
        $param['is_paginate'] = false;

        $response = $this->__validateRequestParams(['id' => $id], $param_rules);

        if($this->__is_error == true)
            return $response;

        $response['status'] = Status::getList($param);
        $response['agent'] = User::getTenantUserList($param);
        $response['type'] = Type::whereIn('tenant_id', [$request['company_id']])->whereNull('deleted_at')->get();
        $response['lead'] = Lead::getById($id);

        $this->__view = 'tenant.lead.lead_detail';

        $this->__is_paginate = false;
        $this->__collection = false;
        return $this->__sendResponse('Lead', $response, 200,'Lead has been retrieved successfully.');

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
        $param_rules['id'] = 'required|exists:lead,id';
        $param_rules['target_id'] = 'required|exists:user,id';
        $param_rules['status_id'] = 'required|exists:status,id';
        $param_rules['is_expired'] = 'required';
        $param_rules['title'] = 'required';
        $param_rules['type_id'] = 'required';
        $param_rules['address'] = 'required';
        $this->__is_ajax = true;

        $request['target_id'] = (!empty($request['target_id'])) ? $request['target_id'] : $request['user_id'];
        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        $address = explode(',', $request->address);
        $address = (isset($address[0])) ? $address[0] : $address;

        $owner = (isset($request['owner']))? $request['owner'] : '';
        $owner = (isset($request['first_name']))? $request['first_name'] : $owner;
        $owner = (isset($request['last_name']))? trim($owner .' '. $request['last_name']) : $owner;

        $lead_old_data = [];
        $obj_lead = Lead::find($id);
        $lead_old_data = $obj_lead->toArray();

        $obj_lead->address = $address;
        $obj_lead->title = $request['title'];
        $obj_lead->foreclosure_date = (isset($request['foreclosure_date']))? $request['foreclosure_date'] : 'test';
        $obj_lead->admin_notes = (isset($request['admin_notes']))? $request['admin_notes'] : 'test';        
        $obj_lead->owner = $owner;
        $obj_lead->assignee_id = ($request['target_id'] == $request['user_id'])? 0 : $request['target_id'];
        $obj_lead->status_id = $request['status_id'];
        $obj_lead->type_id = $request['type_id'];
        $obj_lead->is_expired = (empty($request['is_expired']))? 0 : 1;

        if($address != $lead_old_data['address']){
            $lat_long_response = $this->getLatLongFromAddress($request->address);

            $obj_lead->latitude = $lat_long_response['lat'];
            $obj_lead->longitude = $lat_long_response['long'];
            $obj_lead->formatted_address = $lat_long_response['formatted_address'];
            $obj_lead->city = $lat_long_response['city'];
            $obj_lead->zip_code = $lat_long_response['zip_code'];

        }

        $obj_lead->save();

        $obj_history = new History();

        $obj_history->history_trigger_prefx = 'lead';
        $obj_history->history_trigger_map = ['address', 'title', 'assignee', 'type', 'expired', 'status'];
        $obj_history->initiate($lead_old_data, $request->all());

        if($lead_old_data['status_id'] != $request['status_id']) {
            Status::incrementLeadCount($obj_lead->status_id);
            Status::decrementLeadCount($lead_old_data['status_id']);

            $params['user_id'] = $request['target_id'];
            $params['status_id'] = $request['status_id'];
            $params['lead_id'] = $id;
            UserLeadKnocks::insertLeadKnocks($params);
        }

        $this->__is_paginate = false;
        $this->__is_collection = false;
        return $this->__sendResponse('Lead', Lead::getById($id), 200,'Lead has been retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function bulkUpdate(Request $request)
    {
        $param_rules['assign_id'] = 'nullable|exists:user,id';
        $param_rules['status_id'] = 'nullable|exists:status,id';
        $param_rules['is_expired'] = 'nullable';
        $param_rules['type_id'] = 'nullable|exists:type,id';
        $param_rules['action'] = 'required|in:delete,update';
        $param_rules['lead_ids'] = 'required';

        $this->__is_ajax = true;
        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        $params['assign_id'] = (isset($request['assign_id']))? $request['assign_id'] : '';
        $params['status_id'] =  (isset($request['status_id']))? $request['status_id'] : '';
        $params['type_id'] =  (isset($request['type_id']))? $request['type_id'] : '';
        $params['is_expired'] =  (isset($request['is_expired']))? $request['is_expired'] : '';
        $params['action'] = $request['action'];
        $params['lead_ids'] = $request['lead_ids'];
        $params['company_id'] = $request['company_id'];
        $params['target_user_id'] = (!empty($request['assign_id']))? $request['assign_id'] : $request['user_id'];
        $params['user_id'] = $request['user_id'];

        Lead::bulkUpdate($params);

        $this->__is_paginate = false;
        $this->__collection = false;
        return $this->__sendResponse('Lead', [], 200,'Lead has been updated successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateQuery(Request $request, $id)
    {
        $request['id'] = $id;
        $param_rules['id'] = 'required|exists:lead,id';
        $param_rules['status_id'] = 'required|exists:status,id';
        $param_rules['query'] = 'required';

        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;
        
        $obj_lead = Lead::find($id);
        $status_id = $obj_lead->status_id;
       // $obj_lead->assignee_id = $request['user_id'];
        $obj_lead->status_id = $request['status_id'];
        if(isset($request['is_verified'])) {
            $request['is_verified'] = (empty($request['is_verified']))? 0 : 1;
            $obj_lead->is_verified = $request['is_verified'];
        }
        $obj_lead->save();


        
      //  if($status_id != $request['status_id']) {
        if($request['is_status_update'] == 1) {
            $obj_lead_history = LeadHistory::create([
                'lead_id' => $id,
                'title' => '',
                'assign_id' => $request['user_id'],
                'status_id' => $request['status_id'] //$obj_lead->status_id
            ]);

            Status::incrementLeadCount($obj_lead->status_id);
            Status::decrementLeadCount($status_id);
            $request['lead_id'] = $id;
            UserLeadKnocks::insertLeadKnocks($request->all());
        }
       // }
        $request['user_id'] = $obj_lead->assignee_id;
        LeadQuery::updateQuery($id, json_decode($request['query'],true));

        $this->__is_paginate = false;
        $this->__is_collection = false;
        return $this->__sendResponse('Lead', Lead::getById($id), 200,'Lead has been retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userAssignLead(Request $request, $lead_id)
    {
        $param_rules['id'] = 'required|exists:lead,id';
        $param_rules['target_id'] = 'required|exists:user,id';

        $request['id'] = $lead_id;
        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        $obj_lead = Lead::find($lead_id);
        $lead_old_data = $obj_lead->toArray();
        $obj_lead->assignee_id = isset($request['target_id'])? $request['target_id'] : $request['user_id'];
        $obj_lead->save();

        /*$obj_lead_history = LeadHistory::create([
                'lead_id' => $lead_id,
                'title' => '',
                'assign_id' => $request['user_id'],
                'status_id' => $obj_lead->status_id
            ]);
        */

        $obj_history = new History();

        $obj_history->history_trigger_prefx = 'lead';
        $obj_history->history_trigger_map = ['assignee'];
        $request['assignee_id'] = $obj_lead->assignee_id;

        $obj_history->initiate($lead_old_data, $request->all());

        $this->__is_paginate = false;
        $this->__is_collection = false;
        return $this->__sendResponse('Lead', Lead::getById($lead_id), 200,'Lead has been retrieved successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function history(Request $request)
    {

        $param_rules['search'] = 'sometimes';
        $param_rules['lead_id'] = 'sometimes';

        $param['search'] = isset($request['search']) ? $request['search'] : '';
        $param['lead_id'] = isset($request['lead_id']) ? $request['lead_id'] : '';
        $this->__is_ajax = true;
        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        $param['user_id'] = $request['user_id'];
        $param['company_id'] = $request['company_id'];

        $response = LeadHistory::getList($param);
        return $this->__sendResponse('LeadHistory', $response, 200, 'Lead history list retrieved successfully.');
    }

    public function historyExport(Request $request, $lead_id)
    {

        $param_rules['search'] = 'sometimes';
        $param_rules['lead_id'] = 'required';

        $request['lead_id'] = $lead_id;
        $param['search'] = isset($request['search']) ? $request['search'] : '';
        $param['lead_id'] = isset($request['lead_id']) ? $request['lead_id'] : $lead_id;

        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        $param['user_id'] = $request['user_id'];
        $param['company_id'] = $request['company_id'];

        $data = [];
        $count = 0;
        $result = LeadHistory::getList($param);
        foreach ($result as $row){
            $data[$count] = new \stdClass();
            $data[$count]->title = $row->title;
            $data[$count]->lead_op = $row->lead_history_title;
            $user = User::getById($row->assign_id);
            $data[$count]->updated_by = "{$user->first_name} {$user->last_name}";
            $data[$count]->who = $user->email;
            $data[$count]->updated_at = $row->created_at;
            $count++;
        }

        return $this->__exportCSV(['title', 'lead_op', 'updated_by', 'who', 'updated_at'], $data);

    }

    public function leadsHistoryExport(Request $request)
    {
        $param_rules['search'] = 'sometimes';

        $param['search'] = isset($request['search']) ? $request['search'] : '';
        $param['latitude'] = isset($request['latitude']) ? $request['latitude'] : '';
        $param['longitude'] = isset($request['longitude']) ? $request['longitude'] : '';
        $param['radius'] = isset($request['radius']) ? $request['radius'] : 500;
        $param['user_ids'] = isset($request['user_ids']) ? trim($request['user_ids']) : '';
        $param['status_ids'] = isset($request['status_ids']) ? trim($request['status_ids']) : '';
        $param['start_date'] = isset($request['start_date']) ? $request['start_date'] : '';
        $param['end_date'] = isset($request['end_date']) ? $request['end_date'] : '';
        $param['lead_type_id'] = isset($request['lead_type_id']) ? $request['lead_type_id'] : '';
        $param['lead_ids'] = isset($request['lead_ids']) ? (!empty($request['lead_ids']))? explode(',', $request['lead_ids']) : [] : [];
        $param['is_lead_export'] = isset($request['export']) ? $request['export'] : true;

        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        $param['user_id'] = $request['user_id'];
        $param['company_id'] = $request['company_id'];

        $this->__is_paginate = false;
        if(empty($param['lead_ids'])) {
            $lead_response = Lead::getList($param);
            foreach ($lead_response as $lead)
                $param['lead_ids'][] = $lead->id;
        }


        $data = [];
        $count = 0;
        $result = LeadHistory::getList($param);
        //print_r($result);exit;
        foreach ($result as $row){
            $data[$count] = new \stdClass();
            $data[$count]->title = $row->title;
            $data[$count]->address = $row->address .' '. $row->zip_code.' '. $row->city;
            $data[$count]->lead_status = $row->lead_history_title;
            $user = User::getById($row->assign_id);
            $data[$count]->updated_by = "{$user->first_name} {$user->last_name}";
            $data[$count]->who = $user->email;
            $data[$count]->updated_at = $row->created_at;
            $count++;
        }

        return $this->__exportCSV(['title', 'address', 'lead_status', 'updated_by', 'who', 'updated_at'], $data, '', [], 'leads_status_history.csv');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function createAppointment(Request $request)
    {
        $param_rules['lead_id'] = 'required|exists:lead,id';
        $param_rules['query'] = 'required';
        $param_rules['appointment_date'] = 'required|date_format:"n-j-Y G:i"|after_or_equal:' . date("n-j-Y G:i");

        $appointment_date = explode(':', $request['appointment_date']);
        $appointment_date_min = (isset($appointment_date[1])) ? ((strlen($appointment_date[1]) > 1) ? $appointment_date[1] : "0{$appointment_date[1]}") : '00';
        $request['appointment_date'] = "{$appointment_date[0]}:$appointment_date_min";

        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if ($this->__is_error == true)
            return $response;

        $parse_date = explode(' ', $request['appointment_date']);
        $parse_time = $parse_date[1];
        $parse_date = explode('-', $parse_date[0]);
        $parse_month = $parse_date[0];
        $parse_day = $parse_date[1];
        $parse_year = $parse_date[2];
        $request['appointment_date'] = "$parse_year-$parse_month-$parse_day $parse_time";
        $request['appointment_date'] = date('Y-m-d H:i', strtotime($request['appointment_date']));



        $appointments = UserLeadAppointment::whereRaw("('{$request['appointment_date']}:00'" . ' between `appointment_date` and `appointment_end_date`)')
                            ->whereRaw("(user_id = {$request['user_id']} OR lead_id = {$request['lead_id']})")
                            ->whereNull('deleted_at')
                            ->orderBy('id', 'desc')
                            ->first();

        if(isset($appointments->id)){
            $appointment_date = date("n-j-Y G:i", strtotime($appointments->appointment_date));
            if($appointments->is_out_bound == 1){
                $appointment_end_date = date("n-j-Y G:i", strtotime($appointments->appointment_end_date));
                $appointment_date = "from $appointment_date to $appointment_end_date";
            }else
                $appointment_date = "for $appointment_date";

            $errors['appointment_date'] = 'Appointment is already scheduled ' .$appointment_date;
            return $this->__sendError('Validation Error.', $errors);
        }

        $obj_appointment = new UserLeadAppointment();
        $obj_appointment->lead_id = $request['lead_id'];
        $obj_appointment->user_id = $request['user_id'];
        $obj_appointment->appointment_date = $request['appointment_date'];
        $obj_appointment->appointment_end_date = $request['appointment_date'];
        $obj_appointment->is_out_bound = 0;
        $obj_appointment->type = 'lead';
        $obj_appointment->save();

        $obj_lead = Lead::find($request['lead_id']);
        $obj_lead->assignee_id = $request['user_id'];
        $obj_lead->appointment_date = $request['appointment_date'];
        $obj_lead->save();

        LeadQuery::updateQuery($request['lead_id'], json_decode($request['query'], true));


        $this->__is_paginate = false;
        $this->__is_collection = false;
        return $this->__sendResponse('Lead', Lead::getById($request['lead_id']), 200, 'Appointment Lead has been created successfully.');
    }

    public function createOutBoundAppointment(Request $request)
    {
        $param_rules['start_date'] = 'required|date_format:"n-j-Y G:i"|after_or_equal:' . date("n-j-Y G:i");
        $param_rules['end_date'] = 'required|date_format:"n-j-Y G:i"|after_or_equal:' . $request['start_date']; //date("Y-n-j G:i");
        $param_rules['result'] = 'nullable';

        $appointment_date = explode(':', $request['start_date']);
        $appointment_date_min = (isset($appointment_date[1])) ? ((strlen($appointment_date[1]) > 1)? $appointment_date[1] : "0{$appointment_date[1]}") : '00';
        $request['start_date'] = "{$appointment_date[0]}:$appointment_date_min";

        $appointment_date = explode(':', $request['end_date']);
        $appointment_date_min = (isset($appointment_date[1])) ? ((strlen($appointment_date[1]) > 1)? $appointment_date[1] : "0{$appointment_date[1]}") : '00';
        $request['end_date'] = "{$appointment_date[0]}:$appointment_date_min";

        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        $parse_date = explode(' ', $request['start_date']);
        $parse_time = $parse_date[1];
        $parse_date = explode('-', $parse_date[0]);
        $parse_month = $parse_date[0];
        $parse_day = $parse_date[1];
        $parse_year = $parse_date[2];
        $request['start_date'] = "$parse_year-$parse_month-$parse_day $parse_time";
        $request['start_date'] = date('Y-m-d h:i', strtotime($request['start_date']));

        $parse_date = explode(' ', $request['end_date']);
        $parse_time = $parse_date[1];
        $parse_date = explode('-', $parse_date[0]);
        $parse_month = $parse_date[0];
        $parse_day = $parse_date[1];
        $parse_year = $parse_date[2];
        $request['end_date'] = "$parse_year-$parse_month-$parse_day $parse_time";
        $request['end_date'] = date('Y-m-d h:i', strtotime($request['end_date']));

        $obj_appointment = new UserLeadAppointment();
        $obj_appointment->lead_id = 0;
        $obj_appointment->user_id = $request['user_id'];
        $obj_appointment->appointment_date = $request['start_date'];
        $obj_appointment->appointment_end_date = $request['end_date'];
        $obj_appointment->result = isset($request['result'])? $request['result'] : '';
        $obj_appointment->is_out_bound = 1;
        $obj_appointment->type = 'lead';
        $obj_appointment->save();

        $this->__is_paginate = false;
        $this->__collection = false;
        return $this->__sendResponse('appointment', [], 200,'Outbound appointment has been created successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function executeAppointment(Request $request)
    {
        $param_rules['lead_id'] = 'required|exists:lead,id';
        $param_rules['appointment_id'] = 'required|exists:user_lead_appointment,id';
        $param_rules['result'] = 'required';

        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;

        $param['user_id'] = $request['user_id'];
        $param['company_id'] = $request['company_id'];

        $obj_lead = Lead::find($request['lead_id']);
        $obj_lead->assignee_id = $request['user_id'];
        $obj_lead->appointment_result = $request['result'];
        $obj_lead->save();

        $obj_lead = UserLeadAppointment::find($request['appointment_id']);
        $obj_lead->result = $request['result'];
        $obj_lead->save();

        $this->__is_paginate = false;
        $this->__is_collection = false;
        return $this->__sendResponse('Lead', Lead::getById($request['lead_id']), 200,'Lead has been retrieved successfully.');
    }

    public function viewLeadReport(Request $request){

        $param['user_id'] = $request['user_id'];
        $param['company_id'] = $request['company_id'];
        $param['is_paginate'] = false;


        $response['status'] = Status::getList($param);
        $response['agent'] = User::getTenantUserList($param);
        $response['type'] = Type::whereIn('tenant_id', [$request['company_id']])->whereNull('deleted_at')->get();

        $this->__view = 'tenant.team-performance.team-report';

        $this->__is_paginate = false;
        $this->__collection = false;
        return $this->__sendResponse('Lead', $response, 200,'Lead has been retrieved successfully.');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function leadReport(Request $request)
    {
        $time_slot_map['today'] = 'INTERVAL 1 MONTH';
        $time_slot_map['yesterday'] = 'INTERVAL 1 MONTH';
        $time_slot_map['week'] = 'INTERVAL 1 MONTH';
        $time_slot_map['last_week'] = 'INTERVAL 1 MONTH';
        $time_slot_map['month'] = 'INTERVAL 1 MONTH';
        $time_slot_map['last_month'] = 'INTERVAL 1 MONTH';
        //$time_slot_map['bi_month'] = 'INTERVAL 15 DAY';
        //$time_slot_map['bi_year'] = 'INTERVAL 6 MONTH';
        $time_slot_map['year'] = 'INTERVAL 1 YEAR';
        $time_slot_map['last_year'] = 'INTERVAL 1 YEAR';
        $time_slot_map['all_time'] = '';

        $param['is_web'] = (strtolower($this->call_mode) == 'api') ? 0 : 1;
        $default_time_slot = ($param['is_web']) ? 'year' : 'all_time' ;

        $param['company_id'] = $request['company_id'];
        $param['time_slot'] = isset($request['time_slot']) ? (isset($time_slot_map[$request['time_slot']])) ? $time_slot_map[$request['time_slot']] : $time_slot_map['month'] : $time_slot_map['month'];
        $param['slot'] = isset($request['time_slot']) ? (isset($time_slot_map[$request['time_slot']])) ? $request['time_slot'] : $default_time_slot : $default_time_slot;
        $param['user_id'] = isset($request['target_user_id']) ? trim($request['target_user_id'],' ,') : '';
        $param['status_id'] = isset($request['status_id']) ? trim($request['status_id'],' ,') : '';
        $param['lead_type_id'] = isset($request['lead_type_id']) ? trim($request['lead_type_id'],' ,') : '';
        $param['lead_type_id'] = isset($request['type_id']) ? trim($request['type_id'],' ,') : $param['lead_type_id'];


        $this->__is_ajax = true;
        $list = Lead::getStatusReport($param);

        $this->__is_paginate = false;
        $this->__collection = false;
        return $this->__sendResponse('UserCommission', $list, 200,'User commission list retrieved successfully.');
    }

    public function leadUserReport(Request $request)
    {
        $time_slot_map['today'] = 'INTERVAL 1 MONTH';
        $time_slot_map['yesterday'] = 'INTERVAL 1 MONTH';
        $time_slot_map['week'] = 'INTERVAL 1 MONTH';
        $time_slot_map['last_week'] = 'INTERVAL 1 MONTH';
        $time_slot_map['month'] = 'INTERVAL 1 MONTH';
        $time_slot_map['last_month'] = 'INTERVAL 1 MONTH';
        $time_slot_map['all_time'] = '';
        //$time_slot_map['bi_month'] = 'INTERVAL 15 DAY';
        //$time_slot_map['bi_year'] = 'INTERVAL 6 MONTH';
        $time_slot_map['year'] = 'INTERVAL 1 YEAR';
        $time_slot_map['last_year'] = 'INTERVAL 1 YEAR';

        $param['is_web'] = (strtolower($this->call_mode) == 'api') ? 0 : 1;

        $default_time_slot = ($param['is_web']) ? 'all_time' : 'year';

        $param['company_id'] = $request['company_id'];
        $param['time_slot'] = isset($request['time_slot']) ? (isset($time_slot_map[$request['time_slot']])) ? $time_slot_map[$request['time_slot']] : $time_slot_map['month'] : $time_slot_map['month'];
        $param['slot'] = isset($request['time_slot']) ? (isset($time_slot_map[$request['time_slot']])) ? $request['time_slot'] : $default_time_slot : $default_time_slot;
        $param['user_id'] = isset($request['target_user_id']) ? trim($request['target_user_id'],' ,') : '';
        $param['status_id'] = isset($request['status_id']) ? trim($request['status_id'],' ,') : '';
        $param['lead_type_id'] = isset($request['lead_type_id']) ? trim($request['lead_type_id'],' ,') : '';
        $param['lead_type_id'] = isset($request['type_id']) ? trim($request['type_id'],' ,') : $param['lead_type_id'];

        $this->__is_ajax = true;
        $list = Lead::getUserStatusReport($param);

        if(!$param['is_web'])
            $list = $list['result'];

        if(isset($request['export']) && $request['export'] == true) {
            $list = $list['result'];
            $ignoreCols = [];
            $columns = ['lead_count', 'appointment_count', 'commission_count', 'commission_profit_count', 'commission_contract_count', 'agent_name'];
            $columns = ['agent_name', 'commission_count'];
            return $this->export($columns, $list,'commission.csv',$ignoreCols);
        }
        $this->__is_paginate = false;
        $this->__collection = false;
        return $this->__sendResponse('UserCommission', $list, 200,'User commission list retrieved successfully.', true);
    }

    public function leadStatusUserReport(Request $request)
    {
        $param['company_id'] = $request['company_id'];
        $param['month'] = (!isset($request['month'])) ? '' : $request['month'];
        $param['start_date'] = (!isset($request['start_date'])) ? '' : $request['start_date'];
        $param['end_date'] = (!isset($request['end_date'])) ? '' : $request['end_date'];
        $param['target_user_id'] = (!isset($request['target_user_id'])) ? '' : $request['target_user_id'];
        $param['status_id'] = (!isset($request['status_id'])) ? '' : $request['status_id'];
        $param['type_id'] = (!isset($request['type_id'])) ? '' : $request['type_id'];
        $param['export'] = isset($request['export']) ? $request['export'] : FALSE;

        $this->__is_ajax = true;
        $list = Lead::leadStatusUserReport($param);
        $this->__is_paginate = false;
        $this->__collection = false;
        
        if(isset($request['export']) && $request['export'] == true) {
            $ignoreCols = [];
            $columns = ['S.No', 'Status'];
            $columns = array_merge($columns, $list['user_names']);
            return $this->export($columns, $list['export'], 'lead_status_report.csv', $ignoreCols, 1, 1);
        }
        return $this->__sendResponse('UserLeadStatus', $list, 200,'User lead status list retrieved successfully.', true);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function leadStatsReport(Request $request)
    {
        $time_slot_map['today'] = 'INTERVAL 1 MONTH';
        $time_slot_map['yesterday'] = 'INTERVAL 1 MONTH';
        $time_slot_map['week'] = 'INTERVAL 1 MONTH';
        $time_slot_map['last_week'] = 'INTERVAL 1 MONTH';
        $time_slot_map['month'] = 'INTERVAL 1 MONTH';
        $time_slot_map['last_month'] = 'INTERVAL 1 MONTH';
        //$time_slot_map['bi_month'] = 'INTERVAL 15 DAY';
        //$time_slot_map['bi_year'] = 'INTERVAL 6 MONTH';
        $time_slot_map['year'] = 'INTERVAL 1 YEAR';
        $time_slot_map['last_year'] = 'INTERVAL 1 YEAR';
        $time_slot_map['all_time'] = '';

        $param['is_web'] = (strtolower($this->call_mode) == 'api') ? 0 : 1;
        $default_time_slot = ($param['is_web']) ? 'year' : 'all_time' ;

        $param['company_id'] = $request['company_id'];
        $param['time_slot'] = isset($request['time_slot']) ? (isset($time_slot_map[$request['time_slot']])) ? $time_slot_map[$request['time_slot']] : $time_slot_map['month'] : $time_slot_map['month'];
        $param['slot'] = isset($request['time_slot']) ? (isset($time_slot_map[$request['time_slot']])) ? $request['time_slot'] : $default_time_slot : $default_time_slot;
        $param['user_id'] = isset($request['target_user_id']) ? trim($request['target_user_id'],' ,') : '';
        $param['status_id'] = isset($request['status_id']) ? trim($request['status_id'],' ,') : '';
        $param['lead_type_id'] = isset($request['lead_type_id']) ? trim($request['lead_type_id'],' ,') : '';
        $param['lead_type_id'] = isset($request['type_id']) ? trim($request['type_id'],' ,') : $param['lead_type_id'];


        $this->__is_ajax = true;
        $list = Lead::getStatsReport($param);

        $this->__is_paginate = false;
        $this->__collection = false;
        return $this->__sendResponse('UserCommission', $list, 200,'User commission list retrieved successfully.');
    }

    public function leadStatusStatsReport(Request $request)
    {
        $time_slot_map['today'] = 'INTERVAL 1 MONTH';
        $time_slot_map['yesterday'] = 'INTERVAL 1 MONTH';
        $time_slot_map['week'] = 'INTERVAL 1 MONTH';
        $time_slot_map['last_week'] = 'INTERVAL 1 MONTH';
        $time_slot_map['month'] = 'INTERVAL 1 MONTH';
        $time_slot_map['last_month'] = 'INTERVAL 1 MONTH';
        //$time_slot_map['bi_month'] = 'INTERVAL 15 DAY';
        //$time_slot_map['bi_year'] = 'INTERVAL 6 MONTH';
        $time_slot_map['year'] = 'INTERVAL 1 YEAR';
        $time_slot_map['last_year'] = 'INTERVAL 1 YEAR';
        $time_slot_map['all_time'] = '';

        $param['is_web'] = (strtolower($this->call_mode) == 'api') ? 0 : 1;
        $default_time_slot = ($param['is_web']) ? 'year' : 'all_time' ;

        $param['company_id'] = $request['company_id'];
        $param['time_slot'] = isset($request['time_slot']) ? (isset($time_slot_map[$request['time_slot']])) ? $time_slot_map[$request['time_slot']] : $time_slot_map['month'] : $time_slot_map['month'];
        $param['slot'] = isset($request['time_slot']) ? (isset($time_slot_map[$request['time_slot']])) ? $request['time_slot'] : $default_time_slot : $default_time_slot;
        $param['user_id'] = isset($request['target_user_id']) ? trim($request['target_user_id'],' ,') : '';
        $param['status_id'] = isset($request['status_id']) ? trim($request['status_id'],' ,') : '';
        $param['lead_type_id'] = isset($request['lead_type_id']) ? trim($request['lead_type_id'],' ,') : '';
        $param['lead_type_id'] = isset($request['type_id']) ? trim($request['type_id'],' ,') : $param['lead_type_id'];
        $param['type'] = isset($request['type']) ? $request['type'] : 'percentage';


        $this->__is_ajax = true;
        $list = Lead::getStatusStatsReport($param);

        $this->__is_paginate = false;
        $this->__collection = false;
        return $this->__sendResponse('UserCommission', $list, 200,'User commission list retrieved successfully.');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $param_rules['id']       = 'required|exists:lead,id,company_id,'.$request['company_id'];

        $this->__is_ajax = true;
        $response = $this->__validateRequestParams(['id' => $id], $param_rules);

        if($this->__is_error == true)
            return $response;

        $obj_lead = Lead::find($id);
        Status::decrementLeadCount($obj_lead->status_id);
        Lead::destroy($id);
        UserLeadAppointment::destroyByLeadId($id);

        $this->__is_paginate = false;
        $this->__is_collection = false;
        $this->__collection = false;
        return $this->__sendResponse('Lead', [], 200,'Lead has been deleted successfully.');
    }

    public function templateList(Request $request)
    {
        $param_rules['user_id'] = 'required';

        $this->__is_ajax = true;
        $response = $this->__validateRequestParams($request->all(), $param_rules);


        if ($this->__is_error == true)
            return $response;

        $param['tenant_id'] = $request['company_id'];
        $response = Lead::getTemplate($request['company_id']);
        //$response = Type::getList($request['company_id']);


        $this->__is_paginate = false;
        $this->__collection = false;
        return $this->__sendResponse('Template', $response, 200, 'Your lead template has been retrieved successfully.');
    }

    public function deleteTemplate(Request $request)
    {
        $param_rules['template_id'] = 'required';
        $param_rules['user_id'] = 'required';
        $param_rules['company_id'] = 'required';

        $this->__is_ajax = true;
        $response = $this->__validateRequestParams($request->all(), $param_rules);


        if ($this->__is_error == true)
            return $response;

        $param['tenant_id'] = $request['company_id'];
        $param['template_id'] = $request['template_id'];
        Lead::deleteTemplate($param);

        $this->__is_paginate = false;
        $this->__collection = false;
        return $this->__sendResponse('Template', [], 200, 'Your lead template has been retrieved successfully.');
    }

    public function templateFieldList(Request $request)
    {
        $param_rules['user_id'] = 'required';
        $param_rules['template_id'] = 'required';

        $this->__is_ajax = true;
        $response = $this->__validateRequestParams($request->all(), $param_rules);


        if ($this->__is_error == true)
            return $response;

        $params['is_all'] = (isset($request['is_all']))? $request['is_all'] : '';
        $params['company_id'] =$request['company_id'];
        $response = Lead::getFieldsTemplateById($request['template_id'], $params);
        $ignore_columns = [];

        if(isset($request['is_all']) && $request['is_all'] == 2)
            $ignore_columns =  Config::get('constants.LEAD_IGNORE_COLUMNS');
        $response_data = [];
        foreach ($response as $key => $row){
            $response[$key]->key_map = $response[$key]->key;
            if(in_array($row->field, $ignore_columns)){
                unset($response[$key]);
                continue;
            }

            if($row->field == 'lead_name' || $row->field == 'title'){
                $response[$key]->key_map = 'title';
                $response[$key]->key = Config::get('constants.LEAD_TITLE_DISPLAY');
            }
            if(empty($row->key)){
                $response[$key]->key = $row->field;
                $response[$key]->key_map = $row->field;
            }
            if(empty($row->index_map)){
                $response[$key]->index_map = (ctype_digit($row->field)? '' : $row->field);
            }
            $response_data[] = $response[$key];
        }
        //$response['orderable_columns'] = Config::get('constants.LEAD_DEFAULT_COLUMNS');
        $this->__is_paginate = false;
        $this->__collection = false;
        return $this->__sendResponse('Template', $response_data, 200, 'Your lead template has been retrieved successfully.');
    }

    public function defaultFieldList(Request $request)
    {
        $param_rules['user_id'] = 'required';

        $this->__is_ajax = true;
        $response = $this->__validateRequestParams($request->all(), $param_rules);


        if ($this->__is_error == true)
            return $response;

        $params['is_all'] = (isset($request['is_all']))? $request['is_all'] : '';
        $params['tenant_id'] =$request['company_id'];
        $response = Lead::getFieldsDefault($params);
        foreach ($response as $key => $row){
            //print_r($row->key);
            if($row->key == 'title' || $row->key == 'lead_name'){
                $response[$key]->key = Config::get('constants.LEAD_TITLE_DISPLAY');
            }
            //exit;
        }//exit;

        $this->__is_paginate = false;
        $this->__collection = false;
        return $this->__sendResponse('Template', $response, 200, 'Your lead default has been retrieved successfully.');
    }

    public function templateShow(Request $request, $id)
    {
        $param_rules['id'] = 'required|exists:tenant_template,id';
        $this->__is_ajax = true;
        $response = $this->__validateRequestParams(['id' => $id], $param_rules);

        if($this->__is_error == true)
            return $response;

        $this->__is_paginate = false;
        $this->__collection = false;
        return $this->__sendResponse('Lead', Lead::getTemplateDetailById($request['company_id'], $id), 200,'Lead template has been retrieved successfully.');
    }


    public function templateDestroy(Request $request, $id)
    {
        $param_rules['id']       = 'required|exists:tenant_template,id,tenant_id,'.$request['company_id'];

        $this->__is_ajax = true;
        $response = $this->__validateRequestParams(['id' => $id], $param_rules);

        if($this->__is_error == true)
            return $response;

        TenantTemplate::destroy($id);

        $this->__is_paginate = false;
        $this->__is_collection = false;
        $this->__collection = false;
        return $this->__sendResponse('Tempalte', [], 200,'Lead has been deleted successfully.');
    }

    public function templateUpdate(Request $request, $id)
    {
        $request['id'] = $id;
        $param_rules['id'] = 'required|exists:tenant_template,id,tenant_id,'.$request['company_id'];
        $param_rules['template_title'] = 'required';

        $this->__is_ajax = true;
        $response = $this->__validateRequestParams($request->all(), $param_rules);

        if($this->__is_error == true)
            return $response;


        $obj_lead = TenantTemplate::find($id);
        $obj_lead->title = $request['template_title'];
        $obj_lead->save();

        $this->__is_paginate = false;
        $this->__collection = false;
        return $this->__sendResponse('Lead', Lead::getTemplateDetailById($request['company_id'], $id), 200,'Lead template has been retrieved successfully.');
    }


}
