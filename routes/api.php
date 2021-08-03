<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('user', function (Request $request) {
    return $request->user();
});*/



Route::get('subscription/list', 'SubscriptionController@index');
Route::get('setting/detail', 'GeneralController@getSettingValue');
Route::get('marketing/template/list', 'GeneralController@getMarketingMailTemplate');

//Contact Us
Route::post('user/contact', 'UserController@contactUs');

//company
Route::post('status/create', 'CompanyController@storeStatus');
Route::post('lead/status/update', 'CompanyController@updateStatus');
Route::post('type/create', 'CompanyController@storeType');
Route::get('status/list', 'CompanyController@statusList');
Route::get('type/list', 'CompanyController@typeList');

//user
Route::get('user/list', 'UserController@index');
Route::get('user/setting', 'UserController@getSetting');
Route::post('user/update/setting', 'UserController@updateSetting');
Route::post('user/update/location', 'UserController@updateLocation');
Route::get('tenant/user/list', 'UserController@tenantUserList');
Route::post('business/create', 'UserController@storeBusiness');
Route::post('agent/create', 'UserController@storeAgent');
Route::post('user/login', 'UserController@login');
Route::get('user/detail', 'UserController@show');
Route::post('user/profile', 'UserController@profile');
Route::post('user/forgot/password', 'UserController@forgotPassword');
Route::post('user/change/hash/password', 'UserController@changePasswordByHash');
Route::post('user/change/password', 'UserController@changePassword');
Route::post('business/update', 'UserController@updateBusiness');
Route::post('agent/update', 'UserController@updateAgent');
Route::post('user/subscription', 'UserController@subscription');
Route::get('user/subscription', 'UserController@userSubscription');

Route::post('user/social', 'UserController@social');
Route::post('comapny/donate', 'UserController@addCompanyDonation');
Route::post('payment/process', 'UserController@paymentProcess');


//notification
Route::get('notification/list', 'NotificationController@index');
Route::post('notification/create', 'NotificationController@store');
Route::get('notification/{id}', 'NotificationController@show');

//lead
Route::post('lead/create', 'LeadController@store');
Route::post('lead/bulk/update', 'LeadController@bulkUpdate');
Route::get('lead/list', 'LeadController@index');
Route::get('lead/history', 'LeadController@history');
Route::post('lead/media/{id}', 'LeadController@uploadMedia');
Route::get('lead/{id}', 'LeadController@show');
Route::post('lead/query/{id}', 'LeadController@updateQuery');
Route::post('lead/{id}', 'LeadController@update');
Route::post('user/assign/lead/{id}', 'LeadController@userAssignLead');
Route::get('user/lead', 'LeadController@userList');
Route::post('user/lead/appointment/create', 'LeadController@createAppointment');
Route::post('user/outbound/appointment/create', 'LeadController@createOutBoundAppointment');
Route::post('user/lead/appointment/execute', 'LeadController@executeAppointment');
Route::get('user/lead/appointment/list', 'UserLeadAppointmentController@index');
Route::post('user/marketing/appointment/create', 'UserLeadAppointmentController@createAppointment');
Route::get('user/lead/report', 'LeadController@leadReport');
Route::get('user/lead/status/report', 'LeadController@leadUserReport');
Route::get('user/lead/stats/report', 'LeadController@leadStatsReport');
Route::get('lead/status/user/report', 'LeadController@leadStatusUserReport');

//user commission
Route::post('user/commission/create', 'UserCommissionController@store');
Route::get('user/commission/list', 'UserCommissionController@index');
Route::get('user/commission/report', 'UserCommissionController@commissionReport');
Route::post('user/commission/{id}', 'UserCommissionController@update');

//user training script
Route::post('user/training/create', 'UserTrainingScriptController@store');
Route::get('user/training/list', 'UserTrainingScriptController@index');
Route::post('user/training/{id}', 'UserTrainingScriptController@update');
Route::post('user/training/delete/{id}', 'UserTrainingScriptController@delete');


//TenantQuery
Route::post('tenant/query/create', 'TenantQueryController@store');


Route::post('payment/client/token', 'PaymentController@getPaymentToken');
