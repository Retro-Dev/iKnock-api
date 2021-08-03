<?php namespace App\Http\Controllers;

	use App\Models\CommissionEvents;
    use App\Models\Lead;
    use App\Models\Status;
    use App\Models\TenantCustomField;
    use App\Models\TenantQuery;
    use App\Models\Type;
    use App\Models\User;
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Config;
    use Session;
	use Request;
	use DB;
	use CRUDBooster;
	use App\Models\Company;

	class AdminUserController extends \crocodicstudio\crudbooster\controllers\CBController {

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "first_name";
			$this->limit = "20";
			$this->orderby = "id,desc";
			$this->global_privilege = false;
			$this->button_table_action = true;
			$this->button_bulk_action = true;
			$this->button_action_style = "button_icon";
			$this->button_add = true;
			$this->button_edit = true;
			$this->button_delete = true;
			$this->button_detail = true;
			$this->button_show = false;
			$this->button_filter = false;
			$this->button_import = false;
			$this->button_export = false;
			$this->table = "user";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"First Name","name"=>"first_name"];
			$this->col[] = ["label"=>"Last Name","name"=>"last_name"];
			$this->col[] = ["label"=>"Email","name"=>"email"];
			$this->col[] = ["label"=>"Image Url","name"=>"image_url","image"=>true];
			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'First Name','name'=>'first_name','type'=>'text','validation'=>'required|min:1|max:255|regex:/(?!^\d+$)^.+$/','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Last Name','name'=>'last_name','type'=>'text','validation'=>'required|min:1|max:255|regex:/(?!^\d+$)^.+$/','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Image Url','name'=>'image_url','type'=>'upload','validation'=>'nullable|min:1|max:255|image','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Email','name'=>'email','type'=>'email','validation'=>'required|min:1|max:255|email|unique:user,email,NULL,id,deleted_at,NULL','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Password','name'=>'password','type'=>'password','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			# END FORM DO NOT REMOVE THIS LINE

			# OLD START FORM
			//$this->form = [];
			//$this->form[] = ['label'=>'First Name','name'=>'first_name','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Last Name','name'=>'last_name','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Image Url','name'=>'image_url','type'=>'text','validation'=>'required|min:1|max:255|image','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Email','name'=>'email','type'=>'email','validation'=>'required|min:1|max:255|email|unique:user','width'=>'col-sm-10'];
			# OLD END FORM

			/* 
	        | ---------------------------------------------------------------------- 
	        | Sub Module
	        | ----------------------------------------------------------------------     
			| @label          = Label of action 
			| @path           = Path of sub module
			| @foreign_key 	  = foreign key of sub table/module
			| @button_color   = Bootstrap Class (primary,success,warning,danger)
			| @button_icon    = Font Awesome Class  
			| @parent_columns = Sparate with comma, e.g : name,created_at
	        | 
	        */
	        $this->sub_module = array();


	        /* 
	        | ---------------------------------------------------------------------- 
	        | Add More Action Button / Menu
	        | ----------------------------------------------------------------------     
	        | @label       = Label of action 
	        | @url         = Target URL, you can use field alias. e.g : [id], [name], [title], etc
	        | @icon        = Font awesome class icon. e.g : fa fa-bars
	        | @color 	   = Default is primary. (primary, warning, succecss, info)     
	        | @showIf 	   = If condition when action show. Use field alias. e.g : [id] == 1
	        | 
	        */
	        $this->addaction = array();


	        /* 
	        | ---------------------------------------------------------------------- 
	        | Add More Button Selected
	        | ----------------------------------------------------------------------     
	        | @label       = Label of action 
	        | @icon 	   = Icon from fontawesome
	        | @name 	   = Name of button 
	        | Then about the action, you should code at actionButtonSelected method 
	        | 
	        */
	        $this->button_selected = array();

	                
	        /* 
	        | ---------------------------------------------------------------------- 
	        | Add alert message to this module at overheader
	        | ----------------------------------------------------------------------     
	        | @message = Text of message 
	        | @type    = warning,success,danger,info        
	        | 
	        */
	        $this->alert        = array();
	                

	        
	        /* 
	        | ---------------------------------------------------------------------- 
	        | Add more button to header button 
	        | ----------------------------------------------------------------------     
	        | @label = Name of button 
	        | @url   = URL Target
	        | @icon  = Icon from Awesome.
	        | 
	        */
	        $this->index_button = array();



	        /* 
	        | ---------------------------------------------------------------------- 
	        | Customize Table Row Color
	        | ----------------------------------------------------------------------     
	        | @condition = If condition. You may use field alias. E.g : [id] == 1
	        | @color = Default is none. You can use bootstrap success,info,warning,danger,primary.        
	        | 
	        */
	        $this->table_row_color = array();     	          

	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | You may use this bellow array to add statistic at dashboard 
	        | ---------------------------------------------------------------------- 
	        | @label, @count, @icon, @color 
	        |
	        */
	        $this->index_statistic = array();



	        /*
	        | ---------------------------------------------------------------------- 
	        | Add javascript at body 
	        | ---------------------------------------------------------------------- 
	        | javascript code in the variable 
	        | $this->script_js = "function() { ... }";
	        |
	        */
	        $this->script_js = NULL;


            /*
	        | ---------------------------------------------------------------------- 
	        | Include HTML Code before index table 
	        | ---------------------------------------------------------------------- 
	        | html code to display it before index table
	        | $this->pre_index_html = "<p>test</p>";
	        |
	        */
	        $this->pre_index_html = null;
	        
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Include HTML Code after index table 
	        | ---------------------------------------------------------------------- 
	        | html code to display it after index table
	        | $this->post_index_html = "<p>test</p>";
	        |
	        */
	        $this->post_index_html = null;
	        
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Include Javascript File 
	        | ---------------------------------------------------------------------- 
	        | URL of your javascript each array 
	        | $this->load_js[] = asset("myfile.js");
	        |
	        */
	        $this->load_js = array();
	        
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Add css style at body 
	        | ---------------------------------------------------------------------- 
	        | css code in the variable 
	        | $this->style_css = ".style{....}";
	        |
	        */
	        $this->style_css = NULL;
	        
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Include css File 
	        | ---------------------------------------------------------------------- 
	        | URL of your css each array 
	        | $this->load_css[] = asset("myfile.css");
	        |
	        */
	        $this->load_css = array();
	        //$this->load_css[] = asset("assets/css/custom_crudbooster.css");
	        
	        
	    }


	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for button selected
	    | ---------------------------------------------------------------------- 
	    | @id_selected = the id selected
	    | @button_name = the name of button
	    |
	    */
	    public function actionButtonSelected($id_selected,$button_name) {
	        //Your code here
	            
	    }


	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate query of index result 
	    | ---------------------------------------------------------------------- 
	    | @query = current sql query 
	    |
	    */
	    public function hook_query_index(&$query) {
            $query->where('user_group_id', '1');
	    }

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate row of index table html 
	    | ---------------------------------------------------------------------- 
	    |
	    */    
	    public function hook_row_index($column_index,&$column_value) {	        
	    	//Your code here
	    }

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate data input before add data is execute
	    | ---------------------------------------------------------------------- 
	    | @arr
	    |
	    */
	    public function hook_before_add(&$postdata) {

	        $this->_postdata = $postdata;
            $postdata['user_group_id'] = 1;
            if (isset($postdata['password'])) {
                $obj_controller = new \App\Http\Controllers\Controller();
                $postdata['password'] = $obj_controller->__encryptedPassword($_POST['password']);
            }
	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after add public static function called 
	    | ---------------------------------------------------------------------- 
	    | @id = last insert id
	    | 
	    */
	    public function hook_after_add($id) {
	        //Your code here
            $this->_postdata['primary_user_id'] = $id;
            $company_id = Company::create($this->_postdata);

            Status::createTenantStatus($company_id);
            Type::createTenantType($company_id);
            CommissionEvents::createTenantCommissionEvents($company_id);

            $tenant_type_list = Type::getList($company_id);
            $template_fields = Config::get('constants.LEAD_DEFAULT_COLUMNS');
            TenantCustomField::insertTenantDefaultFields($company_id, $template_fields);
            //['lead_name','lead_type','lead_status', 'address', 'city', 'county', 'state', 'zip_code'];
            foreach($tenant_type_list as $tenant_type){
                $count = 0;
                $data = [];
                foreach($template_fields as $template_field) {
                    $data[$count]['index'] = 0;
                    $data[$count]['index_map'] = str_replace('_', ' ', $template_field);
                    $data[$count]['field'] = $template_field;
                    $data[$count]['order_by'] = $count+1;

                    $count++;
                }

                Lead::saveTemplateField($tenant_type->id, $data);
            }

            $obj = new TenantQuery();
            $obj->tenant_id = $company_id;
            $obj->query = 'Name of person you talk to';
            $obj->type = 'appointment';
            $obj->save();

            $obj_controller = new \App\Http\Controllers\Controller();
            $hash = $obj_controller->__generateUserHash($this->_postdata['email']);

            // update forgot password hash and update hash date
            User::where('id', $id)->Update([
                'company_id' => $company_id,
                'forgot_password_hash' => $hash,
                'forgot_password_hash_date' => Carbon::now()]);




            $mail_params['USER_NAME'] = $this->_postdata['first_name'] . ' ' . $this->_postdata['last_name'];
            $mail_params['CONFIRMATION_LINK'] = env('APP_URL')."/user/registration/$hash";
            //$mail_params['USER_LINK'] = env('APP_URL').'/user/login';
            $mail_params['APP_NAME'] = env('APP_NAME');

            // make forgot password url and implement its email configuration.
            $obj_controller->__sendMail('tenant_registration_email', $this->_postdata['email'], $mail_params);
	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate data input before update data is execute
	    | ---------------------------------------------------------------------- 
	    | @postdata = input post data 
	    | @id       = current id 
	    | 
	    */
	    public function hook_before_edit(&$postdata,$id)
        {
            //Your code here
            if (isset($postdata['password'])) {
                $obj_controller = new \App\Http\Controllers\Controller();
                $postdata['password'] = $obj_controller->__encryptedPassword($_POST['password']);
            }

	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after edit public static function called
	    | ----------------------------------------------------------------------     
	    | @id       = current id 
	    | 
	    */
	    public function hook_after_edit($id) {
	        //Your code here 

	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command before delete public static function called
	    | ----------------------------------------------------------------------     
	    | @id       = current id 
	    | 
	    */
	    public function hook_before_delete($id) {
	        //Your code here

	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after delete public static function called
	    | ----------------------------------------------------------------------     
	    | @id       = current id 
	    | 
	    */
	    public function hook_after_delete($id) {
	        User::deleteAgentByTenantId($id);

	    }



	    //By the way, you can still create your own method in here... :) 


	}