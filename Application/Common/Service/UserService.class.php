<?php
/**
 * Created by PhpStorm.
 * User: lijian
 * Date: 2016/4/12
 * Time: 13:39
 */

namespace Common\Service;


class UserService{

    public static function addNewUser($user ){
        $id = M("user")->add($user);
        return $id;
    }

    public static function getUserById($userid){
        return M("user")->where("id=$userid")->find();
    }


    public static function checkChineseName($name){
        dump(strlen($name);
        if( preg_match('/^[\x{4E00}-\x{9FA5}]+$/u', $name) ){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 通过手机号来获取用户信息
     * @param $phone
     * @return mixed
     */
    public static function getUserByPhone($phone){
        return M("user")->where("phone=$phone")->find();
    }

    public static function getUserByUsername($username){
        return M("user")->where("username='$username'")->find();
    }

    /**
     * 判断手机号为$phone的用户是否存在
     * @param $phone
     * @return bool
     */
    public static function isExistUser($phone){
        $user = UserService::getUserByPhone($phone);
        if( $user ){
            return true;
        }else{
            return false;
        }
    }

    public static function isUsernameUsed($username){
        if (self::getUserByUsername($username)) {
            return true;
        }else{
            return false;
        }
    }

    public static function updateUserInfo($user){
        $result = M("user")->save($user);
        if ($result) {
            return $result;
        }else{
            return false;
        }
    }

    public static function checkUserInfo($user){
        if( !$user['phone'] || !$user['password'] ){
            return false;
        }else{
            return true;
        }
    }

    /**
     * 登录校验，返回error_code和msg
     * @param $phone
     * @param $password
     * @return mixed
     */
    public static function loginVolidate($phone,$password){
        if( !$phone ){
            $ret['error_code'] = 1;
            $ret['msg'] = "请填写手机号！";
        }
        $user = self::getUserByPhone($phone);
        if( !$user ){
            $ret['error_code'] = 2;
            $ret['msg'] = "此手机号未被注册！";
        }else{
            if( $user['password'] == md5($password) ){
                $ret['error_code'] = 0;
                $ret['msg'] = "登录成功！";
                $ret['user'] = $user;
            }else{
                $ret['error_code'] = 3;
                $ret['msg'] = "密码错误！";
            }
        }
        return $ret;
    }

    public static function isPhoneUsed($phone){
        if( M("user")->where("phone=$phone")->find() ){
            return true;
        }else{
            return false;
        }
    }

    public static function checkPasswordFormat($password){
        //TODO 检查密码格式是否合法
        return true;
    }

}