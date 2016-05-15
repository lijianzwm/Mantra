<?php
/**
 * Created by PhpStorm.
 * User: lijian
 * Date: 2016/4/13
 * Time: 13:52
 */

namespace Common\Service;


use Org\Util\Date;

class DateService{
    public static function getStrDate(){
        return strval(date('y-m-d',time()));
    }

    public static function getStrYesterdayDate(){
        return strval(date("Y-m-d",strtotime("-1 day")));
    }

    public static function getStrMonthDay(){
        return strval(date('m-d',time()));
    }

    public static function getStrYearMonth(){
        return strval(date('Y-m',time()));
    }

    public static function getStrYearMonthDay(){
        return strval(date('Y-m-d',time()));
    }

    public static function getCrrentTimeHis(){
        return date('H:i:s', time());
    }

    public static function getCurrentTime(){
        return date('y-m-d H:i:s', time());
    }

    public static function getCurrentYearMonth(){
        return date('Y-m',time());
    }

    public static function getCurrentYearMonthDay(){
        return date('Y-m-d',time());
    }

    public static function getCurrentYearMonthFirstday(){
        return date('Y-m',time())."-01";
    }

    public static function timeDistance($time1, $time2){
        $date1 = strtotime($time1);
        $date2 = strtotime($time2);
        return $date1-$date2;
    }

    public static function checkYearMonthDay($date){
        DebugService::displayLog("checkYearMonthDay(): ".$date);
        if( preg_match('[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]', $date) ){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 输入的$date必须保证格式为"2000-01-01",且为字符串
     * @param $date
     * @return mixed
     */
    public static function isYearMonthDayInPassedMonth($date){
        $yearMonth = substr($date, 7);
        $curYearMonth = self::getStrYearMonth();
        if( $yearMonth <= $curYearMonth ){
            return true;
        }else{
            return false;
        }
    }

    public static function isYearMonthDayInFutureMonth($date){
        $yearMonth = substr($date, 7);
        $curYearMonth = self::getStrYearMonth();
        if( $yearMonth > $curYearMonth ){
            return true;
        }else{
            return false;
        }
    }

    public static function yearMonthDay2YearMonth($date){
        return substr(strval($date), 0,7);
    }

}