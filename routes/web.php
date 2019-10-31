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

Route::get('/oauth', 'WXLoginController@oauth');
Route::any('/wx/login', 'WXLoginController@wxLogin');

Route::any('/index/info', 'IndexController@indexInfo');

Route::group(['middleware' => ['check.admin']], function () {
    Route::any('/prize/current','PrizeController@current');
    Route::any('/prize/select', 'PrizeController@select');
    Route::any('/test', 'TestController@sendTmp');
    Route::any('/test2', 'TestController@GenYXGroupId');
    Route::any('/test3', 'TestController@Download');
    Route::any('/test4', 'TestController@SendResult');
});

Route::group(['middleware' => ['check.wechat']], function () {
    Route::post('/user/info', 'UserController@getMyInfo');
    Route::post('/route/list', 'RouteController@getRouteList');
    Route::post('/group/info', 'GroupController@getGroupInfo');
    Route::post('/group/members/list', 'GroupController@getGroupMembers');
    Route::group(['middleware' => ['check.finish']], function () {
        Route::post('/user/register', 'UserController@register');
        Route::post('/user/update', 'UserController@updateInfo');

        Route::post('/group/list', 'GroupController@groupLists');
        Route::post('/group/create', 'GroupController@createGroup');
        Route::post('/group/break', 'GroupController@breakGroup');
        Route::post('/group/submit', 'GroupController@submitGroup');
        Route::post('/group/search', 'GroupController@searchTeam');

        Route::post('/group/members/delete', 'GroupController@deleteMember');
        Route::post('/group/update', 'GroupController@updateGroupInfo');
        Route::post('/group/unsubmit', 'GroupController@unSubmitGroup');
        Route::post('/group/leave', 'GroupController@leaveGroup');

        Route::post('/apply/list', 'ApplyController@getApplyList');
        Route::post('/apply/agree', 'ApplyController@agreeMember');
        Route::post('/apply/refuse', 'ApplyController@refuseMember');
        Route::post('/apply/do', 'ApplyController@doApply');
        Route::post('/apply/delete', 'ApplyController@deleteApply');
        Route::post('/apply/count', 'ApplyController@getApplyCount');
    });
});
