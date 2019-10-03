<?php
/**
 * Created by PhpStorm.
 * User: 70473
 * Date: 2018/9/18
 * Time: 15:42
 */

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
        'data' => $data
    ];
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
