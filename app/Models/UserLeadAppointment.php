<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class UserLeadAppointment extends Model
{
    protected $table = "user_lead_appointment";

    public static function getList($params)
    {
//        print_r($params);
// die;
        $query = self::select('lead.*','user_lead_appointment.appointment_date','user_lead_appointment.appointment_end_date','user_lead_appointment.result as appointment_result','user_lead_appointment.id as appointment_id','user_lead_appointment.is_out_bound');
        $query->leftJoin('lead', function($join) use ($params){
            $join->on('lead.id', 'user_lead_appointment.lead_id');
            if(!empty($params['company_id']) && !$params['ignore_out_bound'])
                $join->where('lead.company_id', $params['company_id']);
        });
        $query->Join('user','user.id','user_lead_appointment.user_id');
        $query->where('user.company_id', $params['company_id']);
        $query->whereNull('user_lead_appointment.deleted_at');

        if(!empty($params['user_id']) && !$params['ignore_out_bound'])
            $query->where('user_lead_appointment.user_id', $params['user_id']);

        if(empty($params['userIds']) && $params['ignore_out_bound']){
            $param['company_id'] = $params['company_id'];
            $param['is_all'] = true;

            $agents_collection = User::getTenantUserList($param);
            $agents = [];
            foreach($agents_collection as $row){
                $agents[] = $row->id;
            }
            $params['userIds'] = implode(',', $agents);
        }

        if(!empty($params['userIds'])) {
            //$qry_outbound_lead_clause = ($params['is_out_bound'] === 1) ? ' OR user_lead_appointment.is_out_bound = 1 ' : '';
            $qry_outbound_lead_clause = ' OR user_lead_appointment.is_out_bound = 1 ';
            $query->whereRaw('(user_lead_appointment.user_id IN (' . $params['userIds'] . ") $qry_outbound_lead_clause)");
        }

        if(!empty($params['company_id']) && empty($params['userIds'])  && $params['ignore_out_bound']) //  && !$params['ignore_out_bound']
            $query->where('lead.company_id', $params['company_id']);

//        $query->where('user_lead_appointment.user_id', $params['user_id']);
        //$query->where('user_lead_appointment.type', $params['type']);
        if(($params['is_out_bound'] === 0 || $params['is_out_bound'] === 1) && !$params['ignore_out_bound'])
            $query->where('user_lead_appointment.is_out_bound', $params['is_out_bound']);

        if(!empty($params['appointment_date'])) {
            $query->whereRaw("((user_lead_appointment.appointment_date like '{$params['appointment_date']}%' OR user_lead_appointment.appointment_end_date like '{$params['appointment_date']}%') OR
            ('{$params['appointment_date']}-01 00:00:00' BETWEEN user_lead_appointment.appointment_date AND user_lead_appointment.appointment_end_date))");
        }

        if(isset($params['appointment_start_date']) && isset($params['appointment_end_date']))
            $query->whereRaw("(user_lead_appointment.appointment_date >= '{$params['appointment_start_date']}' AND user_lead_appointment.appointment_date <= '{$params['appointment_end_date']}')");

        $query->with('tenantQuery');
        $query->with('leadCustom');
        $query->with('leadStatus');
        $query->with('leadType');
        $query->with('leadMedia');

        $query->orderBy('user_lead_appointment.appointment_date', 'asc');

        if(isset($params['search']) && !empty($params['search']))
            $query->whereRaw("title like '%{$params['search']}%'");

        if($params['API'] || $params['ignore_out_bound'])
            return $query->paginate(Config::get('constants.PAGINATION_PAGE_SIZE'));

        if(!empty($params['appointment_date'])  || $params['ignore_out_bound'])
            return $query->get();

        return $query->paginate(Config::get('constants.PAGINATION_PAGE_SIZE'));
    }


    public function leadQuery()
    {
        return self::hasMany('App\Models\LeadQuery','lead_id');
    }

    public function tenantQuery()
    {
        return self::hasMany('App\Models\TenantQuery','tenant_id','company_id')
            /*->leftjoin('lead_query',function ($join){

                $join->on('lead_query.query_id','=','tenant_query.id')
                    ->where('lead_query.lead_id', 'lead.id');
                //$join->on(\DB::raw('( lead_query.query_id = tenant_query.id AND lead_query.lead_id = lead.id )'));
                //'lead_query.id',

                //$join->on(DB::raw('(  bookings.arrival between ? and ? OR bookings.departure between ? and ? )'), DB::raw(''), DB::raw(''));
            })*/
            /*->select(['tenant_query.tenant_id','tenant_query.type','tenant_query.query', 'lead_query.created_at',
                'lead_query.lead_id','lead_query.response'])*/;
    }

    public function leadMedia()
    {
        return self::hasMany('App\Models\Media', 'source_id')
            ->where('source_type', 'lead');
    }

    public function leadCustom()
    {
        return self::hasMany('App\Models\LeadCustomField','lead_id', 'id')
            ->select(['id', 'lead_id', 'key', 'value']);
    }

    public function leadStatus()
    {
        return self::hasOne('App\Models\Status','id', 'status_id');
    }

    public function leadType()
    {
        return self::hasOne('App\Models\Type','id', 'type_id');
    }
    
    public static function bulkInsertion($params)
    {
        $out_bound_appointments = [];   
        $notes = [];
        if(isset($params['slot_type']) && $params['slot_type'] == 'leads'){
            foreach ($params['lead_id'] as $target_id) {
                $out_bound_appointments[] = "($target_id,{$params['user_id']}, '{$params['start_date']}', '{$params['end_date']}', 1, 'lead', ?, NOW())";
                $notes[] = $params['note'];
            }
        }else {
            foreach ($params['target_user_id'] as $target_id) {
                $out_bound_appointments[] = "(0,{$target_id}, '{$params['start_date']}', '{$params['end_date']}', 1, 'lead', ?, NOW())";
                $notes[] = $params['note'];
            }
        }

        if (count($out_bound_appointments))
                \DB::statement("INSERT INTO user_lead_appointment (lead_id, user_id, appointment_date, appointment_end_date, is_out_bound, type, result, created_at) VALUES " . implode(',', $out_bound_appointments), $notes);
    }

    public static function destroyByLeadId($lead_ids)
    {
        \DB::statement("UPDATE user_lead_appointment set deleted_at = NOW() WHERE lead_id IN ($lead_ids)");
    }

    public static function getById($params)
    {
        $result = \DB::select("SELECT user_lead_appointment.*, concat(user.first_name, '', user.last_name) as user_name, lead.title FROM user_lead_appointment 
                              LEFT JOIN `user` ON `user`.id = user_lead_appointment.user_id
                              LEFT JOIN lead ON lead.id = user_lead_appointment.lead_id 
                              WHERE user_lead_appointment.id = {$params['id']}
                               AND `user`.company_id = {$params['company_id']}");
        return (isset($result[0])) ? $result[0] : [];
    }
}
