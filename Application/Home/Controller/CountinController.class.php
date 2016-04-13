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
        $this->display();
    }

    public function addNumHandler(){
        $num = I("num");
        $id = session("userid");
        if( CountinService::addNum($id,$num) ){
            $this->success("报数成功！");
        }else{
            $this->error("报数失败！");
        }
    }

    public function counter(){
        $total = CountinService::getNumById(session("userid"));
        $this->assign("total", $total);
        $this->display();
    }

}