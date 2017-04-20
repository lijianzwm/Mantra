<?php
/**
 * Created by PhpStorm.
 * User: lijian
 * Date: 17/4/20
 * Time: 23:23
 */
namespace Home\Controller;

use Think\Controller;

class LoginController extends Controller{

    public function player(){
        $videoid = I("video");
        $passwd = I("passwd");
        if ($passwd == '') {
            switch ($videoid) {
                case '170415': $videoid = "d4806164b73f48388ecff6d6c31960cb"; break;
                default: $videoid = "0";
            }
            $this->assign("vid", $videoid);
            $this->display();
        }else{
            echo "密码错误!";
        }

    }

}
