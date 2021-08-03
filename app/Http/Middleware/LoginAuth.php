<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Controller;
use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Lang;

class LoginAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $obj = new Controller();
        $obj->call_mode; // call mode for admin ..
        if(in_array($obj->call_mode,['admin', 'web'])){
            if(isset($request['draw'])){
                $request['page'] = ($request['start'] / 10 ) + 1;    
            }

            //$request->session()->forget('user');
            //$request->session()->flush();

            if($request->session()->exists('user')) {
                $user_session = $request->session()->get('user');
                //print_r($user_session);exit;
                $request['user_id'] = $user_session->id;
                $request['company_id'] = $user_session->company_id;
                $request['session_user_group_id'] = $user_session->user_group_id;
                $request['call_mode'] = $obj->call_mode;

                return $next($request);
            }
            return redirect('/tenant/login');
        }



        if(!($result = User::auth($request->header('user-token')))){
            $code = 404;
            $response = [
                'code' => $code,
                //    'success' => false,
                'message' => Lang::get('passwords.user_token'),
                'data' => [['auth' => Lang::get('passwords.user_token')]],
            ];
            return response()->json($response, $code);
        }
        $request['user_id'] = $result->id;
        $request['company_id'] = $result->company_id;
        $request['session_user_group_id'] = $result->user_group_id;
        $request['call_mode'] = 'api';
        return $next($request);
    }
}
