<?php
/**
 * Created by PhpStorm.
 * User: lijian
 * Date: 16/5/17
 * Time: 下午10:19
 */

namespace Home\Controller;

use Common\Service\CheckService;
use Common\Service\MysqlService;
use Think\Controller;

class TestController extends Controller{
    public function index(){
//        layout(false);
        $this->display("userCenter");
    }

    public function handler(){
        $userid = I("username");
//        dump(CheckService::checkUsernameFormat($username));
        dump( MysqlService::generateMysqlTotalNum() );
//        dump(CheckService::checkNumFormat($username));
    }
}