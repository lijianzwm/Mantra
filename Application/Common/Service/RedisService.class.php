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

//////////////////////////////////////////////////////////////////

    public static function getRedisUserTodayNum($userid){
        $todayKey = RedisKeyService::getUserTodayNumKey($userid);
        return self::get($todayKey);
    }

    public static function setRedisUserTodayNum($userid,$num){
        $todayKey = RedisKeyService::getUserTodayNumKey($userid);
        self::set($todayKey,$num,C("TODAY_NUM_EXPIRE"));
    }

    /**
     * 从Mysql中缓存用户今日共修数目，返回数目，若用户不存在，返回null
     * @param $userid
     * @return int|null
     */
    public static function cachingUserTodayNum($userid){
        $num = MysqlService::getMysqlTodayNumById($userid);
        if( $num == null ){//如果day_count中没有记录
            DebugService::displayLog("day_count没有记录！");
            if( MysqlService::isUserExist($userid)){//该用户今天没有进行报数
                $num = 0;
                MysqlService::initMysqlUserTodayNum($userid);
            }else{//用户不存在
                return null;
            }
        }else{
            DebugService::displayLog("day_count有记录:$num");
        }
        self::setRedisUserTodayNum($userid, $num);
        return $num;
    }

//////////////////////////////////////////////////////////////////

    public static function getRedisUserTotalNum($userid){
        $userTotalKey = RedisKeyService::getUserTotalNumKey($userid);
        $userTotalNum = self::get($userTotalKey);
        if( $userTotalNum == false ){
            $userTotalNum = self::cachingUserTotalNum($userid);
        }
        return $userTotalNum;
    }

    public static function setRedisUserTotalNum($userid, $num){
        $userTotalKey = RedisKeyService::getUserTotalNumKey($userid);
        self::set($userTotalKey, $num, C("TOTAL_NUM_EXPIRE"));
    }

    /**
     * 如果用户存在，缓存total，若用户不存在，返回null
     * @param $userid
     * @return null
     */
    public static function cachingUserTotalNum($userid){
        $user = M("user")->where("id='$userid'")->find();
        if( $user ){
            $num = $user['total'];
        }else{
            return null;
        }
        self::setRedisUserTotalNum($userid, $num);
        return $num;
    }

//////////////////////////////////////////////////////////////////

    public static function getRedisStageGXTotalNum(){
        $key = RedisKeyService::getStageGXKey();
        return self::get($key);
    }

    public static function setRedisStageGXTotalNum($num){
        $key = RedisKeyService::getStageGXKey();
        self::set($key, $num, C("STAGE_GX_TOTAL_NUM_EXPIRE"));
    }

    public static function cachingStageGXTotalNum($stageGX){
        $stageGXTotalNum = MysqlService::getStageGXTotalNum($stageGX);
        DebugService::displayLog("cachingStageGXTotalNum()\tstageGXTotalNum=" . $stageGXTotalNum);
        if( !$stageGX ){
            $stageGXTotalNum = 0;
        }
        self::setRedisStageGXTotalNum($stageGXTotalNum);
        return $stageGXTotalNum;
    }

//////////////////////////////////////////////////////////////////

    /**
     * 获取全部数目
     * @return mixed
     */
    public static function getRedisTotalNum(){
        $key = RedisKeyService::getTotalNumKey();
        return self::get($key);
    }

    public static function setRedisTotalNum($num){
        $key = RedisKeyService::getTotalNumKey();
        self::set($key,$num,C("TOTAL_NUM_EXPIRE"));
    }

    public static function cachingTotalNum(){
        $num = MysqlService::generateMysqlTotalNum();
        self::setRedisTotalNum($num);
        return $num;
    }

//////////////////////////////////////////////////////////////////

    /**
     * 获取redis中存的今日排行榜，如果没有，返回false
     * @return mixed|null
     */
    public static function getRedisTodayRanklist(){
        $key = RedisKeyService::getTodayRanklistKey();
        return self::get($key);
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

//////////////////////////////////////////////////////////////////

    public static function getRedisSomeDayRanklist($date){
        $key = RedisKeyService::getSomedayRanklistKey($date);
        return  self::get($key);
    }

    public static function cachingSomedayRanklist($date){
        $ranklist = MysqlService::getMysqlSomedayRanklist($date);
        $key = RedisKeyService::getSomedayRanklistKey($date);
        self::set($key, $ranklist, C("SOMEDAY_RANKLIST_EXPIRE"));
        return $ranklist;
    }

//////////////////////////////////////////////////////////////////

    /**
     * 获取缓存中的总排行，如果没有，返回false
     * @return mixed
     */
    public static function getRedisTotalRanklist(){
        $key = RedisKeyService::getTotalRanklistKey();
        return  self::get($key);
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

//////////////////////////////////////////////////////////////////

    public static function getRedisCurMonthRanklist(){
        $key = RedisKeyService::getCurMonthRanklistKey();
        return self::get($key);
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

//////////////////////////////////////////////////////////////////

    public static function getRedisMonthRanklist($yearMonth){
        $key = RedisKeyService::getMonthRanklistKey($yearMonth);
        return self::get($key);
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

//////////////////////////////////////////////////////////////////

    /**
     * 更新redis当日数目缓存，如果当日没有缓存，则新建一个
     * @param $userid
     * @param $num
     */
    public static function addRedisTodayNum( $userid, $num ){
        $todayKey = RedisKeyService::getUserTodayNumKey($userid);
        $currentNum = self::getRedisUserTodayNum($userid);
        if( $currentNum == false ){
            $currentNum = self::cachingUserTodayNum($userid);
            //这里缓存完了不用再加$num了，因为前面sql数据库中已经加过了
            self::set($todayKey, $currentNum, C("TODAY_KEY_EXPIRE"));
        }else{
            $num = $num+$currentNum;
            self::set($todayKey,$num, C("TODAY_KEY_EXPIRE"));
        }
    }

    /**
     * 更新redis中用户total，输入保证userid有效
     * @param $userid
     * @param $num
     */
    public static function addRedisUserTotalNum($userid, $num ){
        $totalKey = RedisKeyService::getUserTotalNumKey($userid);
        $currentNum = self::getRedisUserTotalNum($userid);

        self::set($totalKey,$currentNum+$num, C("TODAY_KEY_EXPIRE"));
    }

    public static function addRedisUserTodayNum(){

    }

    public static function addRedisMonthTotalNum(){

    }
    
    


    public static function updateTotalNum($num){
        $key = RedisKeyService::getTotalNumKey();
        self::set($key, $num, C("TOTAL_NUM_EXPIRE"));
    }



    public static function updateStageTotalNum($num){
        $key = RedisKeyService::getStageGXKey();
        self::set($key,$num,C("STAGE_GX_TOTAL_NUM_EXPIRE"));
    }

    public static function insertNum($userid, $num, $date=null){
        if( $date == null ){
            $date = DateService::getCurrentYearMonthDay();
            self::addRedisTodayNum($userid, $num);
        }
        self::addRedisUserTotalNum($userid, $num);

    }

    public static function addNum($userid, $num, $date=null){

    }

}

