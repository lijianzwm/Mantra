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
use Common\Service\DateService;
use Common\Service\StageGXService;
use Think\Controller;

class TestController extends Controller{
    public function index(){
//        layout(false);

//        CheckService::checkApiParam("dharma", "决定幢");

//        if( StageGXService::isInStage() ){
//            dump("in stage");
//        }else{
//            dump("not in stage");
//        }
//
//        if( DateService::isYearMonthDayInPassedMonth("2016-05-21") ){
//            dump("yes!");
//        }else{
//            dump("no!");
//        }

        $this->display("index");
    }

    public function handler(){
        $userid = I("username");
//        dump(CheckService::checkUsernameFormat($username));
        dump( MysqlService::generateMysqlTotalNum() );
//        dump(CheckService::checkNumFormat($username));
    }
}