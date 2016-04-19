<?php
/**
 * Created by PhpStorm.
 * User: lijian
 * Date: 2016/4/12
 * Time: 19:42
 */

namespace Api\Controller;


use Think\Controller;
use Common\Service\CountinService;

class CountinController extends Controller{

    /**
     * url: /Api/Countin/addNum?num=1&userid=1
     */
    public function addNum(){
        $num = I("num");
        $id = I("userid");
        if (CountinService::isCountNumLegal($num)) {
            if( CountinService::addTodayNum($id,$num) ){
                $ret['error_code'] = 0;
            }else{
                $ret['error_code'] = 1;
                $ret['msg'] = "用户不存在！";
            }
        }else{
            $ret['error_code'] = 1;
            $ret['msg'] = "请输入正确的数字！";
        }
        echo json_encode($ret);
    }

    public function getTotalNum(){
        $id = I("userid");
        $total = CountinService::getUserTotalNumById($id);
        if( $total != null ){
            $ret['error_code'] = 0;
            $ret['num'] = $total;
        }else{
            $ret['error_code'] = 1;
            $ret['num'] = $total;
        }
        echo json_encode($ret);
    }

}