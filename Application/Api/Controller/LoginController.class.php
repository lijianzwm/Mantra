<?php
/**
 * Created by PhpStorm.
 * User: lijian
 * Date: 16/5/19
 * Time: 上午10:40
 */

namespace Api\Controller;

use Common\Service\UserService;
use Common\Service\CheckService;
use Think\Controller;

/**
 * 此接口不需要apikey
 * Class LoginController
 * @package Api\Controller
 */
class LoginController extends Controller{

    /**
     * 登录验证
     */
    public function loginVolidate(){

        $username = I("username");
        $password = I("password");

        CheckService::checkApiParam("username", $username);
        CheckService::checkApiParam("password", $password);

        $user = UserService::getUserByUsername($username);
        if( !$user ){
            echoJson(2, "该用户未注册!");
        }else{
            if( $user['password'] == md5($password) ){
                session("userid", $user['id']);
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
        $password = I("password");

        CheckService::checkApiParam("username", $username);
        CheckService::checkApiParam("password", $password);

        if( UserService::isUsernameUsed( $username ) ){
            echoJson(1, "该用户名已经被注册过");
        }

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
            echoJson(1, "无法写入数据库,注册失败!");
        }
    }

}