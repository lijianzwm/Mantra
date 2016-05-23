<?php
/**
 * Created by PhpStorm.
 * User: lijian
 * Date: 2016/4/13
 * Time: 14:27
 */

namespace Home\Controller;


use Common\Service\CountinService;
use Common\Service\DateService;
use Common\Service\DebugService;
use Think\Controller;
use Common\Service\RanklistService;

class RanklistController extends Controller{

    public function _initialize(){
        $this->assign("yourUserid", session("userid"));
        $this->assign("monthDay", DateService::getCurrentYearMonth());
        $this->assign("yearMonthDay", DateService::getCurrentYearMonthDay());
    }

    public function todayRanklist(){
        $ranklist = RanklistService::getTodayRanklist();
        $total = CountinService::getRanklistTotalNum($ranklist);
        $this->assign("title", "今日排行榜");
        $this->assign("total", $total);
        $this->assign("refreshTime", C("TODAY_RANKLIST_EXPIRE"));
        $this->assign("ranklist", $ranklist);
        $this->display("ranklist");
    }

    public function dayRanklist(){
        $date = I("date");
        if( !DateService::checkYearMonthDay($date) ){
            $this->error("日期格式错误!");
        }
        $todayDate = DateService::getStrYearMonthDay();

        DebugService::displayLog("monthRanklist():date=" . $date);
        DebugService::displayLog("monthRanklist():todayDate=" . $todayDate);

        if( $date == $todayDate ){
            $ranklist = RanklistService::getTodayRanklist();
        }else{
            $ranklist = RanklistService::getSomedayRanklist($date);
        }
        $total = CountinService::getRanklistTotalNum($ranklist);
        $this->assign("title", $date);
        $this->assign("refreshTime", C("SOMEDAY_RANKLIST_EXPIRE"));
        $this->assign("total", $total);
        $this->assign("ranklist", $ranklist);
        $this->display("ranklist");
    }

    public function monthRanklist(){
        $year = I("year");
        $month = I("month");
        $yearMonth = $year."-".$month;
        if( !DateService::checkYearMonth($yearMonth)){
            $this->error("日期格式错误!");
        }

        $curYearMonth = DateService::getStrYearMonth();
        DebugService::displayLog("monthRanklist():yearMonth=" . $yearMonth);
        DebugService::displayLog("monthRanklist():curYearMonth=" . $curYearMonth);

        if( $yearMonth == $curYearMonth ){
            $ranklist = RanklistService::getCurMonthRanklist();
        }else{
            $ranklist = RanklistService::getMonthRanklist($yearMonth);
        }
        $total = CountinService::getRanklistTotalNum($ranklist);
        $this->assign("total", $total);
        $this->assign("title", $yearMonth);
        $this->assign("refreshTime", C("MONTH_RANKLIST_EXPIRE"));
        $this->assign("ranklist", $ranklist);
        $this->display("ranklist");
    }

    public function totalRanklist(){
        $ranklist = RanklistService::getTotalRanklist();
        $total = CountinService::getAllUserTotalNum();
        $this->assign("total", $total);
        $this->assign("title","总排行榜");
        $this->assign("refreshTime", C("TOTAL_RANKLIST_EXPIRE"));
        $this->assign("ranklist", $ranklist);
        $this->display("ranklist");
    }

    public function yesterdayRanklist(){
        $ranklist = RanklistService::getYesterdayRanklist();
        $total = CountinService::getRanklistTotalNum($ranklist);
        $this->assign("total", $total);
        $this->assign("title","昨日排行榜");
        $this->assign("refreshTime", C("SOMEDAY_RANKLIST_EXPIRE"));
        $this->assign("ranklist", $ranklist);
        $this->display("ranklist");
    }

    public function curMonthRanklist(){
        $ranklist = RanklistService::getCurMonthRanklist();
        $total = CountinService::getRanklistTotalNum($ranklist);
        $this->assign("total", $total);
        $this->assign("title","本月排行榜");
        $this->assign("refreshTime", C("CUR_MONTH_RANKLIST_EXPIRE"));
        $this->assign("ranklist", $ranklist);
        $this->display("ranklist");
    }

}