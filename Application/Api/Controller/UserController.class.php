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
     * 更新用户信息接口,返回的json中data字段存的是更新好的用户类
     * /Api/User/updateUserInfo?
     */
    public function updateUserInfo(){
        $user['id'] = I("id");
        $username = I("username");
        $goal = I("goal");
        $dayGoal = I("day_goal");
        $realname = I("realname");
        $dharma = I("dharma");
        $user['showname'] = $username;
        if( $realname && !UserService::checkChineseName($realname) ){
            echoJson(1,"姓名只能是中文,并且在10个字符之内!");
        }
        if( $dharma && !UserService::checkChineseName($dharma) ){
            echoJson(1,"法名只能是中文,并且在10个字符之内!");
        }
        if( $realname ){
            $user['realname'] = $realname;
            $user['showname'] = $realname;//如果有真实姓名的话，显示真实姓名
        }else{
            $user['realname'] = null;
        }
        if( $dharma ){
            $user['dharma'] = $dharma;
            $user['showname'] = $dharma;//如果有法名的话，最优先显示法名，然后是真实姓名
        }else{
            $user['dharma'] = null;
        }
        if( $goal ){
            if (CountinService::isCountNumLegal($goal)) {
                $user['goal'] = $goal;
            }else{
                echoJson(1,"发愿目标”数字输入不正确，保存失败！");
            }
        }else{
            $user['goal'] = 0;
        }
        if( $dayGoal ){
            if (CountinService::isCountNumLegal($dayGoal)) {
                $user['day_goal'] = $dayGoal;
            }else{
                echoJson(1,"每日目标数字输入不正确，保存失败！");
            }
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

    public function checkUsername(){
        $username = I("username");
        if( strlen($username) > 10 || strlen($username)< 2 ){
            echoJson(1, "用户名长度非法!");
        }else{
            echoJson(0, "用户名合法!");
        }
    }

    public function isPhoneUsed(){
        $phone = I("phone");
        if( $phone ){
            if (UserService::isPhoneUsed($phone)) {
                $ret['error_code'] = 0;
                $ret['msg'] = "该号码可以重置密码！";
            }else{
                $ret['error_code'] = 1;
                $ret['msg'] = "该号码未被注册！";
            }
        }else{
            $ret['error_code'] = 1;
            $ret['msg'] = "手机号码为空！";
        }
        echo json_encode($ret);
    }

    public function resetPassword(){
        $phone = I("phone");
        $newPassword = I("newPassword");
        if( UserService::isPhoneUsed($phone)){
            if (UserService::checkPasswordFormat($newPassword)) {
                $user = UserService::getUserByPhone($phone);
                if( $user ){
                    $user['password'] = md5($newPassword);
                    UserService::updateUserInfo($user);
                    $ret['error_code'] = 0;
                    $ret['msg'] = "修改密码成功！";
                }else{
                    $ret['error_code'] = 1;
                    $ret['msg'] = "查无此用户";
            }
            }else{
                $ret['error_code'] = 1;
                $ret['msg'] = "密码格式不正确！";
            }
        }else{
            $ret['error_code'] = 1;
            $ret['msg'] = "无此用户！";
        }
        echo json_encode($ret);
    }

    public function modifyPassword(){
        $username = I("username");
        $oldPassword = I("oldPassword");
        $newPassword = I("newPassword");
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