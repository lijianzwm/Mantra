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
     * 判断当前时间是否处于阶段性共修中,输入保证$date的格式
     * @return bool
     */
    public static function isInStage( $date=null ){
        if( $date == null ){
            $date = DateService::getCurrentYearMonthDay();
        }
        if( M("stage_gx")->where("beg_date >= '$date' and end_date <= '$date'")->find()){
            return true;
        }else{
            return false;
        }
    }

    public static function getCurStageGX(){
        $todayDate = DateService::getCurrentYearMonthDay();
        $stageGX = M("stage_gx")->where("beg_date <= '$todayDate' and end_date >= '$todayDate'")->find();
        DebugService::displayLog("getStageGX()\ttodayDate=".$todayDate);
        DebugService::displayLog("getStageGX()\tstageGX=".$stageGX);
        return $stageGX;
    }

    public static function getCurStageGXNum(){
        //TODO
        $num = RedisService::getCurStageGXNum();
        return $num;
    }

    public static function getStageGX($stageGXId){
        return M("stage_gx")->where("id='$stageGXId'")->find();
    }

    public static function addCurStageGXCompletionNum($num){
        $curStageGX = self::getCurStageGX();
        $curStageGX['completion_num'] += $num;
        if (M("stage_gx")->save($curStageGX)) {
            return true;
        }else{
            return false;
        }
    }

    public static function refreshStageGxCompletionNum($stageGXId){
        $stageGX = self::getStageGX($stageGXId);
        $stageGX['completion_num'] = MysqlService::generateStageGXCompletionNum($stageGX);
        M("stage_gx")->save($stageGX);
        return true;
    }

}