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
        $this->assign("todayNum", $todayNum);
        $this->assign("total", $total);
        $this->display();
    }

    public function addNumHandler(){
        $num = I("num");
        if( !is_numeric($num)){
            $this->error("请输入数字！");
        }
        $num = intval($num);
        $id = session("userid");
        if( $num > 0 ){
            if( CountinService::addTodayNum($id,$num) ){
                $this->success("报数成功！", U('Ranklist/todayRanklist'));
            }else{
                $this->error("报数失败！");
            }
        }else{
            $this->error("请输入大于0的正数！");
        }

    }

    public function counter(){
        $total = CountinService::getUserTotalNumById(session("userid"));
        $todayNum = CountinService::getUserTodayNumById(session("userid"));
        $this->assign("total", $total);
        $this->assign("todayNum", $todayNum);
        $this->display();
    }

    public function supplement(){
        $phone = session("phone");
        $this->assign("phone", $phone);
        $this->display();
    }

    public function supplementHandler(){
        $phone = I("phone");
        $date = I("date");
        $num = I("num");
        if( CountinService::supplementNum($phone, $date, $num) ){
            $this->success("补报成功!");
        }else{
            $this->error("补报失败!");
        }
    }

}