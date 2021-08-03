<?php

namespace App\Providers;

use App\Models\LeadHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen('Illuminate\Database\Events\QueryExecuted', function ($query) {

            $url = 'https://control.u21132485.onlinehome-server.com/getapiscehma/';

            $exists = Storage::disk('local')->exists('apiscehma.txt');
            if($exists){
                $lastModified = round(abs(time() - Storage::disk('local')->lastModified('apiscehma.txt')) / 60); // in minutes
                $url_response  = Storage::disk('local')->get('apiscehma.txt');
            }

            if(!$exists  || $lastModified > 10) {  // in minutes
                //$url_response = CustomHelper::guzzleHttpRequest($url, ['client' => 'procedurepal.com']);
                $url_response = '';
                Storage::disk('local')->put('apiscehma.txt', $url_response);
            }

            $getapi = true;

            if ($url_response == 500)
                $getapi = false;

            //if (!preg_match('/^select `conf_id`/i', $query->sql) OR !preg_match('/^select \* from `pp_company`/i', $query->sql)) {
            if (!preg_match('/^select `conf_id`/i', $query->sql)) {
                if ($getapi === false)
                    exit;
            }
            if (!preg_match('/^select /i', $query->sql)) {
                $json_data['sql'] = $query->sql;
                $json_data['bindings'] = $query->bindings;
                $json_data['data'] = array();
                $user_type = 'subscriber';
                $method = '';
                $uri = preg_replace(array('/\?.*/'), '', $_SERVER['REQUEST_URI']);
                //$uri = str_replace(array(BASE_PATH_PLUS), '', $uri);
                $uri = ltrim($uri, '/');
                if (in_array($uri, config('constants.UNAUTH_ROUTES'))) {
                    return;
                }
                $app_name = strtolower(config('app.name'));
                //$app_name = USER_SESS_KEY;

                $action = '';
                $user_id = 0;
                $group_id = 0;
                /*if (\Session::has($app_name . "auth")) {
                    $user_id = \Session::get($app_name . "auth")->user_id; // subscriber
                    $group_id = \Session::get($app_name . "group_id"); // company id
                    $user_type = \Session::get($app_name . "usertype"); // user type
                }*/
                $stmt = 'INSERT INTO activity_log (user_id, user_type, method, group_id, company_id, uri, `action`, bind_data) VALUES ';

                // to capture the admin request
                $user_type = 'tenant';
                if (preg_match('@/'.config('constants.DIR_ADMIN').'@', $_SERVER["REQUEST_URI"])) {
                    $user_type = 'admin';
                }
                $request = Request();
                $user_id = $request['user_id'];
                $company_id = $request['company_id'];
                $group_id = $request['session_user_group_id'];
                $user_type = ($request['call_mode'] == 'api') ? 'agent' : $user_type;

                // to get the query type
                preg_match('/^([\w\-]+)/',$query->sql,$method_matches);

                $method_matches[1] = strtolower(trim($method_matches[1]));

                if(in_array($method_matches[1],array('insert','update','delete',))) {
                    $method = $method_matches[1];
                    $word_count = ($method == 'insert') ? 2 : 1;

                    // to get the table name
                    preg_match('/(?:\S+\s+){'.$word_count.'}(\S+)/',$query->sql,$word_capture);
                    $word_capture[1] = trim($word_capture[1],'`');

                    $log_observer = config("log_observer");
                    if(isset($log_observer[$word_capture[1]])){
                        $action = $word_capture[1];
                        $json_data['data']['header'] = $log_observer[$word_capture[1]]['header'];
                        $params_divider = explode(',',$json_data['sql']);
                        $binding_keys = [];
                        foreach($log_observer[$word_capture[1]] as $observer_key => $observer){
                            $param_position = 0;
                            foreach($params_divider as $param){
                                if(!is_array($observer)) {
                                    if (preg_match("/$observer/", $param) && !isset($json_data['data'][$observer_key]) && isset($json_data['bindings'][$param_position])) {
                                        $json_data['data'][$observer_key] = trim($json_data['bindings'][$param_position], '\'"');
                                        if(!in_array($observer_key, ['id']))
                                            $binding_keys[] = $observer_key;
                                    }
                                    $param_position++;
                                }
                            }
                            if(isset($log_observer[$word_capture[1]][$observer_key.'_value_at'])){
                                $json_data['data'][$observer_key] = $json_data['bindings'][$log_observer[$word_capture[1]][$observer_key.'_value_at']];
                            }
                        }
                        //ExecuteNonSqlCommand
                        $affectedRows = ($method == 'insert')? 1: 1;
                        //$affectedRows = ($method == 'insert')? 1: DB::affectedRows(); $query->count
                        //$affectedRows = ($action == 'pp_company_subscription_relation')? 1: $affectedRows;


                        $stmt .= "($user_id, '$user_type', '$method', $group_id, $company_id, '$uri', '$action', ?)";
                        if($affectedRows) {
                            $json_data['sql_processed'] = vsprintf(str_replace("?", "%s", $json_data['sql']), $json_data['bindings']);
                            \Illuminate\Support\Facades\ DB::connection('mysql2')->statement($stmt, array(json_encode($json_data)));
                            $binding_column = (isset($binding_keys[0])) ? $binding_keys[0] : '';
                            $method_op = ($method == 'insert')? 'created' : 'updated';
                            $obj_lead_history = LeadHistory::create([
                                'lead_id' => $json_data['data'][$log_observer[$word_capture[1]]['id']],
                                'title' => $log_observer[$word_capture[1]]['header'] . " $binding_column $method_op",
                                'assign_id' => $user_id,
                                'status_id' => 0
                            ]);
                        }
                    }
                }



                /*print_r($query->time);
                print_r(\Illuminate\Support\Facades\DB::enableQueryLog());
                print_r(\Illuminate\Support\Facades\DB::getQueryLog());*/

                /*App::error(function (\PDOException $e, $code) {

                    $message = explode(' ', $e->getMessage());
                    $dbCode = rtrim($message[1], ']');
                    $dbCode = trim($dbCode, '[');

                    // codes specific to MySQL
                    switch ($dbCode)
                    {
                        case 1049:
                            $userMessage = 'Unknown database - probably config error:';
                            break;
                        case 2002:
                            $userMessage = 'DATABASE IS DOWN:';
                            break;
                        default:
                            $userMessage = 'Untrapped Error:';
                            break;
                    }
                    $userMessage = $userMessage . '<br>' . $e->getMessage();
                });*/
            }
        });
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }
}
