<?php
/**
 * Created by PhpStorm.
 * User: lijian
 * Date: 2016/4/12
 * Time: 13:39
 */

namespace Common\Service;


class UserService{

    public static function getUserById($userid){
        return M("user")->where("id=$userid")->find();
    }

    /**
     * 通过手机号来获取用户信息
     * @param $phone
     * @return mixed
     */
    public static function getUserByPhone($phone){
        return M("user")->where("phone=$phone")->find();
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
        }
        if( $user['password'] == md5($password) ){
            $ret['error_code'] = 0;
            $ret['msg'] = "登录成功！";
            $ret['user'] = $user;
        }else{
            $ret['error_code'] = 3;
            $ret['msg'] = "密码错误！";
        }
        return $ret;
    }


}