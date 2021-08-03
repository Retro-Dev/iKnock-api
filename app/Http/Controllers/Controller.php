<?php

namespace App\Http\Controllers;

use App\Http\Middleware\ApiAuth;
use App\Http\Middleware\LoginAuth;
use App\Libraries\Helper;
use App\Libraries\readXlsx;
use App\Models\MailTemplate;
use App\Models\Setting;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Request;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public
        $__params       = [],
        $__is_ajax      = false,
        $__is_api_call  = false,
        $__is_redirect  = false,
        $__is_error     = false,
        $__is_paginate  = true, // to control pagination object
        $__is_collection= true, // for item detail response
        $__collection   = true, // to control general response
        $__module       = '',
        $__view         = '';

    public
        $call_mode      = 'admin';    // api, admin, web

    function __construct(){
        //echo \Route::getCurrentRoute()->getActionName();

        $this->_callSetup();
    }

    private function _callSetup()
    {
        $this->__call_mode   = 'web';
        if (preg_match('#/api/#', \Request::url())) {

            $this->__is_api_call = true;
            $this->call_mode   = 'api';
        }

        if (preg_match('/admin/', \Request::url())) {
            $this->call_mode   = 'admin';
        }

        if($this->__is_api_call) {
            $this->middleware(ApiAuth::class);
        }
    }

    protected function __validateRequestParams($input_params, $param_rules, $param_rules_messages = [])
    {
        $this->__params = $input_params;
        $this->__customMessages = [];
        if(!empty($param_rules_messages))
            $this->__customMessages = $param_rules_messages;

        $validator = \Validator::make($input_params, $param_rules, $this->__customMessages);

        $errors = [];

        if($validator->fails()){
            foreach ($param_rules as $field => $value){
                $message = $validator->errors()->first($field);
                if(!empty($message)) {
                    $errors[$field] = $message;
                }
            }
            $this->__is_error = true;

            if($this->__is_api_call)
                return $this->__sendError('Validation Error.', $errors);

            if($this->__is_ajax)
                return $this->__sendError('Validation Error.', $errors);

            if($this->__is_redirect) {
                return $this->__sendError('Validation Error.', $errors);

                return redirect(\URL::to($this->__module . $this->__view));
            }

            return View::make($this->__module.$this->__view, ['error' => $this->__sendError('Validation Error.', $errors), 'page' => $this->__view]);
            print_r($errors);exit;
            return $errors;
        }
        return $response = [
            'code' => 200,
            'success' => true,
            'message' => 'success',
        ];
    }

    protected function __sendResponse($resource, $obj_model, $response_code, $message, $json_numeric_check = false)
    {
        $page_info = $this->__getPaginate($obj_model);

        $resource = "\App\Http\Resources\\$resource";
        if($this->__collection && $this->__is_collection) {
            $result = $resource::collection($obj_model);
            // when data record set is empty
            if($this->__collection && $result->isEmpty()){
                $message = 'No data found.';
                $response_code = 204;
            }
        }

        if($this->__collection && !$this->__is_collection) {
            $result = new $resource($obj_model);
            // when data record set is empty
            if ($this->__collection && !$result->exists) {
                $message = 'No data found.';
                $response_code = 204;
            }
        }

        if(!$this->__collection) {
            $result = $obj_model;
        }

        $response = [
            'code'    => $response_code,
            //'success' => true,
            'data'    => ($this->__collection)? $result : $obj_model,
            'message' => $message,
            'links' => $page_info['links'],
            'meta' => $page_info['meta'],
            'draw' => !empty(\Request::input('draw')) ? ( \Request::input('draw') + 1 ) :0,
            'recordsTotal' => $page_info['meta']['total'],
            'recordsFiltered' => $page_info['meta']['total'],
        ];

        if($this->__is_api_call && $json_numeric_check)
            return response()->json($response, 200, [], JSON_NUMERIC_CHECK);

        if($this->__is_api_call)
            return response()->json($response, 200);

        if($this->__is_ajax && $json_numeric_check)
            return response()->json($response, 200, [], JSON_NUMERIC_CHECK);

        if($this->__is_ajax)
            return response()->json($response, 200);

        if($this->__is_redirect)
            return redirect(\URL::to($this->__module.$this->__view));

        $data = isset($result->collection)? json_decode($result->collection): $result;
        return View::make($this->__module.$this->__view, ['data' => $data, 'page' => $this->__view]);

        //print 'html response';
        //exit;
    }

    protected function __getPaginate($obj_model)
    {
        if(!$this->__is_paginate){
            $response['links'] = [
                "first" => null,
                "last" => null,
                "prev" =>  null,
                "next" =>  null
            ];

            $response['meta'] = [
                "current_page" =>  1,
                "from" =>  1,
                "last_page" =>  0,
                "to" =>  0,
                "total" =>  is_object($obj_model) ? $obj_model->count() : count($obj_model)
            ];

            return $response;
        }

        $response['links'] = [
            "first" => $obj_model->url($obj_model->firstItem()),
            "last" => $obj_model->url($obj_model->lastPage()),
            "prev" =>  $obj_model->previousPageUrl(),
            "next" =>  $obj_model->nextPageUrl()
        ];

        $response['meta'] = [
            "current_page" =>  $obj_model->currentPage(),
            "from" =>  $obj_model->firstItem(),
            "last_page" =>  $obj_model->lastPage(),
            //"path" =>  $obj_model->url(),
            //"per_page" =>  $obj_model->perPage(),
            "to" =>  $obj_model->lastItem(),
            "total" =>  $obj_model->total()
        ];

        return $response;
    }

    public function __sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'code' => $code,
        //    'success' => false,
            'message' => $error,
        ];


        if(!empty($errorMessages))
            $response['data'][] = $errorMessages;

        if($this->__is_api_call)
            return response()->json($response, $code);

        if($this->__is_ajax)
            return response()->json($response, 200);

        if($this->__is_redirect) {
            $request = Request();
            $request->session()->put([
                'error' => $response,
            ]);
            return redirect(\URL::to($this->__module . $this->__view));
        }

        return View::make($this->__module.$this->__view, ['error' => $response, 'page' => $this->__view]);
        //print_r($response);exit;
        return $response;
    }

    protected function __moveUploadFile($obj_image, $title, $image_path, $is_public_path = true)
    {
        $name = str_slug($title).'.'.$obj_image->getClientOriginalExtension();
        $destinationPath = ($is_public_path)? public_path($image_path) : storage_path($image_path);
        /*$dirs = explode('/', trim($image_path, '/'));
        Helper::directory_exists($dirs);*/
        if(!is_dir($destinationPath)){
            mkdir($destinationPath);
        }

        $imagePath = $destinationPath. $name;

        $obj_image->move($destinationPath, $name);
        return $name;
    }

    public function __encryptedPassword($password)
    {
        return md5(Config::get('constants.APP_SALT').$password);
    }

    public function __generateUserHash($email)
    {
        return md5(Config::get('constants.APP_SALT').$email);
    }

    protected function __setSession($key, $value)
    {
        if($this->__call_mode == 'api')
            return false;

        $request = Request();
        $request->session()->put([
            $key => $value,
        ]);
        return true;
    }

    protected function __getSession($key)
    {
        if($this->__call_mode == 'api')
            return [];

        $request = Request();
        return $request->session()->get($key);
    }

    public function __sendMail($identifier, $to, $params)
    {
        $template = MailTemplate::getByIdentifier($identifier);

        $mail_subject = $template->subject;
        $mail_body = $template->body;
        $mail_wildcards = explode(',', $template->wildcards);
        $to = trim($to);
        $from = trim($template->from);
        if(empty($from))
            $from = Setting::getByKey('send_email')->value;


        $mail_wildcard_values = [];
        foreach($mail_wildcards as $value) {
            $value = str_replace(['[',']'],'', $value);
            $mail_wildcard_values[] = $params[$value];
        }

        $mail_body = str_replace($mail_wildcards, $mail_wildcard_values, $mail_body);
        $headers = "From: $from" . "\r\n" ;
        //$headers .= "CC: $cc";

        try {
            //$from = env('MAIL_USERNAME');
            $to1 = explode(',',$to);
            Mail::send('emails.default_template', ['content' => $mail_body], function ($m) use ($to1, $from, $mail_subject) {
                $m->from($from, env('APP_NAME'));
                $m->to($to1)->subject($mail_subject);
            });
        }catch (\Exception $e){
            print $e->getMessage();exit;
            $headers .= "Content-Type: text/html";
            mail($to, $mail_subject, $mail_body, $headers);
        }

        return true;
    }

    protected function __base64ToJpeg($base64_string, $output_file) {
        // open the output file for writing
        $ifp = fopen( $output_file, 'wb' );

        // split the string on commas
         //$data[ 0 ] == "data:image/png;base64"
         //$data[ 1 ] == <actual base64 string>
        //$data = explode( ',', $base64_string );
        // we could add validation here with ensuring count( $data ) > 1
        fwrite( $ifp, base64_decode($base64_string ) );

        // clean up the file resource
        fclose( $ifp );

        return $output_file;
    }

    // have to implement.
    protected function __sendMailNotInUse($to, $message, $data = array(), $files = array(), $ex_headers = array()) {
        // init vars
        $charset = "utf-8";
        $headers = $ex_headers;
        $headers[] = "MIME-Version: 1.0";

        // - from
        $from = MAIL_USERNAME;
        if(isset($data['from'])) {
            if(is_array($data['from'])) {
                $headers[] = "From: ".$data['from'][1]." <".$data['from'][0].">";
                $from = $data['from'][0];
            } else {
                $headers[] = "From: <".$data['from'].">";
                $from = $data['from'];
            }
        }
        $from = MAIL_USERNAME;

        // cc
        if(isset($data['cc'])) {
            if(is_array($data['cc'])) {
                $headers[] = "Cc: ".$data['cc'][1]." <".$data['cc'][0].">";
            } else {
                $headers[] = "Cc: ".$data['cc'];
            }
        }

        // bcc
        if(isset($data['bcc'])) {
            if(is_array($data['bcc'])) {
                $headers[] = "Bcc: ".$data['bcc'][1]." <".$data['bcc'][0].">";
            } else {
                $headers[] = "Bcc: ".$data['bcc']."";
            }
        }
        // reply-to
        if(isset($data['reply-to'])) {
            if(is_array($data['reply-to'])) {
                $headers[] = "Reply-To: ".$data['reply-to'][1]." <".$data['reply-to'][0].">";
            } else {
                $headers[] = "Reply-To: <".$data['reply-to'].">";
            }
        }

        // to_email
        if(is_array($to)) {
            $to_email = $to[0];
        } else {
            $to_email = $to;
        }

        $headers[] = "Subject: {".$data["subject"]."}";
        $headers[] = "X-Mailer: PHP/".phpversion();

        if(count($files) > 0) {
            $random_hash = md5(uniqid(time()));
            $headers[] = "Content-Type: multipart/mixed; boundary=PHP-mixed-".$random_hash;

            //define the body of the message.
            $htmlbody = $message;

            $message = "--PHP-mixed-$random_hash\r\n"."Content-Type: multipart/alternative; boundary=PHP-alt-$random_hash\r\n\r\n";
            $message .= "--PHP-alt-$random_hash\r\n"."Content-Type: text/html; 
          charset=\"".$charset."\"\r\n"."Content-Transfer-Encoding: 7bit\r\n\r\n";


            //Insert the html message.
            $message .= $htmlbody;
            $message .="\r\n\r\n--PHP-alt-$random_hash--\r\n\r\n";

            for($f=0; $f<count($files); $f++) {
                $message .= $this->_prepareAttachment($files[$f],$random_hash);
            }
            $message .= "/r/n--PHP-mixed-$random_hash--";



        } else {
            $headers[] = "Content-type: text/html; charset=".$charset;
        }
        try {
            Mail::send('emails.default_template', ['content' => $message], function ($m) use ($to_email, $from, $data) {
                $m->from($from, APP_NAME);
                $m->to($to_email)->subject($data["subject"]);
            });
        }catch(\Exception $e){

        }


        //@mail($to_email, $data["subject"], $message, implode("\r\n", $headers));
        return;
    }


    public function isArray($string)
    {
        if(is_array($string)){
            return $string['value'];        
        }        
        return $string;
    }

    public function getLatLongFromAddress($address)
    {
        $address = urlencode($address);
        $google_api_key = Config::get('constants.GOOGLE_API_KEY'); //'AIzaSyDlM_qg9kpU6Ho0o5v-JR9dKolEpPvLcT8';

        //print "https://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&region=$address&key=AIzaSyB6D_44n_ZL_llSGghUIRDgWOx1ucPATtc";
        //exit;

        $json = file_get_contents("https://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&region=$address&key=$google_api_key");
        $json = json_decode($json);

        $response['lat'] = (isset($json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'}))? $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'} : '';
        $response['long'] = (isset($json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'}))? $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'} : '';
        $response['city'] = (isset($json->{'results'}[0]->{'address_components'}[2]->{'long_name'}))?$json->{'results'}[0]->{'address_components'}[2]->{'long_name'} : '';
        $response['zip_code'] = (isset($json->{'results'}[0]->{'address_components'}[6]->{'long_name'})) ? $json->{'results'}[0]->{'address_components'}[6]->{'long_name'} : '';
        $response['formatted_address'] = (isset($json->{'results'}[0]->{'formatted_address'})) ? $json->{'results'}[0]->{'formatted_address'} : '';
        return $response;
    }

    protected function __getFileContent($file_path, $is_header = 0){
        $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

        if($ext == 'csv'){
            $arr = array(array(),array());
            $num = 0;
            $row = 0;
            $handle = fopen($file_path, "r");
            while($data = fgetcsv($handle,1000,",")){
                $num = count($data);
                for ($c=0; $c < $num; $c++) {
                    $arr[$row][$c] = $data[$c];
                }
                $row++;
            }
            if($is_header)
                return $arr[0];

            if($arr)
                return $arr;
            return [];
        }


        if ($xlsx = readXlsx::parse($file_path))
            $data = $xlsx->rows();
        if($is_header)
            return $data[0];

        if($data)
            return $data;
        return [];
    }

    protected function __array_search($key, $search_array){
        $key = array_map('strtolower',$key);
        foreach ($search_array as $index => $search){
            $search = strtolower($search);
            if(in_array($search,$key))
                return "$index";
        }
        return false;

    }


    protected function __exportCSV($columns = [], $data,$filename = "",$ignoreCols = [], $export_file_name = 'temp2.csv')
    {
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=file.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
        $file = fopen(public_path($export_file_name), 'w');
        $columns = array_diff( $columns,$ignoreCols);
        fputcsv($file, $columns);
        foreach ($data as $dataRow) {
            $csvRow = [];
            foreach ($columns as $column) {
                if(!in_array($column,$ignoreCols)){
                    if($column == Config::get('constants.LEAD_TITLE_DISPLAY')){
                        $column = 'title';
                    }
                    $csvRow[] = $dataRow->$column;
                }
            }
            fputcsv($file, $csvRow);
        }
        fclose($file);

        return response()->download(public_path($export_file_name),$filename,$headers);

        //return Response::stream($callback, 200, $headers);

    }

}
