<?php
/**
 * Created by PhpStorm.
 * User: lijian
 * Date: 16/7/19
 * Time: 下午9:17
 */

namespace Home\Controller;

use Think\Controller;
use Common\Service\CountinService;

class MobileController extends Controller{

    public function login(){
        layout(false);
        $this->display();
    }

    public function addCount(){
        layout(false);
        $userid = session("userid");
        if( !isset($userid) ){
            redirect(U("Mobile/login"));
        }
        $todayNum = CountinService::getUserTodayNumById($userid);
        $total = CountinService::getUserTotalNumById($userid);
        if( $total == null ){
            $total = "用户不存在！";
            $todayNum = "用户不存在！";
        }else{
            if( $todayNum == null ){
                $todayNum = 0;
            }
        }
        $this->assign("userid", $userid);
        $this->assign("todayNum", $todayNum);
        $this->assign("total", $total);
        $this->display();
    }

}