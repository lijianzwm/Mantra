<?php
/**
 * Created by PhpStorm.
 * User: lijian
 * Date: 16/5/13
 * Time: 下午4:18
 */

namespace Common\Service;


/**
 * 阶段性共修Service
 * Class StageGXService
 * @package Common\Service
 */
class StageGXService{

    /**
     * 判断当前时间是否处于阶段性共修中
     * @return bool
     */
    public static function isInStage(){
        $todayDate = DateService::getCurrentYearMonthDay();
        if( M("stage_gx")->where("beg_date >= '$todayDate' and end_date <= '$todayDate'")->find()){
            return true;
        }else{
            return false;
        }
    }

    public static function getStageGX(){
        $todayDate = DateService::getCurrentYearMonthDay();
        $stageGX = M("stage_gx")->where("beg_date >= '$todayDate' and end_date <= '$todayDate'")->find();
        return $stageGX;
    }

    public static function getStageGXTotalNum($stageGX){
        $stageTotalNum = RedisService::getRedisStageGXTotalNum();
        if( $stageTotalNum == false ){
            $stageTotalNum = RedisService::cachingStageGXTotalNum($stageGX);
        }
        return $stageTotalNum;
    }

}