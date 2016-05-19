<?php

/**
 * Created by PhpStorm.
 * User: lijian
 * Date: 2016/4/12
 * Time: 8:49
 */

namespace Common\Service;

class CountinService{

    /**
     * 获取用户所有数目，若该用户不存在，返回null
     * @param $userid
     * @return mixed|null
     */
    public static function getUserTotalNumById($userid){
        $num = RedisService::getRedisUserTotalNumById($userid);
        if( $num == false ){//这里不用!$num，因为缓存的数据有可能是0
            $num = RedisService::cachingUserTotalNum($userid);
        }
        return $num;
    }

    /**
     * 获取用户当日的数目,若用户不存在或没有进行过报数,返回null
     * @param $userid
     * @return mixed
     */
    public static function getUserTodayNumById($userid){
        $num = RedisService::getRedisUserTodayNumById($userid);
        if( $num == false ){
            DebugService::displayLog("getTodayNumById : not cached");
            $num = MysqlService::getMysqlTodayNumById($userid);
        }
        if( $num == null ){
            return 0;
        }else{
            return $num;
        }
    }

    /**
     * 添加本日数目，用户不存在返回false
     * @param $userid
     * @param $num
     * @return bool
     */
    public static function addTodayNum($userid, $num ){
        //TODO 判断是否添加计数成功

        if ( !MysqlService::isUserExist($userid)) {
            return false;
        }
        if( CountinService::isTodayFirstCommit($userid)){//如果是当天第一次报数
            DebugService::displayLog("当前报数是当天第一次报数");
            MysqlService::insertMysqlTodayNum($userid, $num);
        }else{
            DebugService::displayLog("当前报数不是当天第一次报数");
            MysqlService::addMysqlTodayNum($userid, $num);
        }
        self::addTotalNum($num);
        self::addStageTotalNum($num);
        RedisService::addRedisTodayNum($userid, $num);
        RedisService::addRedisUserTotalNum($userid,$num);
        MysqlService::addMysqlUserTotalNum($userid,$num);
        return true;
    }
    
    

    private static function addTotalNum($num){
        $totalNum = self::getAllUserTotalNum();
        RedisService::updateTotalNum($totalNum + $num);
    }

    private static function addStageTotalNum($num){
        $totalNum = RedisService::getRedisStageGXTotalNum();
        RedisService::updateStageTotalNum($totalNum + $num);
    }

    /**
     * 获取全部用户全部共修数目
     * @return int|mixed
     */
    public static function getAllUserTotalNum(){
        $totalNum = RedisService::getRedisTotalNum();
        if( $totalNum == false ){
            $totalNum = RedisService::cachingTotalNum();
        }
        return $totalNum;
    }

    public static function isTodayFirstCommit( $userid ){
        if( RedisService::getRedisUserTodayNumById($userid) == false){
            if( MysqlService::getMysqlTodayNumById($userid) == null){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public static function getRanklistTotalNum($ranklist){
        $sum = 0;
        foreach($ranklist as $r ){
            $sum += $r['num'];
        }
        return $sum;
    }

    /**
     * 判断计数数字是否合法，0算合法数字
     * @param $num
     * @return bool
     */
    public static function isCountNumLegal($num){
        if( is_numeric( $num ) && !strpos($num, ".") ){
            if( intval($num) >= 0 ){
                return true;
            }
        }
        return false;
    }

    /**
     * 计算完成度
     * @param $num
     * @param $total
     * @return float
     */
    public static function calPercent($num, $total){
        $percent = $num/$total;
        if( $percent >= 1 ){
            return "已完成";
        }
        return round($percent*100,2)."%";
    }

    public static function supplementNumByPhone($phone, $date, $num ){
        $user = UserService::getUserByPhone($phone);
        if( !$user ){
            DebugService::displayLog($phone."所对应的用户不存在");
            return false;
        }
        $userid = $user['id'];
        return self::supplementNumByUserid($userid, $date, $num );
    }

    /**
     * 补报数目,更新总数,个人数,日排行,月排行
     * @param $userid
     * @param $date
     * @param $num
     * @return bool
     */
    public static function supplementNumByUserid( $userid, $date, $num ){
        if( !self::isCountNumLegal($num) ){
            DebugService::displayLog("补报数目num非法".$num);
            return false;
        }
        $date = trim($date);
        if( !DateService::checkYearMonthDay($date)){
            DebugService::displayLog("date输入格式不合法!");
            return false;
        }
        if( !self::isSupplementDateLegeal($date) ){
            DebugService::displayLog("补报数目日期非法!");
            return false;
        }

        DebugService::displayLog("userid=".$userid);
        DebugService::displayLog("date=" . $date);
        $dayCount = M("day_count")->where("userid='$userid' and today_date='$date'")->find();
        DebugService::displayLog("dayCount:");
        DebugService::displayLog($dayCount);
        if( $dayCount ){
            if( !MysqlService::addSupplementMysqlDayNum($userid, $num, $date ) ){
                return false;
            }
        }else{
            if( !MysqlService::insertSupplementMysqlDayNum($userid, $num, $date) ){
                return false;
            }
        }

        //如果补报日期属于阶段性共修,更新阶段性共修数目
        if( StageGXService::isInStage($date) ){
            self::addStageTotalNum($num);
        }

        //更新用户表总数
        RedisService::addRedisUserTotalNum($userid,$num);
        MysqlService::addMysqlUserTotalNum($userid,$num);

        //如果是过去的月份,更新月排行,并做缓存
        if( DateService::isYearMonthDayInPassedMonth($date) ){
            $yearMonth = DateService::yearMonthDay2YearMonth($date);
            MysqlService::refreshMysqlMonthRanklist($yearMonth);
            RedisService::cachingMonthRanklist($yearMonth);
        }

        //更新并缓存日排行
        MysqlService::refreshMysqlSomeDayRanklist($date);
        RedisService::cachingSomedayRanklist($date);

        RedisService::cachingTotalNum();
        return true;
    }

    public static function isSupplementDateLegeal($date){
        $curYearMonthDay = DateService::getStrYearMonthDay();
        if( $date >= $curYearMonthDay ){
            return false;
        }else{
            return true;
        }
    }

}