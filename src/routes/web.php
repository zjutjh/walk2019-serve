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

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Enroll\ApplyController;
use App\Http\Controllers\Enroll\GroupController;
use App\Http\Controllers\Enroll\RouteController;
use App\Http\Controllers\Enroll\UserController;
use App\Http\Controllers\Enroll\WechatLoginController;

Route::get('/oauth', [WechatLoginController::class,'oauth']);
Route::any('/wx/login',  [WechatLoginController::class,'wechatLogin']);

Route::any('/index/info', 'IndexController@indexInfo');

Route::get('/prize','PrizeController@index');
Route::post('/prize','PrizeController@indexPost');

Route::group(['middleware' => ['check.admin']], function () {
    Route::any('user/verify',[UserController::class,'verify']);
    Route::any('/prize/get_data','PrizeController@getData');
    Route::any('/prize/select', 'PrizeController@select');
    Route::any('/prize/verify', 'PrizeController@verify');
    Route::any('/test', [AdminController::class,'sendTmp']);
    Route::any('/test2', [AdminController::class,'genWalkGroupId']);
    Route::any('/test3', [AdminController::class,'Download']);
    Route::any('/test4', [AdminController::class,'SendResult']);
    Route::any('/test5',[AdminController::class,'EncryptIid']);
});

Route::group(['middleware' => ['check.wechat']], function () {
    Route::post('/user/info', [UserController::class,'getUserInfo']);
    Route::post('/route/list', [RouteController::class,'getRouteList']);
    Route::post('/group/remain', [GroupController::class,'getRemainInfo']);
    Route::post('/group/info', [GroupController::class,'getGroupInfo']);
    Route::post('/group/members/list', [GroupController::class,'getGroupMembers']);

    Route::group(['middleware' => ['check.finish']], function () {
        Route::post('/user/register',  [UserController::class,'register']);
        Route::post('/user/update',  [UserController::class,'updateInfo']);
        Route::post('/group/list', [GroupController::class,'groupLists']);
        Route::post('/group/create', [GroupController::class,'createGroup']);
        Route::post('/group/break', [GroupController::class,'breakGroup']);
        Route::post('/group/submit', [GroupController::class,'submitGroup']);
        Route::post('/group/search', [GroupController::class,'searchGroup']);
        Route::post('/group/members/delete', [GroupController::class,'deleteMember']);
        Route::post('/group/update', [GroupController::class,'updateGroupInfo']);
        Route::post('/group/unsubmit', [GroupController::class,'unSubmitGroup']);
        Route::post('/group/leave', [GroupController::class,'leaveGroup']);
        Route::post('/apply/matching', [ApplyController::class,'doMatching']);
        Route::post('/apply/list',  [ApplyController::class,'getApplicantList']);
        Route::post('/apply/agree',  [ApplyController::class,'agreeMember']);
        Route::post('/apply/refuse',  [ApplyController::class,'refuseMember']);
        Route::post('/apply/do',  [ApplyController::class,'doApply']);
        Route::post('/apply/delete',  [ApplyController::class,'deleteApply']);
        Route::post('/apply/count',  [ApplyController::class,'getApplyCount']);
    });
});
