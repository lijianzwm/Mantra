<?php
/**
 * Created by PhpStorm.
 * User: lijian
 * Date: 2016/4/13
 * Time: 10:50
 */

namespace Api\Controller;


use Think\Controller;

class CommonController extends Controller{
    public function _initialize(){
        $apiKey = I("apikey");
        $rightKey = C("API_ACCESS_KEY");
        if( $apiKey != $rightKey ){
            $ret["error_code"] = 1;
            $ret["msg"] = "apikey错误！"."get:$apiKey, right:$rightKey";
            echo json_encode($ret);
            die();
        }
    }
}