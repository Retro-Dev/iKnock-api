<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use DB;

class LeadHistory extends Model
{
    protected $connection= 'mysql2';
    protected $table = "lead_history";
    //public $timestamps = false;
    const UPDATED_AT = null;

    public static function create($history)
    {

        if($history['status_id'] > 0 && empty($history['title']))
            $history['title'] = (!isset($history['lead_status_title']))? 'Lead status updated' : $history['lead_status_title'];

        $obj = new static();
        $obj->lead_id   = $history['lead_id'];
        $obj->assign_id = $history['assign_id'];
        $obj->title     = $history['title'];
        $obj->status_id = $history['status_id'];

        $obj->save();

        return $obj->id;
    }

    public static function getList($params)
    {
        //$query = DB::table('lead_history');
        $query = self::select('lead.id', 'lead.title', 'lead.owner', 'lead.address', 'lead.zip_code', 'lead.city', 'lead.creator_id', 'lead_history.status_id', 'lead.type_id',
            'lead_history.assign_id',DB::raw("concat(user.first_name,' ', user.last_name) as name")
            ,DB::raw("(IF(lead_history.status_id = 0, lead_history.title, concat(status.title, ' status updated'))) as lead_history_title")
            , 'lead_history.created_at');
        $query->leftJoin('lead', 'lead.id', 'lead_history.lead_id');
        $query->leftJoin('status', 'status.id', 'lead_history.status_id');
        $query->leftJoin('user', 'user.id', 'lead_history.assign_id');

        if(isset($params['lead_id']) && !empty($params['lead_id']))
            $query->where('lead_id', $params['lead_id']);

        if(isset($params['lead_ids']) && !empty($params['lead_ids']))
            $query->whereIn('lead_id', $params['lead_ids']);

        if(isset($params['search']) && !empty($params['search']))
            $query->whereRaw("lead.title like '%{$params['search']}%'");

        $query->with('leadStatus');
        $query->with('leadType');
        $query->with('leadMedia');

        if ($params['is_lead_export'] === 'true') {
            $query->where('lead_history.status_id', '!=', 0);
            $query->where('lead_history.assign_id', '!=', 0);
            $query->whereNotNull('lead_history.lead_id');
            $query->orderBy('lead.title');
            return $query->get();
        }
        $query->orderBy('lead_history.created_at', 'desc');
        return $query->paginate(Config::get('constants.PAGINATION_PAGE_SIZE'));
    }

    public static function getLastHistoryByLeadId($params)
    {
        //$query = DB::table('lead_history');
        $query = self::select('user.*');
        $query->join('user', 'user.id', 'lead_history.assign_id');
        $query->where('lead_id', $params['lead_id']);

        $query->orderBy('lead_history.created_at', 'desc');

        if(isset($params['search']) && !empty($params['search']))
            $query->whereRaw("lead.title like '%{$params['search']}%'");

        return $query->first();
    }

    public function leadStatus()
    {
        return self::hasOne('App\Models\Status','id', 'status_id');
    }

    public function leadType()
    {
        return self::hasOne('App\Models\Type','id', 'type_id');
    }

    public function leadMedia()
    {
        return self::hasMany('App\Models\Media', 'source_id')
            ->where('source_type', 'lead');
    }
}
