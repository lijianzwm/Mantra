<?php
/**
 * Created by PhpStorm.
 * User: lijian
 * Date: 2016/4/12
 * Time: 15:12
 */

namespace Home\Controller;

use Common\Service\CountinService;

class CountinController extends CommonController{

    public function addNum(){
        $userid = session("userid");
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

    public function counter(){
        $total = CountinService::getUserTotalNumById(session("userid"));
        $todayNum = CountinService::getUserTodayNumById(session("userid"));
        $this->assign("total", $total);
        $this->assign("todayNum", $todayNum);
        $this->display();
    }

    public function supplement(){
        $userid = session("userid");
        $this->assign("userid", $userid);
        $this->display();
    }

}