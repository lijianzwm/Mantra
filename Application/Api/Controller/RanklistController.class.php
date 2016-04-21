<?php
/**
 * Created by PhpStorm.
 * User: lijian
 * Date: 2016/4/21
 * Time: 11:52
 */

namespace Api\Controller;

use Common\Service\RanklistService;

class RanklistController extends CommonController{
    public function getTodayRanklist(){
        $ranklist = RanklistService::getTodayRanklist();
        $ret['ranklist'] = $ranklist;
        $ret['error_code'] = 0;
        echo json_encode($ret);
    }
}