<?php
/**
 * Created by PhpStorm.
 * User: lijian
 * Date: 2016/4/17
 * Time: 11:42
 */

namespace Common\Service;


class MysqlService{

    /**
     * 获取当日排行榜，如果没有，返回array(0) {}
     * @return mixed
     */
    public static function getMysqlTodayRanklist(){
        $todayDate = DateService::getStrDate();
        return self::generateMysqlSomeDayRanklist($todayDate);
    }

    /**
     * 获取某天的排行榜，如果这个排行榜不存在，就生成一个，存到month_ranklist表中，如果排行榜为空，返回array(0) {}
     * @param $date
     * @return mixed
     */
    public static function getMysqlSomedayRanklist($date){
        $rankItem = M("day_ranklist")->where("date='$date'")->find();
        if( $rankItem ){
            return json_decode($rankItem['ranklist'], true);
        }else{
            $ranklist = self::generateMysqlSomeDayRanklist($date);
            $rankItem['total'] = CountinService::getRanklistTotalNum($ranklist);
            $rankItem['ranklist'] = json_encode($ranklist);
            $rankItem['date'] = $date;
            M("day_ranklist")->add($rankItem);
            return $ranklist;
        }
    }

    /**
     * 获取总排行，如果没有，返回array(0) {}
     * @return mixed
     */
    public static function getMysqlTotalRanklist(){
        return M("user")->field("id as userid, showname as name, total as num")->where("total > 0")->order("total desc")->select();
    }

    /**
     * 生成某日排行榜，若生成的排行榜为空，返回array(0) {}
     * @param $date
     * @return mixed
     */
    public static function generateMysqlSomeDayRanklist($date){
        $userTable = C("DB_PREFIX")."user";
        $dayCountTable = C("DB_PREFIX")."day_count";
        $ranklist = M()->table("$userTable user, $dayCountTable count")->
        where("user.id = count.userid and count.num > '0' and  count.today_date='$date'")->
        field('user.id as userid, user.showname as name, count.num as num')->
        order('count.num desc' )->select();
        return $ranklist;
    }

    /**
     * 获取当月排行榜
     * @return mixed
     */
    public static function getMysqlCurMonthRanklist(){
        $yearMonth = DateService::getCurrentYearMonth();
        $ranklist = self::generateMysqlMonthRanklist($yearMonth);
        return $ranklist;
    }

    /**
     * 获取某月排行榜
     * @return mixed
     */
    public static function getMysqlMonthRanklist($yearMonth){
        $rankItem = M("month_ranklist")->where("yearmonth='$yearMonth'")->find();
        if( $rankItem ){
            return json_decode($rankItem['ranklist'],true);
        }else{
            $ranklist = self::generateMysqlMonthRanklist($yearMonth);
            $rankItem['total'] = CountinService::getRanklistTotalNum($ranklist);
            $rankItem['yearmonth'] = $yearMonth;
            $rankItem['ranklist'] = json_encode($ranklist);
            M("month_ranklist")->add($rankItem);
            return $ranklist;
        }
    }

    /**
     * 生成月排行榜，传入“2016-04”格式的年月字符串，返回排行榜，若排行榜为空，则返回array(0) {}
     * @param $yearMonth
     * @return mixed
     */
    public static function generateMysqlMonthRanklist($yearMonth){
        $begDate = $yearMonth."-01";
        $endDate = $yearMonth."-31";
        DebugService::displayLog("generateMysqlMonthRanklist: begDate=$begDate, endDate=$endDate");
        $userTable = C("DB_PREFIX")."user";
        $dayCountTable = C("DB_PREFIX")."day_count";
        $sql = "SELECT
                    USER .id AS userid,
                    USER .showname AS NAME,
                    count.num AS num
                FROM
                    $userTable USER,
                    (
                        SELECT
                            userid,
                            sum(num) AS num
                        FROM
                            $dayCountTable
                        WHERE
                            today_date >= '$begDate'
                        AND today_date <= '$endDate'
                        GROUP BY
                            userid
                    ) count
                WHERE
                    USER .id = count.userid
                AND count.num > '0'
                ORDER BY
                    count.num DESC";
        DebugService::displayLog($sql);
        return M()->query($sql);
    }

    /**
     * 获取用户所有数目，如果用户不存在，返回null
     * @param $userid
     * @return null
     */
    public static function getMysqlTotalNumById($userid){
        $user = M("user")->where("id='$userid'")->find();
        if( $user ){
            return $user['total'];
        }else{
            return null;
        }
    }

    /**
     * 获取mysql中用户当日数目，如果用户不存在或当日没有进行报数，返回null
     * @param $userid
     * @return null
     */
    public static function getMysqlTodayNumById($userid){
        $todayDate = DateService::getStrDate();
        $count = M("day_count")->where("userid='$userid' and today_date='$todayDate'")->find();
        DebugService::displayLog($count);
        if( $count ){
            return $count['num'];
        }else{
            return null;
        }
    }

    public static function addMysqlTodayNum( $userid, $num ){
        $dao = M();
        $todayDate = DateService::getStrDate();
        $table = C("DB_PREFIX")."day_count";
        if( $dao->execute("update $table set num=num+$num where userid='$userid' AND today_date='$todayDate'") ){
            return true;
        }else{
            return false;
        }
    }

    public static function isUserExist($userid){
        if( M("user")->where("id=$userid")->find() ){
            return true;
        }else{
            return false;
        }
    }

    public static function initMysqlUserTodayNum($userid){
        self::insertMysqlTodayNum($userid, 0);
    }

    public static function insertMysqlTodayNum( $userid, $num ){
        $count['userid'] = $userid;
        $count['num'] = $num;
        $count['today_date'] = DateService::getStrDate();
        if( M("day_count")->add($count) ){
            return true;
        }else{
            return false;
        }
    }

    public static function addMysqlUserTotalNum($userid, $num ){
        $dao = M();
        $table = C("DB_PREFIX")."user";
        if( $dao->execute("update $table set total=total+$num where id='$userid'") ){
            return true;
        }else{
            return false;
        }
    }

    public static function generateMysqlDayTotalNum($date){
        $num = M("day_count")->field("sum(num) as num")->where("today_date='$date'")->select();
        if( $num ){
            DebugService::displayLog("generateMysqlDayTotalNum:num = $num[0]['num']");
            return $num[0]['num'];
        }else{
            DebugService::displayLog("generateMysqlDayTotalNum:num = not data");
            return 0;
        }
    }

    public static function generateMysqlMonthTotalNum($yearMonth){
        $begDate = $yearMonth."-01";
        $endDate = $yearMonth."-31";
        $num = M("day_count")->field("sum(num) as num")->where("today_date>='$begDate' and today_date <= '$endDate'")->select();
        if( $num ){
            return $num[0]['num'];
        }else{
            DebugService::displayLog("generateMysqlMonthTotalNum : num = not data");
            return 0;
        }
    }

    /**
     * 获取某一天（如果是今天的话，查询不到，返回-1）的共修总数，若数据库中没有，返回-1
     * @param $date
     * @return int
     */
    public static function getMysqlDayTotalNum($date){
        $rankItem = M("day_ranklist")->where("date=$date")->find();
        if( $rankItem ){
            return $rankItem['total'];
        }else{
            return -1;
        }
    }

    /**
     * 获取month_ranklist中存的总数，如果不存在，返回-1
     * @param $date
     * @return int
     */
    public static function getMysqlMonthTotalNum($yearMonth){
        $rankItem = M("month_ranklist")->where("yearmonth='$yearMonth'")->find();
        if( $rankItem ){
            return $rankItem['total'];
        }else{
            return -1;
        }
    }

    public static function generateMysqlTotalNum(){
        $totalNum = 0;
        $result = M("month_ranklist")->field("sum(total) as num")->select();
        if( $result ){
            $totalNum += $result[0]['num'];
        }
        $curYearMonth = DateService::getCurrentYearMonth();
        $totalNum += self::generateMysqlMonthTotalNum($curYearMonth);
        return $totalNum;
    }

    public static function getStageGXTotalNum($stageGX){
        $totalNum = 0;
        $begDate = $stageGX['beg_date'];
        $endDate = $stageGX['end_date'];
        $result = M("day_count")->where("beg_date >= '$begDate' and end_date <= '$endDate'")->field("sum(num) as num")->select();
        if( $result ){
            $totalNum = $result[0]['num'];
        }
        return $totalNum;
    }

}