<?php
/**
 * Created by PhpStorm.
 * User: lijian
 * Date: 16/7/24
 * Time: 下午12:22
 */

namespace Home\Controller;


class MobileController extends CommonController{
    public function index(){
        layout(false);
        $this->display();
    }
}