<?php
/**
 * Created by PhpStorm.
 * User: lijian
 * Date: 2016/4/13
 * Time: 10:49
 */

namespace Api\Controller;


use Common\Service\CountinService;
use Think\Controller;
use Common\Service\UserService;

class UserController extends Controller{

    public function updateUserInfo(){
        $user['id'] = I("id");
        $user['phone'] = I("phone");
        $goal = I("goal");
        $dayGoal = I("day_goal");
        $ret['msg'] = "";
        if( I("realname") ){
            $user['realname'] = I("realname");
            $user['showname'] = I("realname");//如果有真实姓名的话，显示真实姓名
        }
        if( I("dharma")){
            $user['dharma'] = I("dharma");
            $user['showname'] = I("dharma");//如果有法名的话，最优先显示法名，然后是真实姓名
        }
        if( I("goal") ){
            if (CountinService::isCountNumLegal($goal)) {
                $user['goal'] = $goal;

            }else{
                $ret['msg'] .= "“发愿目标”数字输入不正确，已放弃保存！\n";
            }
        }else{
            $user['goal'] = 0;
        }
        if( $dayGoal ){
            if (CountinService::isCountNumLegal($dayGoal)) {
                $user['day_goal'] = $dayGoal;
            }else{
                $ret['msg'] .= "“每日目标”数字输入不正确，已放弃保存！\n";
            }
        }else{
            $user['day_goal'] = 0;
        }
        if( UserService::updateUserInfo($user)){
            $ret['error_code'] = 0;
            $ret['msg'] .= "更新用户信息成功！";
        }else{
            $ret['error_code'] = 1;
            $ret['msg'] .= "用户信息未被修改！";
        }
        echo json_encode($ret);
    }

    /**
     * /Api/User/loginVolidate?phone=&password=
     */
    public function loginVolidate(){
        $phone = I("phone");
        $password = I("password");
        $ret = UserService::loginVolidate($phone, $password);
        echo json_encode($ret);
    }

}