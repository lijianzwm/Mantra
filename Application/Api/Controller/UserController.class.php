<?php
/**
 * Created by PhpStorm.
 * User: lijian
 * Date: 2016/4/13
 * Time: 10:49
 */

namespace Api\Controller;


use Common\Service\CheckService;
use Common\Service\CountinService;
use Think\Controller;
use Common\Service\UserService;

class UserController extends CommonController{

    /**
     * 更新用户信息接口,返回的json中data字段存的是更新好的用户类
     * /Api/User/updateUserInfo?
     */
    public function updateUserInfo(){
        $user['id'] = I("id");
        $username = I("username");
        $realname = I("realname");
        $dharma = I("dharma");
        $goal = I("goal");
        $dayGoal = I("day_goal");
        $user['showname'] = $username;

        CheckService::checkApiParam("userid", $user['id']);
        CheckService::checkApiParam("username", $username);

        if( $realname ){
            CheckService::checkApiParam("realname", $realname);
            $user['realname'] = $realname;
            $user['showname'] = $realname;//如果有真实姓名的话，显示真实姓名
        }else{
            $user['realname'] = null;
        }
        if( $dharma ){
            CheckService::checkApiParam("dharma", $dharma);
            $user['dharma'] = $dharma;
            $user['showname'] = $dharma;//如果有法名的话，最优先显示法名，然后是真实姓名
        }else{
            $user['dharma'] = null;
        }
        if( $goal ){
            CheckService::checkApiParam("goal", $goal);
            $user['goal'] = $goal;
        }else{
            $user['goal'] = 0;
        }
        if( $dayGoal ){
            CheckService::checkApiParam("goal", $dayGoal);
            $user['day_goal'] = $dayGoal;
        }else{
            $user['day_goal'] = 0;
        }
        $user = UserService::updateUserInfo($user);
        if( $user ){
            echoJson(0,"更新用户信息成功！", UserService::getUserByUsername($username));
        }else{
            echoJson(1,"师兄别闹,您并没有修改您的信息!");
        }
    }

    public function getUserInfo(){
        $userid = I("userid");

        CheckService::checkApiParam("userid", $userid);

        $user = UserService::getUserById($userid);

        if( $user ){
            echoSuccess("获取用户信息成功!", $user);
        }else{
            echoError("无此用户!");
        }

    }

    public function modifyPassword(){
        $username = I("username");
        $oldPassword = I("oldPassword");
        $newPassword = I("newPassword");

        CheckService::checkApiParam("username", $username);
        CheckService::checkApiParam("password", $oldPassword);
        CheckService::checkApiParam("password", $newPassword);

        $user = UserService::getUserByUsername($username);
        if( !$user ){
            echoJson(1, "账号 ".$username." 不存在!");
        }
        if( $user['password'] == md5($oldPassword) ){
            $user['password'] = md5($newPassword);
            M("user")->save($user);
            echoJson(0, "修改密码成功!");
        }else{
            echoJson(1, "密码错误!");
        }
    }

}