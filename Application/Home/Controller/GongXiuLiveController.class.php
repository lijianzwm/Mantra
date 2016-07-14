<?php
/**
 * Created by PhpStorm.
 * User: lijian
 * Date: 16/7/14
 * Time: 上午10:17
 */

namespace Home\Controller;


use Think\Controller;
use Common\Service\DateService;

class GongXiuLiveController extends Controller{

    public function index(){
        layout(false);
        $todayDate = DateService::getStrDate();
        $everyDayStartTime = C("GX_START_TIME");
        $startTime = $todayDate." ".C("GX_START_TIME");

        $currentTime = DateService::getCurrentTime();

        $totalTime = intval(C("GX_VIDEO_TIME_LENGTH"));//共修视频时长多少秒

        $seconds = intval(DateService::timeDistance($currentTime, $startTime));
        $readyTime = intval(C("GX_READY_TIME")*60);//分钟*秒数

        $state = "end";

        if( $seconds <= 0 ){//在共修开始之前
            if ($seconds < 0 && $seconds > -$readyTime) {//如果在准备时间内
                $this->assign("countdownSeconds", -$seconds);
                $state = "ready";
            }
        }else{
            if( $seconds <= $totalTime ){//如果在共修时
                $this->assign("begTime", $seconds);
                $state = "started";
            }
        }
        $this->assign("state", $state);
        $this->assign("totalTime", $totalTime);
        $this->assign("everyDayStartTime", $everyDayStartTime);
        $this->display();
    }
}
