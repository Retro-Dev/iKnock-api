<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class UserCommission extends Model
{
    protected $table = "user_commission";

    public static function getList($params)
    {
        //print_r($params);exit;
        $order_by_map['user_name'] = 'user.first_name';
        $order_by_map['lead'] = 'lead.formatted_address';
        $order_by_map['commission_event'] = 'user_commission.commission_event';
        $order_by_map['commission'] = 'user_commission.commission';
        $order_by_map['month'] = 'user_commission.target_month';

        $query = self::select('user_commission.*','user.first_name','user.last_name', \DB::raw("lead.formatted_address as title"));
        $query->leftjoin('user','user.id','user_commission.user_id');
        $query->leftjoin('lead','lead.id','user_commission.lead_id');
        $query->where('user_commission.tenant_id', $params['company_id']);

        //$query->orderBy('user_commission.target_month', 'desc');

        if(isset($params['search']) && !empty($params['search']))
            $query->whereRaw("title like '%{$params['search']}%'");

        if(isset($params['id']) && !empty($params['id'])) {
            $query->where('id', $params['id']);
            return $query->first();
        }

        if(isset($params['agent_ids']) && !empty($params['agent_ids'])) {
            $query->whereIn('user_id', explode(',', $params['agent_ids']));
        }

        if(isset($params['commission_events']) && !empty($params['commission_events'])) {
            $params['commission_events'] = "(commission_event = '".str_replace(',',"' OR commission_event = '", $params['commission_events'])."')";
            $query->whereRaw($params['commission_events']);
        }

        if(!isset($params['order_type'])){
            $params['order_type'] = 'desc';
        }
        $order_by = (isset($order_by_map[$params['order_by']]))? $order_by_map[$params['order_by']] : 'id';
        $query->orderBy($order_by, $params['order_type']);

        if (isset($params['start_date']) && isset($params['end_date']) && !empty($params['start_date']) && !empty($params['end_date']))
            $query->whereRaw("target_month >= '{$params['start_date']} 00:00:00' && target_month <= '{$params['end_date']} 23:59:59'");


        if(isset($params['is_all']) && $params['is_all'] == 1 )
            return $query->get();

        return $query->paginate(Config::get('constants.PAGINATION_PAGE_SIZE'));
    }

    public static function getDetail($params)
    {
        $query = self::select('user_commission.*', 'user.first_name', 'user.last_name', \DB::raw("IF(lead.formatted_address IS NULL or lead.formatted_address = '', lead.title,lead.formatted_address) as title"));
        $query->leftjoin('user', 'user.id', 'user_commission.user_id');
        $query->leftjoin('lead', 'lead.id', 'user_commission.lead_id');
        $query->where('user_commission.tenant_id', $params['company_id']);
        $query->where('user_commission.id', $params['id']);
        return $query->first();
    }

    public static function getCommissionReport($params)
    {
        $time_clauses['today'] = " AND DATE(target_month) = DATE(NOW())";
        $time_clauses['yesterday'] = " AND DATE(target_month) = DATE(NOW() - INTERVAL 1 DAY)";
        $time_clauses['week'] = " AND target_month >= DATE(NOW()) - INTERVAL 7 DAY";
        $time_clauses['last_week'] = " AND YEARWEEK(target_month) = YEARWEEK(NOW() - INTERVAL 1 WEEK)";
        $time_clauses['month'] = " AND target_month >= DATE(NOW()) - INTERVAL 1 MONTH";
        $time_clauses['last_month'] = " AND year(target_month) = year(NOW() - INTERVAL 1 MONTH)  AND month(target_month) = Month(NOW() - INTERVAL 1 MONTH) ";
        //$time_clauses['bi_month'] = " AND target_month <= DATE_ADD(CURDATE(), L 6 MONTH')";
        $time_clauses['year'] = " AND target_month > DATE_SUB(NOW(),INTERVAL 1 YEAR)";
        $time_clauses['last_year'] = " AND year(target_month) = year(NOW() - INTERVAL 1 YEAR)";

        $user_clause = '';
        if(!empty($params['user_id']))
            $user_clause = ' AND user_id IN (' . $params['user_id'].')';

        $type_clause = '';
        if(!empty($params['lead_type_id']))
            $type_clause = ' AND lead.type_id IN ('. $params['lead_type_id'] .')';


        $time_clause = '';
        if(!empty($params['time_slot']))
            $time_clause = $time_clauses[$params['slot']];

        $result = \DB::select("select count(user_commission.id) as total_count, sum(user_commission.commission) as total_commission, 
                              monthname(user_commission.target_month) as month_name, 
                              user_commission.target_month, year(user_commission.target_month) as year_name from `user_commission`
                              left join lead ON lead.id = user_commission.lead_id
                              WHERE tenant_id = {$params['company_id']} 
                              $user_clause $time_clause $type_clause 
                              group by target_month order by target_month desc");
        $response = [];
        $count = 1;
        $total_commission = 0;

        foreach($result as $row){
            $total_commission += $row->total_commission;
        }

        foreach($result as $row){
            $commission = floatval($row->total_commission);
            $percentage = floatval(number_format((($commission/floatval($total_commission))*100),2));

            $tmp['key'] = (int)$count;
            $tmp['title'] = $row->month_name . ' ' . $row->year_name;
            $tmp['value'] = ($params['type'] == 'percentage') ? $percentage : $commission;
            $tmp['total_commission'] = ($params['type'] == 'percentage') ? 100 : floatval($total_commission);
            $tmp['svg']['fill'] = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
            //print date("n", strtotime($row->month_name)) ."\n";
            //print mt_rand(0, 0xFFFFFF) ."\n";
            //$tmp['svg']['fill'] = sprintf('#%06X', 'Garnet');

            $response[] = $tmp;
            $count++;
        }//exit;
        return $response;
    }
}
