<?php
/**
 * Created by PhpStorm.
 * User: lijian
 * Date: 16/5/14
 * Time: 下午4:38
 */

namespace Admin\Controller;

use \Common\Service\CountinService;

/**
 * 补报数目
 * Class SupplementController
 * @package Admin\Controller
 */
class SupplementController extends CommonController{
    public function supplement(){
        $this->display();
    }

    public function supplementNum(){
        $phone = I("phone");
        $date = I("date");
        $num = I("num");
        if( !CountinService::supplementNumByPhone($phone, $date, $num) ){
            $this->error("补报失败!");
        }else{
            $this->success("补报成功!");
        }
    }

}