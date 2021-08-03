<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/tenant/login', 'UserController@loginIndex');
Route::get('/tenant', 'UserController@loginIndex');
Route::post('/tenant/login', 'UserController@loginWeb');

Route::get('/login', function () {
    return view('tenant.login.index');
});
Route::get('tenant/login/forget_password', function () {
    return view('tenant.login.forget_password');
});
Route::post('user/forgot/password', 'UserController@forgotPassword');

/*static routes*/
Route::group(['middleware' => ['login.auth'], 'prefix'=>'tenant','as'=>'tenant.'], function() {

    Route::get('commission', 'UserCommissionController@commissionView');
    Route::get('commission/create', 'UserCommissionController@indexView');
    Route::get('commission_event', function () {
        return view('tenant.commission_event.comm_event_mgmt');
    });
    Route::get('commission_event/create', function () {
        return view('tenant.commission_event.add_comm_event');
    });
    Route::get('commission_event/edit/{id}', function () {

        return view('tenant.commission_event.edit_comm_event');
    });

    //Template Management
    Route::get('template/add', function () {
        return view('tenant.template.add_template');
    });
    Route::get('template', function () {
        return view('tenant.template.template_list');
    });
    Route::get('lead-default-order', function () {
        return view('tenant.template.lead_default_view');
    });

     Route::get('template/edit/{id}', function (Request $request) {
         if($request['id'] == 'max'){
             $record = \App\Models\TenantTemplate::getByMax($request['company_id']);
         }else
            $record = \App\Models\TenantTemplate::getById($request['id']);
         return view('tenant.template.edit_template',['data'=>$record]);
    });

    Route::get('template/update/{id}/{temp_id}','TenantQueryController@updateTemplate');
    Route::get('lead-default/edit/{id}','TenantQueryController@updateTenantDefaulTemplate');
    Route::get('template/fields', 'LeadController@templateFieldList');
    Route::post('template/field/index/update', 'LeadController@updateTemplateFieldIndex');
    Route::post('template/field/clear/indexes', 'LeadController@updateTemplateFieldIndexClear');
    Route::get('lead/default/fields', 'LeadController@defaultFieldList');
    Route::get('/logout', 'UserController@logout');

    Route::get('template/create/{id}', function () {
        return view('tenant.template.add_field');
    });

    Route::get('commission/edit/{id}', 'UserCommissionController@updateView');


    Route::get('dashboard', 'LeadController@dashboard');

    Route::get('field', function () {
        return view('tenant.field.field_mgmt');
    });

    Route::get('field/create', function () {
        return view('tenant.field.add_field');
    });

    // Field Management
    Route::get('query/list', 'TenantQueryController@index');
    Route::post('query/update/sorting', 'TenantQueryController@updateSorting');
    Route::post('query/create', 'TenantQueryController@store');
    Route::post('template/field/create', 'TenantQueryController@storeTemplateField');
    Route::post('template/field/update', 'TenantQueryController@updateTemplateField');
    Route::post('lead/default/field/update', 'TenantQueryController@updateLeadDefaultField');
    Route::post('template/field/delete/{id}', 'TenantQueryController@destroyTemplateField');
    Route::post('lead/default/field/delete/{id}', 'TenantQueryController@destroyLeadDefaultField');
    Route::get('field/detail/{id}', 'TenantQueryController@show');
    Route::post('field/edit/{id}', 'TenantQueryController@update');
    Route::post('field/delete/{id}', 'TenantQueryController@destroy');
    Route::get('field/edit/{id}', function () {
        return view('tenant.field.edit_field');
    });


    Route::get('lead', 'LeadController@indexView');
    Route::get('lead/edit/{id}', 'LeadController@edit');
    Route::post('lead/bulk/update', 'LeadController@bulkUpdate');

    Route::get('lead/add_lead', 'LeadController@addView');
    Route::get('lead/add_lead/get/{id}', 'LeadController@getFields');

    Route::get('lead/wizard', 'LeadController@wizardView');
    Route::get('lead/lead_status', function () {
        return view('tenant.lead.lead_status');
    });

    //User
    Route::post('agent/profile', 'UserController@profile');
    Route::post('user/profile', 'UserController@profile');
    Route::post('agent/update', 'UserController@updateAgent');
    Route::post('user/update', 'UserController@updateBusiness');
    Route::post('printer/email/update', 'UserController@updatePrinterEmailAddress');

    //Printer Email Routes
    Route::get('printer_email', 'UserController@showPrinterEmail');
    Route::get('printer_email/create', function () {
        return view('tenant.printer.add_printer_email');
    });
    Route::get('printer_email/edit/{id}', function () {
        return view('tenant.printer.edit_printer_email');
    });
    

//Lead Start
    Route::post('lead/create', 'LeadController@store');
    Route::get('lead/list', 'LeadController@listView')->name('export');
    Route::get('lead/status/list', 'LeadController@statusListView');
    Route::get('lead/history', 'LeadController@history');
    Route::get('leads/history/export', 'LeadController@leadsHistoryExport');
    Route::get('lead/history/export/{lead_id}', 'LeadController@historyExport');
    Route::post('lead/query/{id}', 'LeadController@updateQuery');
    Route::post('lead/delete/{id}', 'LeadController@destroy');
    Route::get('/leads/map', 'LeadController@leadsMap');
    Route::get('leads/{id}', 'LeadController@show');
    Route::post('leads/{id}', 'LeadController@update');
//Lead End

    //company
    Route::post('status/create', 'CompanyController@storeStatus');
    Route::post('status/edit/{id}', 'CompanyController@updateStatusValue');
    Route::post('status/delete/{id}', 'CompanyController@deleteStatus');
    Route::get('status/detail/{id}', 'CompanyController@getStatusDetail');
    Route::post('status/sorting/update', 'CompanyController@updateStatusSorting');
    Route::get('type/detail/{id}', 'CompanyController@getTypeDetail');
    Route::post('type/create', 'CompanyController@storeType');
    Route::post('type/edit/{id}', 'CompanyController@updateTypeValue');
    Route::post('type/delete/{id}', 'CompanyController@deleteType');
    Route::get('status/list', 'CompanyController@statusList');
    Route::get('type/list', 'CompanyController@typeList');

//user commission
    Route::post('user/commission/create', 'UserCommissionController@store');
    Route::get('user/commission/list', 'UserCommissionController@index');
    Route::get('user/commission/export', 'UserCommissionController@exportCSV');
    Route::post('user/commission/delete/{id}', 'UserCommissionController@destroy');
    Route::get('user/commission/report', 'UserCommissionController@commissionReport');
    Route::post('user/commission/{id}', 'UserCommissionController@update');
    Route::get('user/commission/{id}', 'UserCommissionController@show');

//user commission event
    Route::get('commission/event/detail/{id}', 'CompanyController@getCommissionEventDetail');
    Route::get('commission/event/list', 'CompanyController@getCommissionEventList');
    Route::post('commission/event/create', 'CompanyController@storeCommissionEvent');
    Route::post('commission/event/edit/{id}', 'CompanyController@updateCommissionEvent');
    Route::post('commission/event/delete/{id}', 'CompanyController@deleteCommissionEvent');


    Route::get('user/lead/report', 'LeadController@leadReport');
    //Route::get('user/lead/stats/report', 'LeadController@leadStatsReport');
    Route::get('user/lead/stats/report', 'LeadController@leadStatusStatsReport');
    Route::get('user/lead/status/report', 'LeadController@leadUserReport');
    Route::get('lead/status/user/report', 'LeadController@leadStatusUserReport');
    Route::get('user/lead/appointment/list', 'UserLeadAppointmentController@index');

    Route::get('lead/lead_status/edit/{id}', function () {
        return view('tenant.lead.edit_leadstatus');
    });
    Route::get('lead/lead_status/create', function () {
        return view('tenant.lead.add_leadstatus');
    });

    Route::get('lead/lead_type', function () {
        return view('tenant.lead.lead_type');
    });
    Route::get('lead/lead_type/create', function () {
        return view('tenant.lead.add_leadtype');
    });

    Route::get('lead/lead_type/edit/{id}', function () {
        return view('tenant.lead.edit_leadtype');
    });
    Route::get('lead/{id}', 'LeadController@show');


    Route::get('/forget_password', function () {
        return view('tenant.login.forget_password');
    });

    Route::get('/team-performance/comm_report', 'UserCommissionController@viewCommissionReport');
    Route::get('/team-performance/user_report', 'UserCommissionController@viewUserReport');
    Route::get('/team-performance/team_report', 'LeadController@viewLeadReport');

    Route::get('/training', function () {
        return view('tenant.training.training_mgmt');
    });
    Route::get('/training/create', function () {
        return view('tenant.training.add_script');
    });

    Route::get('training/edit/{id}', function () {
        return view('tenant.training.edit_script');
    });
//Training
    Route::post('user/training/create', 'UserTrainingScriptController@store');
    Route::get('user/training/list', 'UserTrainingScriptController@index');
    Route::post('user/training/{id}', 'UserTrainingScriptController@update');
    Route::get('user/training/{id}', 'UserTrainingScriptController@show');
    Route::post('user/training/delete/{id}', 'UserTrainingScriptController@deleteTrainingScript');


    Route::get('/agent', function () {
        return view('tenant.agent.agent_mgmt');
    });

    Route::get('agent/edit_profile', 'UserController@showView');

    Route::get('/user/list', 'UserController@tenantUserList');

    Route::get('/agent/create', function () {
        return view('tenant.agent.add_agent');
    });
    Route::post('agent/create', 'UserController@storeAgent');
    Route::post('agent/delete/{id}', 'UserController@deleteAgent');
    Route::post('agent/reset/{id}', 'UserController@resetAgentPassword');

    Route::get('/agent/edit/{id}', function () {
        return view('tenant.agent.edit_agent');
    });

    Route::post('/lead/wizard/upload', 'LeadController@uploadLeads');
    Route::post('/template/create', 'LeadController@addTemplate');
    Route::post('/lead/wizard/template', 'LeadController@wizardTemplate');
    Route::post('/leads/wizard/fields', 'LeadController@wizardFields');
   

    Route::get('/lead/template/list', 'LeadController@templateList');
    Route::post('/lead/template/delete', 'LeadController@deleteTemplate');
    Route::get('/lead/template/{id}', 'LeadController@templateShow');
    Route::post('/lead/template/update/{id}', 'LeadController@templateUpdate');
    Route::post('/lead/template/delete/{id}', 'LeadController@templateDestroy');

    Route::get('scheduling', 'UserLeadAppointmentController@schedulingView');
    Route::get('scheduling/create','UserLeadAppointmentController@storeView');
    Route::post('scheduling/create', 'UserLeadAppointmentController@store');
    Route::post('scheduling/store', 'UserLeadAppointmentController@store');
    Route::get('scheduling/getAppointments', 'UserLeadAppointmentController@getAppointments');
    Route::get('scheduling/{id}', 'UserLeadAppointmentController@show');
    Route::post('scheduling', 'UserLeadAppointmentController@update');
    Route::post('scheduling/delete/{id}', 'UserLeadAppointmentController@destroy');



}); /*Tenant Group End*/

/*static routes end*/



/* API routes*/




/*Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');*/
Route::get('/layout/about', 'HomeController@layoutAbout');
Route::get('/layout/contact', 'HomeController@layoutSecurity');
Route::get('/privacy-policy', function () {
    return view('layouts.privacy-policy');
});

Route::get('/terms-conditions', function () {
    return view('layouts.terms-conditions');
});
Route::post('/admin/subscribe/user', 'UserController@subscribe');
Route::post('/admin/user/donate', 'UserController@addDonation');
Route::any('/user/subscribe', 'UserController@updateSubscription');
Route::any('/user/forgot/password/{token}', 'UserController@changePasswordWeb');
Route::any('/user/registration/{token}', 'UserController@changePasswordWeb');
