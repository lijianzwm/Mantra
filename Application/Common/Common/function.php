<?php
/**
 * Created by PhpStorm.
 * User: lijian
 * Date: 16/5/17
 * Time: 下午2:52
 */

/**
 * 返回json函数,status=0表示成功
 * @param $status
 * @param $msg
 * @param null $data
 */
function echoJson( $status, $msg, $data=null ){
    $ret['error_code'] = $status;
    $ret['msg'] = $msg;
    if( $data ){
        $ret['data'] = $data;
    }
    exit(json_encode($ret));
}

/**
 * 返回成功json
 * @param $msg
 * @param null $data
 */
function echoSuccess( $msg, $data=null ){
    echoJson(0,$msg,$data);
}

/**
 * 返回失败json
 * @param $msg
 * @param null $data
 */
function echoError( $msg, $data=null ){
    echoJson(1,$msg,$data);
}