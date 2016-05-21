<?php
/**
 * Created by PhpStorm.
 * User: lijian
 * Date: 2016/4/12
 * Time: 19:42
 */

namespace Api\Controller;

use Think\Controller;
use Common\Service\RedisService;
use Common\Service\StageGXService;
use Common\Service\UserService;
use Common\Service\CountinService;
use Common\Service\CheckService;
use Common\Service\DebugService;
use Common\Service\MysqlService;
use Common\Service\DateService;

class CountinController extends CommonController{

    /**
     * url: /Api/Countin/addNum?num=1&userid=1
     */
    public function addNum(){
        $num = I("num");
        $userid = I("userid");

        //校验
        CheckService::checkApiParam("userid", $userid);
        CheckService::checkApiParam("num", $num);
        if( session("userid") && $userid != session("userid")){
            echoError("师兄别闹,不要乱传入别人的userid,如果不是故意来捣乱的就请重新登录后再进行报数!");
        }
        if (!UserService::isUserExist($userid)) {
            echoError("用户不存在!");
        }
        $todayNum = CountinService::getUserTodayNumById($userid);
        if( $todayNum + $num < 0 ){
            echoError("总数小于0!");
        }
        if (0 == intval($num)) {
            echoError("数目为0!");
        }

        //更新mysql
        if( CountinService::isTodayFirstCommit($userid)){//如果是当天第一次报数
            DebugService::displayLog("当前报数是当天第一次报数");
            if( !MysqlService::insertMysqlTodayNum($userid, $num) ){
                echoError("日计数表插入数据失败!");
            }
        }else{
            DebugService::displayLog("当前报数不是当天第一次报数");
            if( !MysqlService::addMysqlTodayNum($userid, $num) ){
                echoError("日计数表更新数据失败!");
            }
        }

        //更新User中total字段,如果更新失败,重新统计total
        if( !MysqlService::addMysqlUserTotalNum($userid,$num) ){
            MysqlService::refreshUserTableTotal($userid);
        }

        //刷新Redis
        RedisService::cachingUserTodayNum($userid);
        RedisService::cachingUserTotalNum($userid);

        CountinService::updateTotalNum($num);

        if (StageGXService::isInStage()) {
            StageGXService::addCurStageGXCompletionNum($num);
        }

        RedisService::cachingCurMonthRanklist();
        RedisService::cachingTodayRanklist();
        RedisService::cachingTotalRanklist();

        echoSuccess("报数成功!");
    }

    /**
     * 补报数目
     */
    public function supplementNum(){
        $userid = I("userid");
        $date = I("date");
        $num = I("num");

        CheckService::checkApiParam("userid", $userid);
        CheckService::checkApiParam("date", $date, "yyyy-mm-dd");
        CheckService::checkApiParam("num", $num);

        if( 0 == intval($num) ){
            echoError("补报数目为0!");
        }

        if (!CountinService::isSupplementDateLegeal($date)) {
            echoError("补报失败,补报日期须为今天之前!");
        }

        $dayCount = M("day_count")->where("userid='$userid' and today_date='$date'")->find();
        DebugService::displayLog("dayCount:");
        DebugService::displayLog($dayCount);
        if( $dayCount ){//如果当天进行过报数
            if( !MysqlService::addSupplementMysqlDayNum($userid, $num, $date ) ){
                echoError("更新日计数表失败!");
            }
        }else{
            if( !MysqlService::insertSupplementMysqlDayNum($userid, $num, $date) ){
                echoError("插入日计数表失败!");
            }
        }

        CountinService::updateTotalNum($num);//这里最好用redisservice

        if( StageGXService::isInStage($date) ){
            StageGXService::addCurStageGXCompletionNum($num);
        }

        //如果更新user表失败,重新统计total
        if( !MysqlService::addMysqlUserTotalNum($userid,$num) ){
            MysqlService::refreshUserTableTotal($userid);
        }

        RedisService::cachingUserTotalNum($userid);

        //如果是过去的月份,更新月排行,并做缓存
        if( DateService::isYearMonthDayInPassedMonth($date) ){
            $yearMonth = DateService::yearMonthDay2YearMonth($date);
            MysqlService::refreshMysqlMonthRanklist($yearMonth);
            RedisService::cachingMonthRanklist($yearMonth);
        }

        //更新并缓存日排行
        MysqlService::refreshMysqlSomeDayRanklist($date);
        RedisService::cachingSomedayRanklist($date);

        echoSuccess("补报成功!");
    }

    /**
     * 返回今日总数和全部总数
     */
    public function getUserCurNums(){
        $id = I("userid");
        CheckService::checkApiParam("userid", $id);
        $data['totalNum'] = CountinService::getUserTotalNumById($id);
        $data['todayNum'] = CountinService::getUserTodayNumById($id);
        if(  $data['totalNum'] == null ){
            echoError("用户不存在!");
        }else{
            if( $data['todayNum'] == null ){
                $data['todayNum'] = 0;
                echoSuccess("获取今日数目和总数成功!", $data);
            }
        }
    }

}