<?php
/**
 * Created by PhpStorm.
 * User: 70473
 * Date: 2018/9/18
 * Time: 15:42
 */

use App\Helpers\_notify;
use Illuminate\Http\JsonResponse;

/**
 *  api json response
 * @param $code 状态码
 * @param string $msg 状态信息
 * @param null $data 返回数据
 * @return \Illuminate\Http\JsonResponse
 */
function template($code, $msg = '', $data = null) {
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
 *  api json response
 * @param $code 状态码
 * @param string $msg 状态信息
 * @param null $data 返回数据
 * @return \Illuminate\Http\JsonResponse
 */
function RJM($code, $msg = '', $data = null) {
    $json = [
        'code' => $code,
        'msg' => $msg,
        'data' => $data
    ];
    return response()->json($json);
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

