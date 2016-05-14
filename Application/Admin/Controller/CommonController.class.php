<?php
/**
 * Created by PhpStorm.
 * User: lijian
 * Date: 2016/3/8
 * Time: 8:46
 */

namespace Admin\Controller;

use Think\Controller;

class CommonController extends Controller{
    public function _initialize(){
        if( !session("adminuser") ){
            $this->redirect("User/index");
        }
    }
}