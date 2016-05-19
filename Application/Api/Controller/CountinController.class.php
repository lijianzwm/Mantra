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
use Common\Service\CheckService;

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
        if (!CountinService::isSupplementDateLegeal($date)) {
            echoJson(1, "补报失败,补报日期须为今天之前!");
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

    /**
     * 返回今日总数和全部总数
     */
    public function getUserCurNums(){
        $id = I("userid");
        $data['totalNum'] = CountinService::getUserTotalNumById($id);
        $data['todayNum'] = CountinService::getUserTodayNumById($id);
        if(  $data['totalNum'] == null ){
            echoJson(1, "用户不存在!");
        }else{
            if( $data['todayNum'] == null ){
                $data['todayNum'] = 0;
                echoJson(0, "获取今日数目和总数成功!", $data);
            }
        }
    }

}