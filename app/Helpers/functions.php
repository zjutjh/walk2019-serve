<?php
/**
 * Created by PhpStorm.
 * User: 70473
 * Date: 2018/9/18
 * Time: 15:42
 */


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
        'data' => $data
    ];
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
    echo $sex;
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