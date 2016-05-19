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

        self::checkParams($id, $num);

        if( session("userid") && $id != session("userid")){
            echoError("师兄别闹,不要乱传入别人的userid,如果不是故意捣乱的就请重新登录后再进行报数!");
        }

        if( CountinService::addTodayNum($id,$num) ){
            echoSuccess("报数成功!");
        }else{
            echoError("用户不存在!");
        }
    }

    /**
     * 补报数目
     */
    public function supplementNum(){
        $userid = I("userid");
        $date = I("date");
        $num = I("num");

        self::checkParams($userid, $num, $date);

        if (!CountinService::isSupplementDateLegeal($date)) {
            echoError("补报失败,补报日期须为今天之前!");
        }

        if( CountinService::supplementNumByUserid($userid, $date, $num) ){
            echoSuccess("补报成功!");
        }else{
            echoError("补报失败!");
        }
    }

    /**
     * 返回今日总数和全部总数
     */
    public function getUserCurNums(){
        $id = I("userid");
        self::checkParams($id);
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

    /**
     * 检查传过来的参数是否合法
     * @param $userid
     * @param $num
     * @param $date
     */
    private function checkParams($userid, $num=null, $date=null){
        $checkUserid = CheckService::checkUseridFormat($userid);
        if( $checkUserid['status'] ){
            echoError($checkUserid['msg']);
        }

        if( $num != null ){
            $checkNum = CheckService::checkNumFormat($num);
            if( $checkNum['status'] ){
                echoError($checkNum['msg']);
            }
        }

        if( $date != null ){
            $checkDate = CheckService::checkDateFormat($date, "yyyy-mm-dd");
            if( $checkDate['status'] ){
                echoError($checkDate['msg']);
            }
        }
    }

}