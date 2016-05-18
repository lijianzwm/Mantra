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
        if( strlen( $name ) > 30 ){
            return false;
        }
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
    public static function loginVolidate($username,$password){
        if( !trim($username) ){
            echoJson(1, "用户名为空!");
        }
        $user = self::getUserByUsername($username);
        if( !$user ){
            echoJson(2, "该用户未注册!");
        }else{
            if( $user['password'] == md5($password) ){
                echoJson(0, "登录成功!", $user);
            }else{
                echoJson(3, "密码错误!");
            }
        }
    }

    public static function isPhoneUsed($phone){
        if( M("user")->where("phone=$phone")->find() ){
            return true;
        }else{
            return false;
        }
    }

    public static function checkPasswordFormat($password){
        $patrn = '/^(\w){6,20}$/';
        if (preg_match($patrn, $password)) {
            return true;
        }else{
            return false;
        }
    }

    public static function checkUsernameFormat($username ){
        $patrn = "/^[\u4E00-\u9FA5A-Za-z0-9_]+$/";
        if( strlen($username) > 10 || strlen($username)< 2 ){
            return false;
        }
        if( preg_match($patrn, $username) ){
            return true;
        }else{
            return false;
        }
    }

}