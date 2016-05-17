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
        $goal = I("goal");
        $dayGoal = I("day_goal");
        $realname = I("realname");
        $dharma = I("dharma");
        $user['showname'] = I("username");
        if( !UserService::checkChineseName($realname) ){
            echoJson(1,"姓名只能是中文,并且在10个字符之内!");
        }
        if( !UserService::checkChineseName($dharma) ){
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
                echoJson(1,"每日目标”数字输入不正确，保存失败！");
            }
        }else{
            $user['day_goal'] = 0;
        }
        $user = UserService::updateUserInfo($user);
        if( $user ){
            session("userid", $user['userid']);
            session("username", $user['username']);
            session("showname", $user['showname']);
            echoJson(0,"更新用户信息成功！", UserService::getUserById($user['id']));
        }else{
            echoJson(1,"师兄别闹,您并没有修改您的信息!");
        }
    }

    /**
     * /Api/User/loginVolidate?phone=&password=
     */
    public function loginVolidate(){

        $username = I("username");
        $password = I("password");

        if( !trim($username) ){
            echoJson(1, "用户名为空!");
        }
        $user = UserService::getUserByUsername($username);
        if( !$user ){
            echoJson(2, "该用户未注册!");
        }else{
            if( $user['password'] == md5($password) ){
                session("userid", $user['userid']);
                session("username", $user['username']);
                session("showname", $user['showname']);
                echoJson(0, "登录成功!", $user);
            }else{
                echoJson(3, "密码错误!");
            }
        }
    }

    /**
     * 用户注册接口,注册成功时,返回的json中,data字段值为用户id
     */
    public function regist(){
        $username = I("username");
        if( UserService::isUsernameUsed( $username )){
            echoJson(1, "该用户名已经被注册过");
        }
        $password = I("password");
        $user['username'] = $username;
        $user['password'] = md5($password);
        $user['showname'] = $username;
        $id = UserService::addNewUser($user);
        if( $id ){
            session("userid", $id);
            session("username", $username);
            session("showname", $user['showname']);
            echoJson(0, "注册成功!", $id);
        }else{
            echoJson(1, "无法写入数据库,注册失败!", $id);
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
        if( preg_match('/^[a-zA-Z1-9]{1}([a-zA-Z0-9]|[.@_]){4,19}$/',$username) ){
            if( UserService::isUsernameUsed($username) ){
                echoJson(1, "用户名已存在");
            }else{
                echoJson(0, "用户名可以使用!");
            }
        }else{
            echoJson(1, "用户名长度为5-20位,只能以字母或数字(1-9)开头、后可带数字、“_”、“.”、“@”");
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
            if( M("user")->save($user) ){
                echoJson(0, "修改密码成功!");
            }else{
                echoJson(1, "新密码写入数据库失败!");
            }

        }else{
            echoJson(1, "密码错误!");
        }
    }

}