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

Route::get('/oauth', 'WechatLoginController@oauth');
Route::any('/wx/login', 'WechatLoginController@wechatLogin');

Route::any('/index/info', 'IndexController@indexInfo');

Route::get('/prize','PrizeController@index');
Route::post('/prize','PrizeController@indexPost');

Route::group(['middleware' => ['check.admin']], function () {
    Route::any('user/verify','UserController@verify');
    Route::any('/prize/get_data','PrizeController@getData');
    Route::any('/prize/select', 'PrizeController@select');
    Route::any('/prize/verify', 'PrizeController@verify');
    Route::any('/test', 'AdminController@sendTmp');
    Route::any('/test2', 'AdminController@genWalkGroupId');
    Route::any('/test3', 'AdminController@Download');
    Route::any('/test4', 'AdminController@SendResult');
    Route::any('/test5','AdminController@EncryptIid');
});

Route::group(['middleware' => ['check.wechat']], function () {
    Route::post('/user/info', 'UserController@getUserInfo');
    Route::post('/route/list', 'RouteController@getRouteList');
    Route::post('/group/remain', 'GroupController@getRemainInfo');
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
