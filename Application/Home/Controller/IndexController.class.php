<?php
namespace Home\Controller;
use Common\Service\CountinService;
use Common\Service\StageGXService;
use Think\Controller;
use Common\Service\DateService;

class IndexController extends Controller {
    public function index(){
        if( session("userid")){
            $this->assign("isLogin", 1);
        }else{
            $this->assign("isLogin", 0);
        }

        $todayDate = DateService::getStrDate();
        $startTime = $todayDate." ".C("GX_START_TIME");

        $currentTime = DateService::getCurrentTime();

        $videoTimeLength = intval(C("GX_VIDEO_TIME_LENGTH"));//共修视频时长多少秒

        $seconds = intval(DateService::timeDistance($currentTime, $startTime));
        $readyTime = intval(C("GX_READY_TIME")*60);//分钟*秒数

        $state = "end";

        if( $seconds <= 0 ){//在共修开始之前
            if ($seconds < 0 && $seconds > -$readyTime) {//如果在准备时间内
                $this->assign("countdownSeconds", -$seconds);
                $state = "ready";
            }
        }else{
            if( $seconds <= $videoTimeLength ){//如果在共修时
                $this->assign("begTime", $seconds);
                $state = "started";
            }
        }

        $stageGX = StageGXService::getCurStageGX();
        if( $stageGX ){
            $stageGXTotalNum = $stageGX['completion_num'];
            $stageGXPercent = $stageGXTotalNum / floatval($stageGX['num']) * 100;
            $stageGXRequireNum = intval($stageGX['num']) - $stageGXTotalNum;
            $stageGXRequireNum = $stageGXRequireNum < 0 ? 0 : $stageGXRequireNum;
            $stageGXPercent = round($stageGXPercent ,3);
            $this->assign("stageGX", $stageGX);
            $this->assign("stageGXRequireNum", $stageGXRequireNum);
            $this->assign("stageGXTotalNum", $stageGXTotalNum);
            $this->assign("stageGXPercent", $stageGXPercent);
        }

        $totalNum = CountinService::getAllUserTotalNum();
        $this->assign("totalNum", $totalNum);
        $this->assign("state", $state);
        $this->display();
    }

    public function yigui(){
        $this->display();
    }

    public function guide(){
        layout(false);
        $this->display();
    }

}