<?php
/**
 * Created by PhpStorm.
 * User: lijian
 * Date: 2016/4/12
 * Time: 9:38
 */

namespace Home\Controller;

use Think\Controller;
use Common\Service\UserService;
use Common\Service\CountinService;
use Common\Service\BrowserService;
use Think\Page;

class LoginController extends Controller{

    public function login(){

        $username = I("username");
        $password = I("password");

        if( BrowserService::isMobileTencentBrowser() ){
            layout(false);
            if( $username && $password ){
                $this->assign("username", $username);
                $this->assign("password", $password);
            }
            $this->display('m_login');
        }else{
            $this->display('login');
        }
    }

    public function findPassword(){
        $this->display("modifyPassword");
    }

    public function modifyPassword(){
        $username = I("username");
        $this->assign("username", $username);
        $this->display();
    }

    public function modifyPasswordHandler(){
        $phone = I("phone");
        $password = I("password");
        if( !$phone || !$password ){
            $this->error("请将信息填写完整！");
        }
        $user = UserService::getUserByPhone($phone);
        session("userid", $user['id']);
        session("phone", $user['phone']);
        session("showname",$user['showname']);
        $user['password'] = md5($password);
        UserService::updateUserInfo($user);
        $this->success("修改成功！", U('Login/userCenter'));
    }

    public function userCenter(){
        $id =  session("userid");
        $username = session("username");
        $todayNum = CountinService::getUserTodayNumById($id);
        if( $id && $username ){
            $user = UserService::getUserByUsername($username);
            if( !$user ){
                session("userid", null);
                session("username", null);
                session("showname",null);
                redirect(U('Login/login'));
            }
            $user['goal'] = $user['goal'] == 0 ? null : $user['goal'];
            $user['day_goal'] = $user['day_goal'] == 0 ? null : $user['day_goal'];
            if( $user['goal'] != null ){
                $totalGoalPercent = CountinService::calPercent($user['total'],$user['goal']);
                $this->assign("totalGoalPercent", $totalGoalPercent);
            }
            if( $user['day_goal'] != null ){
                $dayGoalPercent = CountinService::calPercent($todayNum,$user['day_goal']);
                $this->assign("dayGoalPercent", $dayGoalPercent);
            }
            $this->assign("todayNum", $todayNum);
            $this->assign("user", $user);
            $this->display();
        }else{
            redirect(U('Login/login'));
        }
    }

    public function logout(){
        session("userid", null);
        session("username", null);
        session("showname", null);
        redirect(U('Index/index'));
    }

    public function regist(){
        if( BrowserService::isMobileTencentBrowser() ){
            layout(false);
            $this->display('m_regist');
        }else{
            $this->display('regist');
        }
    }

    /**
     * 自动登录
     */
    public function autoLogin(){
        layout(false);
        $this->display();
    }


}
