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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/oauth', 'WXLoginController@oauth');
Route::any('/wx/login', 'WXLoginController@wxLogin');


Route::get('/index/info', 'IndexController@indexInfo');
Route::any('/user/info', 'UserController@getMyInfo');

Route::group(['middleware' => ['check.finish']], function() {
    Route::any('/user/register', 'UserController@register');
    Route::any('/user/update', 'UserController@updateInfo');

    Route::any('/group/list', 'GroupController@groupLists');
    Route::any('/group/create', 'GroupController@createGroup');
    Route::any('/group/break', 'GroupController@breakGroup');
    Route::any('/group/submit', 'GroupController@submitGroup');
    Route::any('/group/search', 'GroupController@searchTeam');
    Route::any('/group/members/list', 'GroupController@getGroupMembers');
    Route::any('/group/member/delete', 'GroupController@deleteMember');
    Route::any('/group/info', 'GroupController@getGroupInfo');
    Route::any('/group/update', 'GroupController@updateGroupInfo');
    Route::any('/group/unsubmit', 'GroupController@unSubmitGroup');
    Route::any('/group/leave', 'GroupController@leaveGroup');

    Route::any('/apply/list', 'ApplyController@getApplyList');
    Route::any('/apply/agree', 'ApplyController@agreeMember');
    Route::any('/apply/refuse', 'ApplyController@refuseMember');
    Route::any('/apply/do', 'ApplyController@doApply');
    Route::any('/apply/delete', 'ApplyController@deleteApply');
    Route::any('/apply/count', 'ApplyController@getApplyCount');
});

