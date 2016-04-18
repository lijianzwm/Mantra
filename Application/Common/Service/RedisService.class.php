<?php
/**
 * Created by PhpStorm.
 * User: lijian
 * Date: 2016/4/17
 * Time: 11:39
 */

namespace Common\Service;


class RedisService{

    public static function set($key,$value,$expire){
        S(array(
                'host'=>C("REDIS_HOST"),
                'port'=>C("REDIS_PORT"),
                'prefix'=>C("REIDS_PREFIX"),
                'expire'=>C("REDIS_EXPIRE"))
        );
        S($key, $value, $expire);
    }

    public static function get($key){
        S(array(
                'host'=>C("REDIS_HOST"),
                'port'=>C("REDIS_PORT"),
                'prefix'=>C("REIDS_PREFIX"),
                'expire'=>C("REDIS_EXPIRE"))
        );
        return S($key);
    }

    /**
     * 从Mysql中缓存用户今日共修数目，返回数目，若用户不存在，返回null
     * @param $userid
     * @return int|null
     */
    public static function cachingUserTodayNum($userid){
        $num = MysqlService::getMysqlTodayNumById($userid);
        if( $num == null ){//如果day_count中没有记录
            if( MysqlService::isUserExist($userid)){//该用户今天没有进行报数
                $num = 0;
                MysqlService::initMysqlUserTodayNum($userid);
            }else{//用户不存在
                return null;
            }
        }
        $todayKey = RedisKeyService::getUserTodayNumKey($userid);
        self::set($todayKey, $num, C("TODAY_NUM_EXPIRE"));
        return $num;
    }

    /**
     * 如果用户存在，缓存total，若不存在，返回null
     * @param $userid
     * @return null
     */
    public static function cachingUserTotalNum($userid){
        $num = MysqlService::getMysqlTotalNumById($userid);
        if( $num != null ){
            $totalKey = RedisKeyService::getUserTotalNumKey($userid);
            self::set($totalKey, $num, C("TOTAL_NUM_EXPIRE"));
        }
        return $num;
    }


    /**
     * 缓存今日排行榜，返回排行榜，如果今天没有人报数，就在缓存中存入array(0) {}
     * @return null
     */
    public static function cachingTodayRanklist(){
        $ranklist = MysqlService::getMysqlTodayRanklist();
        $key = RedisKeyService::getTodayRanklistKey();
        self::set($key,$ranklist,C("TODAY_RANKLIST_EXPIRE"));
        return $ranklist;
    }

    public static function cachingSomedayRanklist($date){
        $ranklist = MysqlService::getMysqlSomedayRanklist($date);
        $key = RedisKeyService::getSomedayRanklistKey($date);
        self::set($key, $ranklist, C("SOMEDAY_RANKLIST_EXPIRE"));
        return $ranklist;
    }

    /**
     * 缓存总排行榜，如果总排行榜为空，则存入array(0) {}
     * @return mixed
     */
    public static function cachingTotalRanklist(){
        $ranklist = MysqlService::getMysqlTotalRanklist();
        $key = RedisKeyService::getTotalRanklistKey();
        self::set($key,$ranklist,C("TOTAL_RANKLIST_EXPIRE"));
        return $ranklist;
    }

    /**
     * 缓存当月排行榜,如果当月排行榜为空，则存入array(0) {}
     * @return mixed
     */
    public static function cachingCurMonthRanklist(){
        $ranklist = MysqlService::getMysqlCurMonthRanklist();
        $key = RedisKeyService::getCurMonthRanklistKey();
        self::set($key, $ranklist,C("CUR_MONTH_RANKLIST_EXPIRE"));
        return $ranklist;
    }

    /**
     * 缓存某月排行榜，如果某月排行榜为空，存入array(0) {}
     * @param $yearMonth
     * @return mixed
     */
    public static function cachingMonthRanklist($yearMonth){
        $ranklist = MysqlService::getMysqlMonthRanklist($yearMonth);
        $key = RedisKeyService::getMonthRanklistKey($yearMonth);
        self::set($key, $ranklist,C("MONTH_RANKLIST_EXPIRE"));
        return $ranklist;
    }

    /**
     * 获取缓存中的总排行，如果没有，返回false
     * @return mixed
     */
    public static function getRedisTotalRanklist(){
        $key = RedisKeyService::getTotalRanklistKey();
        return  self::get($key);
    }

    /**
     * 获取redis中存的今日排行榜，如果没有，返回false
     * @return mixed|null
     */
    public static function getRedisTodayRanklist(){
        $key = RedisKeyService::getTodayRanklistKey();
        return self::get($key);
    }

    public static function getRedisSomeDayRanklist($date){
        $key = RedisKeyService::getSomedayRanklistKey($date);
        return  self::get($key);
    }

    public static function getRedisCurMonthRanklist(){
        $key = RedisKeyService::getCurMonthRanklistKey();
        return self::get($key);
    }

    public static function getRedisMonthRanklist($yearMonth){
        $key = RedisKeyService::getMonthRanklistKey($yearMonth);
        return self::get($key);
    }

    public static function getRedisUserTodayNumById($userid){
        $todayKey = RedisKeyService::getUserTodayNumKey($userid);
        return self::get($todayKey);
    }

    public static function getRedisTotalNumById($userid){
        $totalKey = RedisKeyService::getUserTotalNumKey($userid);
        return self::get($totalKey);
    }

    /**
     * 更新redis当日数目缓存，如果当日没有缓存，则新建一个
     * @param $userid
     * @param $num
     */
    public static function addRedisTodayNum( $userid, $num ){
        $todayKey = RedisKeyService::getUserTodayNumKey($userid);
        $currentNum = self::getRedisUserTodayNumById($userid);
        $expire = C("TODAY_KEY_EXPIRE");
        if ($currentNum != false ) {
            $num = $num+$currentNum;
        }
        self::set($todayKey,$num,$expire);
    }

    /**
     * 更新redis中用户total，输入保证userid有效
     * @param $userid
     * @param $num
     */
    public static function addRedisUserTotalNum($userid, $num ){
        $totalKey = RedisKeyService::getUserTotalNumKey($userid);
        $currentNum = self::getRedisTotalNumById($userid);
        if( $currentNum == false ){//如果redis中没有存total的数据
            $currentNum = self::cachingUserTotalNum($userid);
        }
        self::set($totalKey,$currentNum+$num, C("TODAY_KEY_EXPIRE"));
    }

}

