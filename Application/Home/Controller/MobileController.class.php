<?php
/**
 * Created by PhpStorm.
 * User: lijian
 * Date: 16/7/24
 * Time: 下午12:22
 */

namespace Home\Controller;
use Common\Service\CountinService;
use Common\Service\BrowserService;
use Common\Service\UserService;

class MobileController extends CommonController{
    public function index(){
        layout(false);
        $userid = session("userid");
        $todayNum = CountinService::getUserTodayNumById($userid);
        $user = UserService::getUserById($userid);

        $this->assign("userid", $userid);
        $this->assign("showname", $user['showname']);
        $this->assign("todayNum", $todayNum);

        $this->display();
    }
}