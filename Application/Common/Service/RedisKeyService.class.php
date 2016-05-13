<?php
/**
 * Created by PhpStorm.
 * User: lijian
 * Date: 2016/4/17
 * Time: 11:38
 */

namespace Common\Service;

/**
 * 获取redis的key
 * Class RedisKeyService
 * @package Common\Service
 */
class RedisKeyService{

    public static function getUserTodayNumKey($userid ){
        return "today-num-".DateService::getStrMonthDay()."-".$userid;
    }

    public static function getUserTotalNumKey($userid ){
        return "user-total-num-".$userid;
    }

    public static function getTodayRanklistKey(){
        return "ranklist-today-".DateService::getStrMonthDay();
    }

    public static function getTotalRanklistKey(){
        return "ranklist-total";
    }

    public static function getSomedayRanklistKey($date){
        return "ranklist-".$date;
    }

    public static function getCurMonthRanklistKey(){
        return "ranklist-".DateService::getCurrentYearMonth();
    }

    public static function getMonthRanklistKey($yearMonth){
        return "ranklist-".$yearMonth;
    }

    public static function getDayTotalNumKey($date){
        return "total-".$date;
    }

    public static function getTotalNumKey(){
        return "total-num";
    }

    public static function getMonthTotalNumKey($yearMonth){
        return "total-" . $yearMonth;
    }

    public static function getStageGXKey(){
        return "stage-total-num";
    }

}