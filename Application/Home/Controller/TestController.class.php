<?php
/**
 * Created by PhpStorm.
 * User: lijian
 * Date: 16/5/17
 * Time: 下午10:19
 */

namespace Home\Controller;

use Common\Service\UserService;

class TestController{
    public function index(){
        if( UserService::checkChineseName("李健") ){
            dump("是中文");
        }else{
            dump("非中文");
        }
    }
}