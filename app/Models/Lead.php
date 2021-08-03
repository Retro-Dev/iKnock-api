<?php

namespace App\Models;

use App\Libraries\Helper;
use App\Libraries\History;
use function GuzzleHttp\Psr7\str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;

class Lead extends Model
{
    protected $table = "lead";
    use SoftDeletes;

    public static function getById($id)
    {

        $query = self::select();
        $query->with('tenantQuery');
        $query->with('leadCustom');
        /*$query->leftjoin('tenant_query',function ($tenant_join){
            $tenant_join->leftjoin('lead_query',function ($join) use ($tenant_join){
                $join->on(\DB::raw('( lead_query.query_id = tenant_query.id AND lead_query.lead_id = lead.id )'));
            });
        });*/

        $query->with('leadStatus');
        $query->with('leadType');
        $query->with('leadMedia');
        return $query->where('id', $id)
            ->first();
    }

    public static function saveTempFile($params)
    {

        \DB::statement("INSERT INTO tenant_tmp_file (tenant_id, media_url, created_at) VALUES 
                    ({$params['tenant_id']}, '{$params['media_url']}', NOW())
                    ON DUPLICATE KEY UPDATE media_url = '{$params['media_url']}'");

        return true;
    }

    public static function bulkUpdate($params)
    {
        $lead_ids = [];
        $lead_status_count = [];
        $lead_history = [];
        $lead_result = \DB::select("SELECT * FROM lead WHERE company_id = {$params['company_id']} AND id IN ({$params['lead_ids']})");

        //History implementation
        $obj_history = new History();
        $obj_history->history_trigger_prefx = 'lead';
        $obj_history->history_trigger_map = ['bulkAssignee', 'bulkExpired', 'bulkStatus'];
        //$obj_history->history_trigger_map['bulkStatus'] = 'bulkStatus';
        //$obj_history->initiate($lead_old_data, $request->all());

        foreach ($lead_result as $lead_row) {
            $lead_ids[] = $lead_row->id;
            if (!empty($params['status_id']) && $params['status_id'] != $lead_row->status_id) {
                if (!isset($lead_status_count[$lead_row->status_id]))
                    $lead_status_count[$lead_row->status_id] = 0;

                //$lead_history[] = "({$lead_row->id},{$params['target_user_id']}, {$params['status_id']}, NOW())";
                $lead_status_count[$lead_row->status_id]++;
            }
            $lead_old_data = (array)$lead_row;
            $params['id'] = $lead_row->id;
            $params['target_id'] = (($params['assign_id'] == ''))? $lead_row->assignee_id : $params['assign_id'];

            if(empty($params['status_id']))
                $params['status_id'] = $lead_row->status_id;

            //if(($params['is_expired'] != 0 && $params['is_expired'] != 1))
            if($params['is_expired'] == '')
                $params['is_expired'] = $lead_row->is_expired;



            $obj_history->initiate($lead_old_data, $params);
        }

        $params['lead_ids'] = implode(',', $lead_ids);


        $update_value = [];
        if (!empty($params['assign_id']))
            $update_value[] = 'assignee_id = ' . $params['assign_id'];

        if (!empty($params['status_id'])) {
            $update_value[] = 'status_id = ' . $params['status_id'];

            if (count($lead_history))
                \DB::statement("INSERT INTO lead_history (lead_id, assign_id, status_id, created_at) VALUES " . implode(',', $lead_history));

            $update_status_count = 0;
            foreach ($lead_status_count as $status_id => $status_count) {
                \DB::statement("Update `status` SET lead_count = (lead_count - $status_count) WHERE id = $status_id");
                $update_status_count += $status_count;
            }

            if ($update_status_count)
                \DB::statement("Update `status` SET lead_count = (lead_count + $update_status_count) WHERE id = {$params['status_id']}");
        }

        if (!empty($params['type_id']))
            $update_value[] = 'type_id = ' . $params['type_id'];

        if (($params['is_expired'] == 0 || $params['is_expired'] == 1) && $params['is_expired'] != '' )
            $update_value[] = 'is_expired = ' . $params['is_expired'];

        if ($params['action'] == 'delete') {
            $update_value = [];
            $update_value[] = 'deleted_at = NOW()';
            UserLeadAppointment::destroyByLeadId($params['lead_ids']);
        }
        \DB::statement("Update lead SET " . implode(',', $update_value) . " WHERE id IN ({$params['lead_ids']})");
        $obj_history->bulkExecute();
        return true;
    }

    public static function saveTemplate($params)
    {

        \DB::statement("INSERT INTO tenant_template (tenant_id, title, description, created_at) VALUES 
                    ({$params['tenant_id']}, '{$params['title']}', '{$params['description']}', NOW())");
        return \DB::getPdo()->lastInsertId();
    }

    public static function saveLead($obj)
    {
        $pdo_parmas = [];
        $pdo_parmas[] = $obj->assignee_id;
        $pdo_parmas[] = $obj->title;
        $pdo_parmas[] = $obj->owner;
        $pdo_parmas[] = $obj->county;
        $pdo_parmas[] = $obj->state;
        $pdo_parmas[] = $obj->address;
        $pdo_parmas[] = $obj->foreclosure_date;
        $pdo_parmas[] = $obj->admin_notes;
        $pdo_parmas[] = $obj->formatted_address;
        $pdo_parmas[] = $obj->city;
        $pdo_parmas[] = $obj->zip_code;
        \DB::statement("INSERT INTO lead (`creator_id`, `company_id`, `assignee_id`, `title`, `owner`, `county`, `state`, `address`,  `foreclosure_date`, `admin_notes`, `type_id`, `status_id`, `latitude`, `longitude`, `formatted_address`, `city`, `zip_code`, `updated_at`, `created_at`) VALUES ({$obj->creator_id} , {$obj->company_id}, ?, ?, ?, ?, ?, ?, ?, ?,{$obj->type_id}, {$obj->status_id}, '{$obj->latitude}', '{$obj->longitude}', ?, ?,?, NOW(), NOW())", $pdo_parmas);
        return \DB::getPdo()->lastInsertId();
    }

    public static function getTemplateById($template_id, $join = 'LEFT', $field_id = 0)
    {
        $field_where_clause = '';
        if(!empty($field_id))
            $field_where_clause = " AND tenant_custom_field.id = $field_id";

        $result = \DB::select("Select IFNULL(tenant_custom_field.id, template_fields.field) as id, IFNULL(tenant_custom_field.`key`,
template_fields.field) as `key`, template_fields.* 
FROM template_fields $join JOIN tenant_custom_field ON tenant_custom_field.id = 
                  template_fields.field WHERE template_fields.template_id = $template_id $field_where_clause ORDER BY template_fields.order_by ASC");
        return $result;
    }

    public static function getByTemplateId($template_id, $id ='', $join = 'LEFT')
    {
        $id = !empty($id) ? " HAVING id= '{$id}'" : '';
        $result = \DB::select("Select IFNULL(tenant_custom_field.id, template_fields.field) as id, IFNULL(tenant_custom_field.`key`,template_fields.field) as `key`, template_fields.* FROM template_fields $join JOIN tenant_custom_field ON tenant_custom_field.id = 
                  template_fields.field WHERE template_fields.template_id = $template_id  $id ORDER BY template_fields.order_by ASC");
        return $result;
    }

    public static function getByTenantTemplateFieldDetail($id, $join = 'LEFT')
    {
        $result = \DB::select("Select tenant_custom_field.id, tenant_custom_field.`key`, template_fields.*
        FROM template_fields $join JOIN tenant_custom_field ON tenant_custom_field.id = template_fields.field 
        WHERE tenant_custom_field.id = $id ORDER BY tenant_custom_field.order_by ASC");
        return $result;
    }

    public static function getFieldsTemplateById($template_id, $params)
    {
        $join_type = 'INNER';
        if(!empty($params['is_all']))
            $join_type = 'INNER';
        $join_type = 'LEFT';
        if($params['is_all'] == 2)
            $join_type = 'LEFT';
        if($template_id == 'max'){
            $max_template =   Type::getByMax($params['company_id']);
            $template_id = $max_template->id;
        }
            /*$result = \DB::select("Select template_fields.*, field as id, template_fields.*, IFNULL(tenant_custom_field.key, template_fields.field) as `key` FROM template_fields
                          $join_type  JOIN tenant_custom_field ON tenant_custom_field.id = template_fields.field 
                          WHERE template_fields.template_id = $template_id  ORDER BY template_fields.order_by ASC");*/

            $result = \DB::select("Select template_fields.*, field as id, tenant_custom_field.key from template_fields
                                  LEFT JOIN tenant_custom_field ON tenant_custom_field.id = template_fields.field 
                                  WHERE template_fields.template_id = $template_id  ORDER BY template_fields.order_by ASC;");

        $columns = Config::get('constants.LEAD_DEFAULT_COLUMNS');
        //['title', 'lead_type','address', 'city', 'county', 'state', 'zip_code', 'is_expired'];
        if($params['is_all'] == 2)
            $columns = [];

        $defual_set = [];
        /*if(!empty($params['is_all'])) {
            foreach ($columns as $column) {
                $tmp['id'] = $column;
                $tmp['template_id'] = 0;
                $tmp['field'] = $column;
                $tmp['index'] = 0;
                $tmp['key'] = $column;

                $defual_set[] = $tmp;
            }
            //$result = array_merge($defual_set, $result);
        }*/

        return $result;
    }

    public static function getFieldsDefault($params)
    {

        $result = \DB::select("Select * FROM tenant_custom_field
                          WHERE tenant_id = {$params['tenant_id']}  ORDER BY tenant_custom_field.order_by ASC");

        $columns = ['title', 'owner', 'lead_type', 'address','foreclosure_date',  'city', 'county', 'state', 'is_expired'];
        if ($params['is_all'] == 2)
            $columns = [];

        $defual_set = [];
        if (!empty($params['is_all'])) {
            foreach ($columns as $column) {
                $tmp['template_id'] = 0;
                $tmp['field'] = $column;
                $tmp['index'] = 0;
                $tmp['key'] = $column;

                $defual_set[] = $tmp;
            }
            $result = array_merge($defual_set, $result);
        }

        return $result;
    }

    public static function saveTemplateFields($template_id, $data, $index_map = [], $order_by = 1)
    {
        $statements = [];
        $index_map_col = '';
        foreach ($data as $key => $value) {
            if(isset($index_map[$key])){
                $index_value = $index_map[$key];
                $index_map_col = ', index_map';
                $statements[] = "($template_id, '{$key}', '$value', $order_by, '$index_value')";
            }else {
                $statements[] = "($template_id, '{$key}', '$value', $order_by)";
            }
        }

        \DB::statement("INSERT INTO template_fields (template_id, field, `index`, order_by $index_map_col) VALUES " .
            implode(',', $statements) . "ON DUPLICATE KEY UPDATE `index` = VALUES(`index`)");
        return true;
    }

    public static function saveTemplateField($template_id, $data)
    {
        $is_order_by = '';
        foreach ($data as $row) {
            if(isset($row['order_by'])){
                $is_order_by = ' , order_by';
                $statements[] = "($template_id, '{$row['field']}', '{$row['index']}', '{$row['index_map']}', {$row['order_by']})";
            }else
                $statements[] = "($template_id, '{$row['field']}', '{$row['index']}', '{$row['index_map']}')";
        }


        \DB::statement("INSERT INTO template_fields (template_id, field, `index`, index_map $is_order_by) VALUES " .
            implode(',', $statements) . "ON DUPLICATE KEY UPDATE `index` = VALUES(`index`), `index_map` = VALUES(`index_map`)");
        return true;
    }

    public static function getTemplateFields($template_id)
    {
        return \DB::select("SELECT * FROM template_fields WHERE  template_id = $template_id");
    }

    public static function deleteTemplateFields($template_id, $field_id)
    {
        \DB::statement("DELETE FROM template_fields WHERE  template_id = $template_id AND  field = '$field_id'");
        return true;
    }

    public static function deleteDefaultLeadFields($field_id)
    {
        \DB::statement("DELETE FROM tenant_custom_field WHERE  id = $field_id");
        return true;
    }

    public static function getTemplate($tenant_id)
    {
        return \DB::select("SELECT id, title FROM tenant_template WHERE tenant_id = $tenant_id AND deleted_at IS  NULL ORDER BY 1 ASC");
    }

    public static function deleteTemplate($params)
    {
        \DB::statement("Update tenant_template SET deleted_at = NOW() WHERE id = {$params['template_id']} AND tenant_id = {$params['tenant_id']})");
    }

    public static function getTemplateDetailById($tenant_id, $template_id)
    {
        return \DB::select("SELECT id, title FROM tenant_template WHERE id = $template_id AND tenant_id = $tenant_id ORDER BY 1 DESC");
    }

    public static function getTempfile($tenant_id)
    {
        $result = \DB::select("SELECT * FROM tenant_tmp_file WHERE tenant_id = $tenant_id ORDER BY 1 DESC LIMIT 1");
        return (isset($result[0])) ? $result[0] : [];
    }

    public static function getList($params)
    {

        $lat = $params['latitude'];
        $lng = $params['longitude'];
        $radius = $params['radius'];

        $haversine = "(3959 * acos (
                    cos ( radians($lat) )
                    * cos( radians(`latitude`) )
                    * cos( radians(`longitude`) - radians($lng) )
                    + sin ( radians($lat) )
                    * sin( radians(`latitude`) )
                ))";

        $query = self::select('lead.*',\DB::raw('type.title as lead_type'));
        $query->leftJoin('type','type.id','lead.type_id');
        $query->where('lead.company_id', $params['company_id']);
        $query->whereNull('lead.deleted_at');

        if (isset($params['user_ids']) && !empty($params['user_ids']))
            $query->whereRaw('lead.assignee_id  IN (' . $params['user_ids'] . ')');

        if (isset($params['status_ids']) && !empty($params['status_ids']))
            $query->whereRaw('lead.status_id  IN (' . $params['status_ids'] . ')');

        if (!isset($params['is_web']) || $params['is_web'] == 0) {
            $query->where('lead.is_expired', 0);
        }

        if (isset($params['start_date']) && isset($params['end_date']) && !empty($params['start_date']) && !empty($params['end_date'])) {
            $params['start_date'] = date('Y-m-d', strtotime($params['start_date']));
            $params['end_date'] = date('Y-m-d', strtotime($params['end_date']));
            $query->whereRaw("lead.created_at >= '{$params['start_date']} 00:00:00' && lead.created_at <= '{$params['end_date']} 23:59:59'");
        }

        if (!empty($params['lead_type_id'])) {
            $query->whereRaw('lead.type_id  IN (' . $params['lead_type_id'] . ')');
        }
        if(!isset($params['order_by'])){
            $params['order_by'] = 'id';
            $params['order_type'] = 'desc';
        }
        $order_by = 'lead.'.$params['order_by'];
        if(strtolower($params['order_by']) == 'lead_type')
            $order_by = 'type.title';

        if(strtolower($params['order_by']) == 'first_name' || strtolower($params['order_by']) == 'last_name')
            $order_by = 'lead.owner';

        $query->orderBy($order_by, $params['order_type']);

        if (isset($params['search']) && !empty($params['search'])) {
           // $query->leftJoin('type', 'type.id', 'lead.type_id');
            $query->whereRaw("(lead.title like '%{$params['search']}%' OR formatted_address like '%{$params['search']}%' 
             OR address like '%{$params['search']}%' OR type.title like '%{$params['search']}%' )");

        }

        $time_clause = '';
        $group_by__clause = '';
        if (!empty($params['time_slot'])) {
            //$time_clauses['today'] = " AND lead.created_at >= CURDATE() AND lead.created_at < CURDATE() + INTERVAL 1 DAY";
            $time_clauses['today'] = " DATE(lead.created_at) = DATE(NOW())";
            $time_clauses['yesterday'] = " DATE(lead.created_at) = DATE(NOW() - INTERVAL 1 DAY)";
            $time_clauses['week'] = " lead.created_at >= DATE(NOW()) - INTERVAL 7 DAY";
            $time_clauses['last_week'] = " YEARWEEK(lead.created_at) = YEARWEEK(NOW() - INTERVAL 1 WEEK)";
            $time_clauses['month'] = " lead.created_at >= DATE(NOW()) - INTERVAL 1 MONTH";
            $time_clauses['last_month'] = " year(lead.created_at) = year(NOW() - INTERVAL 1 MONTH)  AND month(lead.created_at) = Month(NOW() - INTERVAL 1 MONTH) ";
            $time_clauses['year'] = " lead.created_at > DATE_SUB(NOW(),INTERVAL 1 YEAR)";
            $time_clauses['last_year'] = " year(lead.created_at) = year(NOW() - INTERVAL 1 YEAR)";

            $group_by_clauses['today'] = " hour, day, month, year";
            $group_by_clauses['yesterday'] = " hour, day, month, year";
            $group_by_clauses['week'] = " day, month, year";
            $group_by_clauses['last_week'] = " day, month, year";
            $group_by_clauses['month'] = " day, month, year";
            $group_by_clauses['last_month'] = " day, month, year";
            $group_by_clauses['year'] = " month, year";
            $group_by_clauses['last_year'] = " month, year";

            $slot_types['today'] = " hour";
            $slot_types['yesterday'] = " hour";
            $slot_types['week'] = " day";
            $slot_types['last_week'] = " day";
            $slot_types['month'] = " day";
            $slot_types['last_month'] = " day";
            $slot_types['year'] = " month";
            $slot_types['last_year'] = " month";

            $time_clause = $time_clauses[$params['slot']];
            $group_by__clause = $group_by_clauses[$params['slot']];

            $query->whereRaw("$time_clause");
        }

        if (!empty($params['latitude']) && !empty($params['longitude']))
            $query->selectRaw("{$haversine} AS distance")
                ->whereRaw("{$haversine} < ?", [$radius]);

        if ((isset($params['is_status_group_by']) && $params['is_status_group_by'])) {
            $query->select('lead.*',\DB::raw('count(lead.id) as lead_count'));
            //$query->leftJoin('status','status.id','lead.status_id');
            $query->groupBy('lead.status_id');
            return $query->get();
        }

        if ((isset($params['is_paginate']) && !$params['is_paginate']) || $params['export'] === 'true')
            return $query->get();



        return $query->paginate(Config::get('constants.PAGINATION_PAGE_SIZE'));
    }

    public static function getUserList($params)
    {

        $lat = $params['latitude'];
        $lng = $params['longitude'];
        $radius = $params['radius'];

        $haversine = "(3959 * acos (
                    cos ( radians($lat) )
                    * cos( radians(`latitude`) )
                    * cos( radians(`longitude`) - radians($lng) )
                    + sin ( radians($lat) )
                    * sin( radians(`latitude`) )
                ))";


        $query = self::select('lead.*');
        $query->where('company_id', $params['company_id']);
        $query->where('assignee_id', $params['user_id']);
        $query->whereNull('lead.deleted_at');


        if (isset($params['status_ids']) && !empty($params['status_ids']))
            $query->whereRaw('lead.status_id IN (' . $params['status_ids'] . ')');

        if (isset($params['start_date']) && isset($params['end_date']) && !empty($params['start_date']) && !empty($params['end_date']))
            $query->whereRaw("created_at >= '{$params['start_date']} 00:00:00' && created_at <= '{$params['end_date']} 23:59:59'");

        if (!empty($params['lead_type_id'])) {
            $query->whereRaw('lead.type_id IN (' . $params['lead_type_id'] . ')');
        }


        if (isset($params['search']) && !empty($params['search'])) {
            $query->leftJoin('type', 'type.id', 'lead.type_id');
            $query->whereRaw("(lead.title like '%{$params['search']}%' OR formatted_address like '%{$params['search']}%' 
             OR address like '%{$params['search']}%' OR type.title like '%{$params['search']}%' )");

        }

        $time_clause = '';
        $group_by__clause = '';
        if (!empty($params['time_slot'])) {
            //$time_clauses['today'] = " AND lead.created_at >= CURDATE() AND lead.created_at < CURDATE() + INTERVAL 1 DAY";
            $time_clauses['today'] = " DATE(lead.created_at) = DATE(NOW())";
            $time_clauses['yesterday'] = " DATE(lead.created_at) = DATE(NOW() - INTERVAL 1 DAY)";
            $time_clauses['week'] = " lead.created_at >= DATE(NOW()) - INTERVAL 7 DAY";
            $time_clauses['last_week'] = " YEARWEEK(lead.created_at) = YEARWEEK(NOW() - INTERVAL 1 WEEK)";
            $time_clauses['month'] = " lead.created_at >= DATE(NOW()) - INTERVAL 1 MONTH";
            $time_clauses['last_month'] = " year(lead.created_at) = year(NOW() - INTERVAL 1 MONTH)  AND month(lead.created_at) = Month(NOW() - INTERVAL 1 MONTH) ";
            $time_clauses['year'] = " lead.created_at > DATE_SUB(NOW(),INTERVAL 1 YEAR)";
            $time_clauses['last_year'] = " year(lead.created_at) = year(NOW() - INTERVAL 1 YEAR)";

            $group_by_clauses['today'] = " hour, day, month, year";
            $group_by_clauses['yesterday'] = " hour, day, month, year";
            $group_by_clauses['week'] = " day, month, year";
            $group_by_clauses['last_week'] = " day, month, year";
            $group_by_clauses['month'] = " day, month, year";
            $group_by_clauses['last_month'] = " day, month, year";
            $group_by_clauses['year'] = " month, year";
            $group_by_clauses['last_year'] = " month, year";

            $slot_types['today'] = " hour";
            $slot_types['yesterday'] = " hour";
            $slot_types['week'] = " day";
            $slot_types['last_week'] = " day";
            $slot_types['month'] = " day";
            $slot_types['last_month'] = " day";
            $slot_types['year'] = " month";
            $slot_types['last_year'] = " month";

            $time_clause = $time_clauses[$params['slot']];
            $group_by__clause = $group_by_clauses[$params['slot']];

            $query->whereRaw("$time_clause");
        }


        if (!empty($params['latitude']) && !empty($params['longitude']))
            $query->selectRaw("{$haversine} AS distance")
                ->whereRaw("{$haversine} < ?", [$radius]);

        $query->with('tenantQuery');
        $query->with('leadCustom');
        $query->with('leadStatus');
        $query->with('leadType');
        $query->with('leadMedia');

        $query->orderBy('lead.id', 'desc');
        return $query->paginate(Config::get('constants.PAGINATION_PAGE_SIZE'));
    }

    public function leadQuery()
    {
        return self::hasMany('App\Models\LeadQuery', 'lead_id');
    }

    public function tenantQuery()
    {
        return self::hasMany('App\Models\TenantQuery', 'tenant_id', 'company_id')
            ->orderBy('tenant_query.order_by')
            /*->leftjoin('lead_query',function ($join){

                $join->on('lead_query.query_id','=','tenant_query.id')
                    ->where('lead_query.lead_id', 'lead.id');
                //$join->on(\DB::raw('( lead_query.query_id = tenant_query.id AND lead_query.lead_id = lead.id )'));
                //'lead_query.id',

                //$join->on(DB::raw('(  bookings.arrival between ? and ? OR bookings.departure between ? and ? )'), DB::raw(''), DB::raw(''));
            })*/
            /*->select(['tenant_query.tenant_id','tenant_query.type','tenant_query.query', 'lead_query.created_at',
                'lead_query.lead_id','lead_query.response'])*/
            ;
    }

    public function leadMedia()
    {
        return self::hasMany('App\Models\Media', 'source_id')
            ->where('source_type', 'lead')
            ->whereNull('deleted_at');
    }

    public function leadCustom()
    {
        return self::hasMany('App\Models\LeadCustomField', 'lead_id', 'id')
            ->select(['lead_custom_field.id', 'lead_custom_field.lead_id', 'tenant_custom_field.key', 'lead_custom_field.value'])
            ->leftJoin('tenant_custom_field','tenant_custom_field.id','lead_custom_field.tenant_custom_field_id')
            ->groupBy('tenant_custom_field.id')
            ->orderBy('tenant_custom_field.order_by');
    }

    public function leadStatus()
    {
        return self::hasOne('App\Models\Status', 'id', 'status_id');
    }

    public function leadType()
    {
        return self::hasOne('App\Models\Type', 'id', 'type_id');
    }

    public static function getStatsReport($params)
    {
        $tenant_clause = ' AND lead.company_id = ' . $params['company_id'];

        $user_clause = '';
        if (!empty($params['user_id']))
            $user_clause = ' AND lead.assignee_id IN (' . $params['user_id'] . ')';

        $time_clause = '';
        $group_by__clause = '';
        if (!empty($params['time_slot'])) {
            //$time_clauses['today'] = " AND lead.created_at >= CURDATE() AND lead.created_at < CURDATE() + INTERVAL 1 DAY";
            $time_clauses['today'] = " AND DATE(lead.created_at) = DATE(NOW())";
            $time_clauses['yesterday'] = " AND DATE(lead.created_at) = DATE(NOW() - INTERVAL 1 DAY)";
            $time_clauses['week'] = " AND lead.created_at >= DATE(NOW()) - INTERVAL 7 DAY";
            $time_clauses['last_week'] = " AND YEARWEEK(lead.created_at) = YEARWEEK(NOW() - INTERVAL 1 WEEK)";
            $time_clauses['month'] = " AND lead.created_at >= DATE(NOW()) - INTERVAL 1 MONTH";
            $time_clauses['last_month'] = " AND year(lead.created_at) = year(NOW() - INTERVAL 1 MONTH)  AND month(lead.created_at) = Month(NOW() - INTERVAL 1 MONTH) ";
            $time_clauses['year'] = " AND lead.created_at > DATE_SUB(NOW(),INTERVAL 1 YEAR)";
            $time_clauses['last_year'] = " AND year(lead.created_at) = year(NOW() - INTERVAL 1 YEAR)";

            $group_by_clauses['today'] = " hour, day, month, year";
            $group_by_clauses['yesterday'] = " hour, day, month, year";
            $group_by_clauses['week'] = " day, month, year";
            $group_by_clauses['last_week'] = " day, month, year";
            $group_by_clauses['month'] = " day, month, year";
            $group_by_clauses['last_month'] = " day, month, year";
            $group_by_clauses['year'] = " month, year";
            $group_by_clauses['last_year'] = " month, year";

            $slot_types['today'] = " hour";
            $slot_types['yesterday'] = " hour";
            $slot_types['week'] = " day";
            $slot_types['last_week'] = " day";
            $slot_types['month'] = " day";
            $slot_types['last_month'] = " day";
            $slot_types['year'] = " month";
            $slot_types['last_year'] = " month";

            $time_clause = $time_clauses[$params['slot']];
            $group_by__clause = $group_by_clauses[$params['slot']];
        }
        //print_r($params['status_id']);exit;
        $status_clause = '';
        $status_qry = Status::whereIn('tenant_id', [$params['company_id']])->whereNull('deleted_at');
        if (!empty($params['status_id'])) {
            $status_qry->where('id', $params['status_id']);
            $status_clause = ' AND lead.status_id IN (' . $params['status_id'] . ')';
        } else {
            $status_result = $status_qry->get();
            $status_id = $status_result[0]['id'];
            $status_clause = " AND status_id != $status_id";
        }

        $type_clause = '';
        if (!empty($params['lead_type_id']))
            $type_clause = ' AND lead.type_id IN (' . $params['lead_type_id'] . ')';


        $result = \DB::select("Select count(*) as lead_count from lead WHERE 1 = 1 $status_clause $tenant_clause $time_clause $user_clause $type_clause
                              union all 
                              Select count(*) as lead_count from lead where 1 = 1 $status_clause $tenant_clause $time_clause $user_clause $type_clause
                              union all 
                              Select count(*) as lead_count from lead where appointment_date IS NOT NULL $status_clause $tenant_clause $time_clause $user_clause $type_clause");
        $response = [];
        //print_r($result);exit;

        $total_leads = empty($result[0]->lead_count) ? 1 : $result[0]->lead_count;
        $total_leads_contacted = $result[1]->lead_count;
        $total_leads_appointed = $result[2]->lead_count;

        $response[0]['title'] = 'leads contacted';
        $response[0]['value'] = floatval(number_format(($total_leads_contacted / $total_leads) * 100, 2));
        $response[0]['colour_code'] = sprintf('#%06X', mt_rand(0, 0xFFFFFF));

        $response[1]['title'] = 'leads appointed';
        $response[1]['value'] = floatval(number_format(($total_leads_appointed / $total_leads) * 100, 2));
        $response[1]['colour_code'] = sprintf('#%06X', mt_rand(0, 0xFFFFFF));

        return $response;
    }

    public static function getStatusStatsReport($params)
    {
        $tenant_clause = ' AND lead.company_id = ' . $params['company_id'];

        $user_clause = '';
        if (!empty($params['user_id']))
            $user_clause = ' AND lead.assignee_id IN (' . $params['user_id'] . ')';

        $time_clause = '';
        $group_by__clause = '';
        if (!empty($params['time_slot'])) {
            //$time_clauses['today'] = " AND lead.created_at >= CURDATE() AND lead.created_at < CURDATE() + INTERVAL 1 DAY";
            $time_clauses['today'] = " AND DATE(lead.created_at) = DATE(NOW())";
            $time_clauses['yesterday'] = " AND DATE(lead.created_at) = DATE(NOW() - INTERVAL 1 DAY)";
            $time_clauses['week'] = " AND lead.created_at >= DATE(NOW()) - INTERVAL 7 DAY";
            $time_clauses['last_week'] = " AND YEARWEEK(lead.created_at) = YEARWEEK(NOW() - INTERVAL 1 WEEK)";
            $time_clauses['month'] = " AND lead.created_at >= DATE(NOW()) - INTERVAL 1 MONTH";
            $time_clauses['last_month'] = " AND year(lead.created_at) = year(NOW() - INTERVAL 1 MONTH)  AND month(lead.created_at) = Month(NOW() - INTERVAL 1 MONTH) ";
            $time_clauses['year'] = " AND lead.created_at > DATE_SUB(NOW(),INTERVAL 1 YEAR)";
            $time_clauses['last_year'] = " AND year(lead.created_at) = year(NOW() - INTERVAL 1 YEAR)";

            $group_by_clauses['today'] = " hour, day, month, year";
            $group_by_clauses['yesterday'] = " hour, day, month, year";
            $group_by_clauses['week'] = " day, month, year";
            $group_by_clauses['last_week'] = " day, month, year";
            $group_by_clauses['month'] = " day, month, year";
            $group_by_clauses['last_month'] = " day, month, year";
            $group_by_clauses['year'] = " month, year";
            $group_by_clauses['last_year'] = " month, year";

            $slot_types['today'] = " hour";
            $slot_types['yesterday'] = " hour";
            $slot_types['week'] = " day";
            $slot_types['last_week'] = " day";
            $slot_types['month'] = " day";
            $slot_types['last_month'] = " day";
            $slot_types['year'] = " month";
            $slot_types['last_year'] = " month";

            $time_clause = $time_clauses[$params['slot']];
            $group_by__clause = $group_by_clauses[$params['slot']];
        }
        //print_r($params['status_id']);exit;
        $status_clause = '';
        $status_qry = Status::whereIn('tenant_id', [$params['company_id']])->whereNull('deleted_at');
        if (!empty($params['status_id'])) {
            $status_qry->where('id', $params['status_id']);
            $status_clause = ' AND lead.status_id IN (' . $params['status_id'] . ')';
        } else {
            $status_qry->select('id');
            $status_qry->where('is_permanent', 1);
            $status_result = $status_qry->get();

            $status_ids = [];
            foreach ($status_result as $row){
                $status_ids[] = $row->id;
            }
            $status_id = $status_result[0]['id'];
            $status_clause = " AND status_id NOT IN (".implode(',', $status_ids).")";
        }

        $type_clause = '';
        if (!empty($params['lead_type_id']))
            $type_clause = ' AND lead.type_id IN (' . $params['lead_type_id'] . ')';

        /*
         * Select count(*) as lead_count from lead where 1 = 1 $status_clause $tenant_clause $time_clause $user_clause $type_clause
                              union all
         * */
        $result = \DB::select("Select count(*) as lead_count, status.title as status_title, color_code from lead 
                                LEFT JOIN status ON status.id = lead.status_id   
                                WHERE 1 = 1 $status_clause $tenant_clause $time_clause $user_clause $type_clause                                
                                group by status_id
                              union all                                
                              Select count(*) as lead_count, 'lead appointed' as status_title, '' as color_code from lead where appointment_date IS NOT NULL $status_clause $tenant_clause $time_clause $user_clause $type_clause");
        $response = [];
        $processed = [];
        $total_leads_contacted = 0;
        foreach($result as $row) {
            $tmp['title'] = $row->status_title; //'leads contacted';
            $tmp['value'] = $row->lead_count; //floatval(number_format(($total_leads_contacted / $total_leads) * 100, 2));
            $tmp['colour_code'] = (!empty($row->color_code))? $row->color_code : sprintf('#%06X', mt_rand(0, 0xFFFFFF));
            $total_leads_contacted += $row->lead_count;

            $processed[] = $tmp;
        }

        if(empty($total_leads_contacted))
            $total_leads_contacted = 1;

        foreach($processed as $row) {
            $tmp['title'] = $row['title'];
            $tmp['value'] = floatval(number_format(($row['value'] / $total_leads_contacted) * 100, 2));
            $tmp['colour_code'] = $row['colour_code'];

            $response[] = $tmp;
        }

        return ($params['type'] == 'amount') ? $processed : $response;
    }

    public static function getStatusReport($params)
    {
        $tenant_clause = ' AND lead.company_id = ' . $params['company_id'];

        $user_clause = '';
        if (!empty($params['user_id']))
            $user_clause = ' AND lead.assignee_id IN (' . $params['user_id'] . ')';

        $time_clause = '';
        $group_by__clause = '';
        if (!empty($params['time_slot'])) {
            //$time_clauses['today'] = " AND lead.created_at >= CURDATE() AND lead.created_at < CURDATE() + INTERVAL 1 DAY";
            $time_clauses['today'] = " AND DATE(lead.created_at) = DATE(NOW())";
            $time_clauses['yesterday'] = " AND DATE(lead.created_at) = DATE(NOW() - INTERVAL 1 DAY)";
            $time_clauses['week'] = " AND lead.created_at >= DATE(NOW()) - INTERVAL 7 DAY";
            $time_clauses['last_week'] = " AND YEARWEEK(lead.created_at) = YEARWEEK(NOW() - INTERVAL 1 WEEK)";
            $time_clauses['month'] = " AND lead.created_at >= DATE(NOW()) - INTERVAL 1 MONTH";
            $time_clauses['last_month'] = " AND year(lead.created_at) = year(NOW() - INTERVAL 1 MONTH)  AND month(lead.created_at) = Month(NOW() - INTERVAL 1 MONTH) ";
            $time_clauses['year'] = " AND lead.created_at > DATE_SUB(NOW(),INTERVAL 1 YEAR)";
            $time_clauses['last_year'] = " AND year(lead.created_at) = year(NOW() - INTERVAL 1 YEAR)";

            $group_by_clauses['today'] = " hour, day, month, year";
            $group_by_clauses['yesterday'] = " hour, day, month, year";
            $group_by_clauses['week'] = " day, month, year";
            $group_by_clauses['last_week'] = " day, month, year";
            $group_by_clauses['month'] = " day, month, year";
            $group_by_clauses['last_month'] = " day, month, year";
            $group_by_clauses['year'] = " month, year";
            $group_by_clauses['last_year'] = " month, year";

            $slot_types['today'] = " hour";
            $slot_types['yesterday'] = " hour";
            $slot_types['week'] = " day";
            $slot_types['last_week'] = " day";
            $slot_types['month'] = " day";
            $slot_types['last_month'] = " day";
            $slot_types['year'] = " month";
            $slot_types['last_year'] = " month";

            $time_clause = $time_clauses[$params['slot']];
            $group_by__clause = $group_by_clauses[$params['slot']];
        }

        $type_clause = '';
        if (!empty($params['lead_type_id'])) {
            $type_clause = ' AND lead.type_id IN (' . $params['lead_type_id'] . ')';
        }

        $status_clause = '';
        $status_qry = Status::whereIn('tenant_id', [$params['company_id']])->whereNull('deleted_at');
        if (!empty($params['status_id'])) {
            $status_qry->where('id', $params['status_id']);
            $status_clause = ' AND lead.status_id IN (' . $params['status_id'] . ')';
        }

        $status_result = $status_qry->get();

        if (empty($params['status_id'])) {
            $status_clause = ' AND lead.status_id != ' . $status_result[0]['id'];
            unset($status_result[0]);
        }


        $result = \DB::select("SELECT count(lead.id) as lead_count, status.title as status_title, status.color_code, status.code,
                    DATE_FORMAT(lead.created_at, '%b') as month, year(lead.created_at) as year, status.id as status_id, 
                    DATE_FORMAT(lead.created_at,'%d') as day, DATE_FORMAT(lead.created_at, '%a') as dayname, DATE_FORMAT(lead.created_at, '%H') as hour 
                    FROM lead left join status on status.id = lead.status_id 
                    WHERE 1 = 1 $tenant_clause $user_clause $type_clause $status_clause $time_clause 
                    GROUP BY lead.status_id order by lead.status_id");
        $response = [];
        //print_r($result);exit;
        //$fn = 'populate'.ucfirst($params['slot']).'Data';
        $fn = 'populateStatusData';

        $populated_data = self::$fn($status_result);
        $slot_type = $slot_types[$params['slot']];
        //print_r($populated_data);
        foreach ($result as $row) {
            //print_r($row);exit;
            $tmp_slot_type['year'] = $row->month;
            $tmp_slot_type['month'] = $row->day;
            $tmp_slot_type['week'] = $row->dayname;
            $tmp_slot_type['today'] = $row->hour;

            //$label = $tmp_slot_type[$params['slot']];
            $label = $row->code;

            //$tmp['label'] = $label;
            $tmp = [];
            $tmp['label'] = $row->code;
            $tmp[$row->status_title]['value'] = $row->lead_count;
            $tmp[$row->status_title]['svg'] = (object)[];
            $tmp[$row->status_title]['status_id'] = $row->status_id;
            $tmp[$row->status_title]['color_code'] = $row->color_code;
            $tmp[$row->status_title]['code'] = $row->code;

            $populated_data[$label][$row->status_title] = $tmp;
        }
        if ($params['is_web']) {
            foreach ($populated_data as $data) {
                $tmp = [];
                foreach ($data as $label_key => $row) {
                    $label = $row['label'];
                    $tmp['label'] = $label;
                    $tmp['title'] = $label_key;
                    $tmp['long_label'] = $label_key;
                    $tmp['status_id'] = $row[$label_key]['status_id'];
                    $tmp['value'] = $row[$label_key]['value'];
                    $tmp['code'] = $row[$label_key]['code'];
                    $tmp['color_code'] = $row[$label_key]['color_code'];
                }
                $response[] = $tmp;
            }
            return $response;
        }


        foreach ($populated_data as $data) {
            $tmp = [];
            foreach ($data as $row) {
                $label = $row['label'];
                $tmp['label'] = $label;
                unset($row['label']);
                foreach ($row as $key => $value)
                    $tmp[$key] = $value;

            }
            $response[] = $tmp;
        }
        return $response;
    }

    public static function getUserStatusReport($params)
    {
        $tenant_clause = ' AND user.company_id = ' . $params['company_id'];
        $commission_tenant_clause = ' AND commission_events.tenant_id = ' . $params['company_id'];
        $profit_tenant_clause = ' AND commission_events.tenant_id = ' . $params['company_id'];

        $user_clause = '';
        if (!empty($params['user_id']))
            $user_clause = ' AND user.id IN (' . $params['user_id'] . ')';

        $time_clause = '';
        $group_by__clause = '';
        if (!empty($params['time_slot'])) {
            $table_place_holder = '<!__TABLE_NAME__!>';
            $column_place_holder = '<!__COLUMN_NAME__!>';
            //$time_clauses['today'] = " AND $table_place_holder.$column_place_holder >= CURDATE() AND $table_place_holder.$column_place_holder < CURDATE() + INTERVAL 1 DAY";
            $time_clauses['today'] = " AND DATE($table_place_holder.$column_place_holder) = DATE(NOW())";
            $time_clauses['yesterday'] = " AND DATE($table_place_holder.$column_place_holder) = DATE(NOW() - INTERVAL 1 DAY)";
            $time_clauses['week'] = " AND $table_place_holder.$column_place_holder >= DATE(NOW()) - INTERVAL 7 DAY";
            $time_clauses['last_week'] = " AND YEARWEEK($table_place_holder.$column_place_holder) = YEARWEEK(NOW() - INTERVAL 1 WEEK)";
            $time_clauses['month'] = " AND $table_place_holder.$column_place_holder >= DATE(NOW()) - INTERVAL 1 MONTH";
            $time_clauses['last_month'] = " AND year($table_place_holder.$column_place_holder) = year(NOW() - INTERVAL 1 MONTH)  AND month($table_place_holder.$column_place_holder) = Month(NOW() - INTERVAL 1 MONTH) ";
            $time_clauses['year'] = " AND $table_place_holder.$column_place_holder > DATE_SUB(NOW(),INTERVAL 1 YEAR)";
            $time_clauses['last_year'] = " AND year($table_place_holder.$column_place_holder) = year(NOW() - INTERVAL 1 YEAR)";


            $time_clause = $time_clauses[$params['slot']];

            $lead_time_clause = str_replace($table_place_holder, 'lead', $time_clause);
            $lead_time_clause = str_replace($column_place_holder, 'created_at', $lead_time_clause);

            $appointment_time_clause = str_replace($table_place_holder, 'user_lead_appointment', $time_clause);
            $appointment_time_clause = str_replace($column_place_holder, 'appointment_date', $appointment_time_clause);

            $commission_time_clause = str_replace($table_place_holder, 'user_commission', $time_clause);
            $commission_time_clause = str_replace($column_place_holder, 'target_month', $commission_time_clause);
        }

        $type_clause = '';
        if (!empty($params['lead_type_id'])) {
            $type_clause = ' AND lead.type_id IN (' . $params['lead_type_id'] . ')';
        }

        $status_clause = '';
        $status_knocks_clause = '';
        $status_qry = Status::whereIn('tenant_id', [$params['company_id']])->whereNull('deleted_at');
        if (!empty($params['status_id'])) {
            $status_qry->whereIn('id', explode(',', $params['status_id']));
            $status_clause = ' AND lead.status_id IN (' . $params['status_id'] . ')';
            $status_knocks_clause = ' AND status_id IN (' . $params['status_id'] . ')';
        }

        //if (empty($params['status_id'])) {
            /*$status_result = $status_qry->get();
            $status_clause = ' AND lead.status_id != ' . $status_result[0]['id'];
            unset($status_result[0]);*/

            $status_qry->select('id');
            $status_qry->where('is_permanent', 1);
            $status_result = $status_qry->get();

            $status_ids = [];
            foreach ($status_result as $row){
                $status_ids[] = $row->id;
            }
            //$status_id = $status_result[0]['id'];
            $status_not_knocks_clause = '';
            if(!empty($status_ids))
            $status_not_knocks_clause = " AND status_id NOT IN (".implode(',', $status_ids).")";

        //}

        $lead_all_clauses = " $type_clause $status_not_knocks_clause $status_clause $lead_time_clause";
        $lead_time_knocks_clause = str_replace('lead', 'user_lead_knocks', $lead_time_clause);
        $lead_all_knocks_clauses = " $status_not_knocks_clause $status_knocks_clause $lead_time_knocks_clause";
        $lead_results = \DB::select("SELECT id FROM lead where 1=1 $type_clause $status_clause");
        $lead_id_collection = [];
        foreach ($lead_results as $row)
            $lead_id_collection[] = $row->id;

        $lead_in_clause = '';
        if(!empty($lead_id_collection))
            $lead_in_clause = ' AND lead_id IN (' . implode(',', $lead_id_collection) .') ';
        $result = \DB::select("SELECT 
                    concat(user.first_name, ' ', user.last_name) as agent_name,
                    (SELECT count(*) as lead_count from user_lead_knocks where user_lead_knocks.user_id = user.id $lead_all_knocks_clauses ) as lead_count,
                    (SELECT distinct count(*) as appointment_count FROM user_lead_appointment WHERE user_lead_appointment.user_id = user.id 
                    AND is_out_bound = 0 $appointment_time_clause $lead_in_clause) as appointment_count,
                    (SELECT sum(commission) as user_commission from user_commission where user_commission.user_id = user.id $commission_time_clause  $lead_in_clause) as commission_count,
                    (SELECT count(commission) as user_commission from user_commission LEFT JOIN commission_events ON commission_events.title = user_commission.commission_event
                    AND is_permanent = 1  $profit_tenant_clause   
                    where user_commission.user_id = user.id $commission_time_clause  AND user_commission.commission_event = 'profit'  $lead_in_clause 
                    ORDER BY commission_events.id limit 1) as commission_profit_count,
                    (SELECT count(commission) as user_commission from user_commission LEFT JOIN commission_events ON commission_events.title = user_commission.commission_event
                    AND is_permanent = 1 $commission_tenant_clause   
                    where user_commission.user_id = user.id $commission_time_clause AND user_commission.commission_event = 'contracts'  $lead_in_clause
                    ORDER BY commission_events.id desc limit 1) as commission_contract_count                    
                    FROM user            
                    WHERE 1 = 1 $tenant_clause $user_clause AND user_group_id = 2 AND user.deleted_at IS NULL 
                    order by 2 desc");

        $response = [];
        $response['lead_count'] = 0;
        $response['appointment_count'] = 0;
        $response['commission_profit_count'] = 0;
        $response['commission_contract_count'] = 0;
        foreach ($result as $row) {
            $tmp = [];
            $tmp['lead_count'] = $row->lead_count;
            $tmp['appointment_count'] = $row->appointment_count;
            $tmp['commission_count'] = (empty($row->commission_count))? 0 : $row->commission_count;
            $tmp['commission_profit_count'] = $row->commission_profit_count;
            $tmp['commission_contract_count'] = $row->commission_contract_count;
            $tmp['agent_name'] = $row->agent_name;

            $response['result'][] = $tmp;
            $response['lead_count'] += $row->lead_count;
            $response['appointment_count'] += $row->appointment_count;
            $response['commission_count'] += $row->commission_count;
            $response['commission_profit_count'] += $row->commission_profit_count;
            $response['commission_contract_count'] += $row->commission_contract_count;
        }
        return $response;
    }

    public static function leadStatusUserReport($params)
    {
        $month_clause = '';
        $status_clause = '';
        $target_user_clause = '';
        if(!empty($params['month']))
            $month_clause .= " AND lead_history.created_at LIKE '{$params['month']}%' ";
        if(!empty($params['target_user_id'])) {
            $month_clause .= " AND lead_history.assign_id IN ({$params['target_user_id']}) ";
            $target_user_clause = " AND id IN ({$params['target_user_id']}) ";
        }
        if(!empty($params['status_id'])) {
            $month_clause .= " AND lead_history.status_id IN ({$params['status_id']}) ";
            $status_clause = " AND id IN ({$params['status_id']}) ";
        }
        if(!empty($params['type_id'])) {
            $lead_type_clause = " AND lead.type_id IN ({$params['type_id']}) "; // AND lead.deleted_at IS NULL
            $lead_type_result = \DB::select("SELECT id FROM lead WHERE company_id = {$params['company_id']} $lead_type_clause");
            $lead_type_ids = [];
            foreach ($lead_type_result as $lead_type_row)
                $lead_type_ids[] = $lead_type_row->id;

            if(count($lead_type_ids))
                $month_clause .= " AND lead_history.lead_id IN (".implode(',', $lead_type_ids).") ";
        }
        if(!empty($params['start_date'])) {
            $params['start_date'] = date('Y-m-d', strtotime($params['start_date']));
            $params['end_date'] = date('Y-m-d', strtotime($params['end_date']));
            $params['end_date'] = (empty($params['end_date'])) ? $params['start_date'] : $params['end_date'];
            $month_clause .= " AND (lead_history.created_at >= '{$params['start_date']} 00:00:00' ";
                $month_clause .= " AND lead_history.created_at <= '{$params['end_date']} 23:59:59')";
        }


        /*$result = \DB::select("SELECT count(*) as user_lead_total, assignee_id, lead.status_id, concat(user.first_name, ' ', user.last_name) as name,
                              status.title as status_title FROM lead
                              INNER JOIN user ON user.id = lead.assignee_id
                              INNER JOIN status ON status.id = lead.status_id
                              INNER JOIN lead_history ON lead_history.status_id = lead.status_id
                              WHERE lead.company_id = {$params['company_id']} $month_clause
                              group by status_id,assignee_id");*/

        $result = \DB::select("SELECT count(*) as user_lead_total, lead_history.assign_id as assignee_id, lead_history.status_id, concat(user.first_name, ' ', user.last_name) as name, 
                              status.title as status_title FROM lead_history
                              INNER JOIN user ON user.id = lead_history.assign_id
                              INNER JOIN status ON status.id = lead_history.status_id                              
                              WHERE user.company_id = {$params['company_id']} $month_clause
                              group by status_id,assign_id");

        $status_result = \DB::select("SELECT id, title FROM status WHERE tenant_id = {$params['company_id']} $status_clause ORDER BY order_by");
        $user_result = \DB::select("SELECT id as assignee_id, concat(user.first_name, ' ', user.last_name) as name FROM user WHERE company_id = {$params['company_id']} $target_user_clause ");
        $response = [];
        $temp_response = [];
        $status_map = [];
        $map_user_collection = [];


        foreach ($status_result as $row) {
            $status_map[$row->id]['name'] = $row->title;
            $status_map[$row->id]['data'][$row->id] = 0;
        }

        foreach($user_result as $row) {
            $temp_response['user_names'][$row->assignee_id] = $row->name;
            $temp_response['status'][$row->assignee_id] = $status_map;
        }

        foreach($result as $row){
            $temp_response['status'][$row->assignee_id][$row->status_id]['data'][$row->status_id] = $row->user_lead_total;
        }

        $status_response = [];
        foreach ($temp_response['status'] as $ass_user_id => $user_row){
            foreach ($user_row as $status_id => $status_row) {
                $status_response[$status_id]['name'] = $status_row['name'];
                $status_response[$status_id]['data'][] = $status_row['data'][$status_id];
            }
        }

        foreach($temp_response['user_names'] as $row)
            $response['user_names'][] = $row;
        
        $s_no = 1;
        foreach($status_response as $row){
            $response['status'][] = $row;
            $response['export'][] = array_merge([$s_no++, $row['name']],$row['data']);
        }

        return $response;
    }

    public static function populateStatusData($status_result)
    {
        $response = [];

        foreach ($status_result as $status) {
            $tmp = [];
            $tmp['label'] = $status->code;
            $tmp[$status->title]['value'] = 0;
            $tmp[$status->title]['svg'] = (object)[];
            $tmp[$status->title]['status_id'] = $status->id;
            $tmp[$status->title]['color_code'] = $status->color_code;
            $tmp[$status->title]['code'] = $status->code;

            $response[$tmp['label']][$status->title] = $tmp;
        }

        return $response;
    }

    public static function populateYearData($status_result)
    {
        $months = Helper::getYearMonthsFromToday();
        $response = [];
        foreach ($status_result as $row) {
            foreach ($months as $month) {
                $tmp = [];
                $tmp['label'] = $month;
                $tmp[$row->title]['value'] = 0;
                $tmp[$row->title]['svg'] = (object)[];
                $tmp[$row->title]['status_id'] = $row->id;
                $tmp[$row->title]['color_code'] = $row->color_code;

                $response[$month][$row->title] = $tmp;
            }
        }
        return $response;
    }

    public static function populateMonthData($status_result)
    {
        //$months = Helper::getWeekDaysFromToday();
        $months = Helper::getMonthDaysFromToday();
        $response = [];
        foreach ($status_result as $row) {
            foreach ($months as $month) {
                $tmp = [];
                $tmp['label'] = $month;
                $tmp[$row->title]['value'] = 0;
                $tmp[$row->title]['status_id'] = $row->id;
                $tmp[$row->title]['color_code'] = $row->color_code;

                $response[$month][$row->title] = $tmp;
            }
        }
        return $response;
    }

    public static function populateWeekData($status_result)
    {
        $months = Helper::getWeekDaysFromToday();

        $response = [];
        foreach ($status_result as $row) {
            foreach ($months as $month) {
                $tmp = [];
                $tmp['label'] = $month;
                $tmp[$row->title]['value'] = 0;
                $tmp[$row->title]['status_id'] = $row->id;
                $tmp[$row->title]['color_code'] = $row->color_code;

                $response[$month][$row->title] = $tmp;
            }
        }
        return $response;
    }

    public static function populateTodayData($status_result)
    {
        $months = Helper::getHoursFromToday();

        $response = [];
        foreach ($status_result as $row) {
            foreach ($months as $month) {
                $tmp = [];
                $tmp['label'] = $month;
                $tmp[$row->title]['value'] = 0;
                $tmp[$row->title]['status_id'] = $row->id;
                $tmp[$row->title]['color_code'] = $row->color_code;

                $response[$month][$row->title] = $tmp;
            }
        }
        return $response;
    }


    public static function getLeadWCustomField($params)
    {

//        echo "<pre>";
//        print_r($params);
//        die;

        $lat = $params['latitude'];
        $lng = $params['longitude'];
        $radius = $params['radius'];

        $haversine = "(3959 * acos (
                    cos ( radians($lat) )
                    * cos( radians(`latitude`) )
                    * cos( radians(`longitude`) - radians($lng) )
                    + sin ( radians($lat) )
                    * sin( radians(`latitude`) )
                ))";

        $query = self::select('lcf.*','lead.*',\DB::raw('type.title as lead_type'),\DB::raw('status.title as lead_status'));
        $query->leftJoin('type','type.id','lead.type_id');
        $query->leftJoin('status','status.id','lead.status_id');
        $query->leftJoin('lead_custom_field AS lcf', 'lcf.lead_id', '=', 'lead.id');
        $query->where('lead.company_id', $params['company_id']);
        $query->whereNull('lead.deleted_at');

        if (!empty($params['lead_type_id'])) {
            $query->whereRaw('lead.type_id  IN (' . $params['lead_type_id'] . ')');
        }


        if (isset($params['lead_ids']) && !empty($params['lead_ids']) && !empty(implode(',', $params['lead_ids'])))
            $query->whereRaw('lead.id  IN (' . implode(',', $params['lead_ids']) . ')');

        if (isset($params['user_ids']) && !empty($params['user_ids']))
            $query->whereRaw('lead.assignee_id  IN (' . $params['user_ids'] . ')');

        if (isset($params['status_ids']) && !empty($params['status_ids']))
            $query->whereRaw('lead.status_id  IN (' . $params['status_ids'] . ')');

        if (isset($params['start_date']) && isset($params['end_date']) && !empty($params['start_date']) && !empty($params['end_date'])) {
            $params['start_date'] = date('Y-m-d', strtotime($params['start_date']));
            $params['end_date'] = date('Y-m-d', strtotime($params['end_date']));
            $query->whereRaw("lead.created_at >= '{$params['start_date']} 00:00:00' && lead.created_at <= '{$params['end_date']} 23:59:59'");
        }

        if (!empty($params['lead_type_id'])) {
            $query->whereRaw('lead.type_id  IN (' . $params['lead_type_id'] . ')');
        }

        if (isset($params['search']) && !empty($params['search'])) {
            $query->leftJoin('type', 'type.id', 'lead.type_id');
            $query->whereRaw("(lead.title like '%{$params['search']}%' OR formatted_address like '%{$params['search']}%' 
             OR address like '%{$params['search']}%' OR type.title like '%{$params['search']}%' )");
        }

        if(!isset($params['order_by'])){
            $params['order_by'] = 'id';
            $params['order_type'] = 'desc';
        }
        $order_by = 'lead.'.$params['order_by'];
        if(strtolower($params['order_by']) == 'lead_type')
            $order_by = 'type.title';

        if(strtolower($params['order_by']) == 'first_name' || strtolower($params['order_by']) == 'last_name')
            $order_by = 'lead.owner';

        $query->orderBy($order_by, $params['order_type']);

        if (!empty($params['time_slot'])) {
            //$time_clauses['today'] = " AND lead.created_at >= CURDATE() AND lead.created_at < CURDATE() + INTERVAL 1 DAY";
            $time_clauses['today'] = " DATE(lead.created_at) = DATE(NOW())";
            $time_clauses['yesterday'] = " DATE(lead.created_at) = DATE(NOW() - INTERVAL 1 DAY)";
            $time_clauses['week'] = " lead.created_at >= DATE(NOW()) - INTERVAL 7 DAY";
            $time_clauses['last_week'] = " YEARWEEK(lead.created_at) = YEARWEEK(NOW() - INTERVAL 1 WEEK)";
            $time_clauses['month'] = " lead.created_at >= DATE(NOW()) - INTERVAL 1 MONTH";
            $time_clauses['last_month'] = " year(lead.created_at) = year(NOW() - INTERVAL 1 MONTH)  AND month(lead.created_at) = Month(NOW() - INTERVAL 1 MONTH) ";
            $time_clauses['year'] = " lead.created_at > DATE_SUB(NOW(),INTERVAL 1 YEAR)";
            $time_clauses['last_year'] = " year(lead.created_at) = year(NOW() - INTERVAL 1 YEAR)";

            $group_by_clauses['today'] = " hour, day, month, year";
            $group_by_clauses['yesterday'] = " hour, day, month, year";
            $group_by_clauses['week'] = " day, month, year";
            $group_by_clauses['last_week'] = " day, month, year";
            $group_by_clauses['month'] = " day, month, year";
            $group_by_clauses['last_month'] = " day, month, year";
            $group_by_clauses['year'] = " month, year";
            $group_by_clauses['last_year'] = " month, year";

            $slot_types['today'] = " hour";
            $slot_types['yesterday'] = " hour";
            $slot_types['week'] = " day";
            $slot_types['last_week'] = " day";
            $slot_types['month'] = " day";
            $slot_types['last_month'] = " day";
            $slot_types['year'] = " month";
            $slot_types['last_year'] = " month";

            $time_clause = $time_clauses[$params['slot']];
            $group_by__clause = $group_by_clauses[$params['slot']];

            $query->whereRaw("$time_clause");
        }

        if (!empty($params['latitude']) && !empty($params['longitude'])) {
            $query->selectRaw("{$haversine} AS distance")->whereRaw("{$haversine} < ?", [$radius]);
        }

        //$query->groupBy('lead.id');
        //return $query->get();
        $response = [];
        //$lead_field_data = $query->get()->toArray();
        $lead_field_data = $query->paginate(Config::get('constants.EXPORT_PAGE_SIZE')*2);
        $tmp_fields = [];
        $field_columns = [];
        foreach($lead_field_data as $field_data){
            $field_data = json_decode(json_encode($field_data),true);
            $tmp = $field_data;

            $tmp_fields[$field_data['id']][$field_data['key']] = $field_data['value'];

            $tmp = array_merge($tmp,$tmp_fields[$field_data['id']]);
            $response[$field_data['id']] = $tmp;
            foreach ($tmp as $field => $data){
                $field_columns[$field] = $field;
            }

        }
        $response_data['data'] = $response;
        $response_data['field_columns'] = $field_columns;
        return $response_data;
    }

}
