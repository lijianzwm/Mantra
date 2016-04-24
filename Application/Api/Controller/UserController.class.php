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

class UserController extends CommonController{

    /**
     * /Api/User/updateUserInfo?
     */
    public function updateUserInfo(){
        $user['id'] = I("id");
        $user['phone'] = I("phone");
        $goal = I("goal");
        $dayGoal = I("day_goal");
        $realname = I("realname");
        $dharma = I("dharma");
        $ret['msg'] = "";
        $user['showname'] = "师兄".substr($user['phone'], -4);
        if( $realname ){
            $user['realname'] = $realname;
            $user['showname'] = $realname;//如果有真实姓名的话，显示真实姓名
        }
        if( $dharma ){
            $user['dharma'] = $dharma;
            $user['showname'] = $dharma;//如果有法名的话，最优先显示法名，然后是真实姓名
        }
        if( $goal ){
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
            $ret['user'] = UserService::getUserById($user['id']);
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

    public function regist(){
        $phone = I("phone");
        if( UserService::isPhoneUserd( $phone )){
            $ret['error_code'] = 0;
            $ret['msg'] = "电话号码已经被注册过！";
            echo json_encode($ret);
            die();
        }
        $password = I("password");
        $user['phone'] = $phone;
        $user['password'] = md5($password);
        $user['realname'] = I("realname");
        $user['showname'] = "师兄".substr($phone, -4);
        if( $user['realname'] ){
            $user['showname'] = $user['realname'];
        }
        if( !UserService::checkUserInfo($user) ){
            $ret['msg'] = "请将信息填写完整！";
            $ret['error_code'] = 1;
        }
        $id = M("user")->add($user);
        if( $id ){
            $ret['msg'] = "注册成功！";
            $ret['error_code'] = 0;
        }else{
            $ret['msg'] = "注册失败！";
            $ret['error_code'] = 1;
        }
        echo json_encode($ret);
    }

    public function getUserInfo(){
        $userid = I("userid");
        if( $userid ){
            if( is_numeric($userid) ){
                $ret['error_code'] = 0;
                $ret['msg'] = "获取用户信息成功！";
                $ret['user'] = UserService::getUserById($userid);
            }else{
                $ret['error_code'] = 1;
                $ret['msg'] = "用户id非法！";
            }
        }else{
            $ret['error_code'] = 1;
            $ret['msg'] = "用户id为空！";
        }
        echo json_encode($ret);
    }

}