<?php
/**
 * Created by PhpStorm.
 * User: 70473
 * Date: 2018/9/18
 * Time: 15:42
 */

use App\Helpers\_notify;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;

/**
 *  api json response
 * @param int $code 状态码
 * @param string $msg 状态信息
 * @param null $data 返回数据
 * @return JsonResponse
 */
function StandardJsonResponse($code, $msg = '', $data = null) {
    $json = [
        'code' => $code,
        'msg' => $msg,
    ];

    if($data !== null){
       $json['data'] = $data;
    }
    return response()->json($json);
}

/**
 * 标准成功响应
 * @param null $data
 * @return JsonResponse
 */
function StandardSuccessJsonResponse($data = null) {
    return StandardJsonResponse(1, "Success", $data);
}

/**
 * 标准失败响应
 * @param null $data
 * @return JsonResponse
 */
function StandardFailJsonResponse($data = null) {
    return StandardJsonResponse(-1, "Fail", $data);
}
/**
 * 用身份证获取性别
 * @param string $iid
 * @return string
 */
function iidGetSex(string $iid) {
    $sex = (int) substr($iid, 16, 1);
    // 怎么能够在请求中随便 echo 呢。
    // echo $sex;
    return $sex % 2 == 0 ? '女' : '男';
}

function getAccessToken(){
    $client = new Client();
    $access_token = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . env('WECHAT_APPID') . "&secret=" . env('WECHAT_SECRET');
    $access_msg = json_decode($client->get($access_token)->getBody());
    $token = $access_msg->access_token;
    return $token;
}
/**
 * 确认是否关注公众号
 * @return bool
 */
function identifyGz($openid)
{
    $client = new Client();
    $token=config('accessToken');
    if($token===null){
        config(['accessToken'=>getAccessToken()]);
        $token=config('accessToken');
    }
    $subscribe_msg = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$token&openid=$openid";

    $subscribe = json_decode($client->get($subscribe_msg)->getBody());
    if(property_exists($subscribe,'errcode')){
        config(['accessToken'=>getAccessToken()]);
        $token=config('accessToken');
        $subscribe_msg = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$token&openid=$openid";
        $subscribe = json_decode($client->get($subscribe_msg)->getBody());
    }

    $isSubscribed = $subscribe->subscribe;
    //
    if ($isSubscribed === 1) {
        return true;
    } else {
        return false;
    }
}

/**
 * 用身份证获取生日
 * @param string $iid
 * @return bool|string
 */
function iidGetBirthday(string $iid) {
    return substr($iid, 6, 8);
}

/**
 * 加密身份证号码
 */
function encryptIid(string $iid){
    //TODO: 对身份证号码进行加密
    return $iid;
}

function notify(_notify $type, $data){

}

/**
 * 验证是否是合法的身份证号码
 */
function checkIid(string $iid){
    return true;
}

function map($array, $func){
    $result = array();
    foreach($array as $item){
        $result[] = func($item);
    }
}

function mapdic($array, $func){
    $result = array();
    foreach($array as $key => $value){
        $result[] = func($key, $value);
    }
}

