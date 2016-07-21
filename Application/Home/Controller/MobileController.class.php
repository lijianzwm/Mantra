<?php
/**
 * Created by PhpStorm.
 * User: lijian
 * Date: 16/7/19
 * Time: 下午9:17
 */

namespace Home\Controller;

use Think\Controller;
use Common\Service\CountinService;
use Common\Service\UserService;

class MobileController extends Controller{

    public function login(){
        layout(false);
        $this->display();
    }

    public function regist(){
        layout(false);
        $this->display();
    }

    /**
     * 自动登录
     */
    public function autoLogin(){
        layout(false);

        $openid = I("openid");

        if( !$openid ){
            $this->error("openid没有传入!");
        }

        $user = M("user")->where("openid='$openid'");

        if( $user ){
            session("userid", $user['id']);
            session("username", $user['username']);
            session("showname", $user['showname']);
            redirect(U("Mobile/addCount"));
        }else{
            redirect(U("Mobile/wxBind", array( 'openid'=> $openid)));
        }
    }

    /**
     * 绑定微信
     */
    public function wxBind(){
        layout(false);
        $openid = I("openid");
        $this->assign("openid", $openid);
        $this->display();
    }

    /**
     * 处理绑定微信
     */
    public function wxBindHandler(){
        layout(false);
        $openid = I("openid");
        $username = I("username");
        $password = I("password");

        $user = UserService::getUserByUsername($username);

        if( !$user ){
            echoJson(2, "该用户未注册!");
        }else{
            if( $user['password'] == md5($password) ){
                $user['openid'] = $openid;
                if( M("user")->save($user) ){
                    echoJson(0, "绑定成功!", $user);
                }else{
                    echoJson(1, "该账户已经绑定过了!", $user);
                }
            }else{
                echoJson(3, "密码错误!");
            }
        }

    }

    public function addCount(){
        layout(false);
        $userid = session("userid");
        if( !isset($userid) ){
            redirect(U("Mobile/login"));
        }
        $todayNum = CountinService::getUserTodayNumById($userid);
        $total = CountinService::getUserTotalNumById($userid);
        if( $total == null ){
            $total = "用户不存在！";
            $todayNum = "用户不存在！";
        }else{
            if( $todayNum == null ){
                $todayNum = 0;
            }
        }
        $this->assign("userid", $userid);
        $this->assign("todayNum", $todayNum);
        $this->assign("total", $total);
        $this->display();
    }

}