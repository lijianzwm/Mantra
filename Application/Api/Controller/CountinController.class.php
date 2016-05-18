<?php
/**
 * Created by PhpStorm.
 * User: lijian
 * Date: 2016/4/12
 * Time: 19:42
 */

namespace Api\Controller;


use Think\Controller;
use Common\Service\CountinService;
use Common\Service\DateService;

class CountinController extends CommonController{

    /**
     * url: /Api/Countin/addNum?num=1&userid=1
     */
    public function addNum(){
        $num = I("num");
        $id = I("userid");
        if( !$id ){
            echoJson(1, "没有传入用户id!");
        }
        if (CountinService::isCountNumLegal($num)) {
            if( CountinService::addTodayNum($id,$num) ){
                echoJson(0, "报数成功!");
            }else{
                echoJson(1, "用户不存在!");
            }
        }else{
            echoJson(1, "请输入正确的数字！");
        }
    }

    public function getUserTotalNum(){
        $id = I("userid");
        $total = CountinService::getUserTotalNumById($id);
        if( $total != null ){
            $ret['error_code'] = 0;
            $ret['num'] = $total;
        }else{
            $ret['error_code'] = 1;
            $ret['num'] = $total;
        }
        echo json_encode($ret);
    }

    /**
     * 返回今日总数和全部总数
     */
    public function getUserCurNums(){
        $id = I("userid");
        $ret['totalNum'] = CountinService::getUserTotalNumById($id);
        $ret['todayNum'] = CountinService::getUserTodayNumById($id);
        if(  $ret['totalNum'] == null ){
            $ret['error_code'] = 1;
            $ret['msg'] = "用户不存在！";
        }else{
            if( $ret['todayNum'] == null ){
                $ret['error_code'] = 0;
                $ret['todayNum'] = 0;
            }
        }
        echo json_encode($ret);
    }

    /**
     * 补报数目
     */
    public function supplementNum(){
        $userid = I("userid");
        $date = I("date");
        $num = I("num");
        if( !$userid ){
            echoJson(1, "userid为空!");
        }
        if( !$date ){
            echoJson(1, "请选择日期!");
        }
        if( !DateService::checkYearMonthDay($date) ){
            echoJson(1, "补报失败,日期格式须为yyyy-mm-dd");
        }
        if( !$num ){
            echoJson(1, "请填写数目!");
        }
        if( !CountinService::isCountNumLegal($num)){
            echoJson(1, "请填写合法数字!");
        }
        if( CountinService::supplementNumByUserid($userid, $date, $num) ){
            echoJson(0, "补报成功!");
        }else{
            echoJson(1, "补报失败!");
        }
    }

}