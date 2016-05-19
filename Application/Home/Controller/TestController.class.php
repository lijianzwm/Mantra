<?php
/**
 * Created by PhpStorm.
 * User: lijian
 * Date: 16/5/17
 * Time: 下午10:19
 */

namespace Home\Controller;

use Common\Service\CheckService;
use Think\Controller;

class TestController extends Controller{
    public function index(){
        $this->display();
    }

    public function handler(){
        $username = I("username");
//        dump(CheckService::checkUsernameFormat($username));
        dump(CheckService::checkNumFormat($username));
    }
}