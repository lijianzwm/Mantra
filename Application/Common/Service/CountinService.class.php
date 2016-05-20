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
        return RedisService::getRedisUserTotalNum($userid);
    }

    /**
     * 获取用户当日的数目,若用户不存在返回null
     * @param $userid
     * @return mixed
     */
    public static function getUserTodayNumById($userid){
        $num = RedisService::getRedisUserTodayNum($userid);
        if( $num == false ){
            $num = RedisService::cachingUserTodayNum($userid);
        }
        return $num;
    }

    public static function updateTotalNum($num){
        $totalNum = RedisService::getRedisTotalNum();
        if( $totalNum == false ){
            RedisService::cachingTotalNum();//这里直接缓存不+$num目是因为mysql中数目已经更新完毕了
        }else{
            RedisService::updateTotalNum($totalNum + $num);
        }

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
        if( RedisService::getRedisUserTodayNum($userid) == false){
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

    public static function isSupplementDateLegeal($date){
        $curYearMonthDay = DateService::getStrYearMonthDay();
        if( $date >= $curYearMonthDay ){
            return false;
        }else{
            return true;
        }
    }

}