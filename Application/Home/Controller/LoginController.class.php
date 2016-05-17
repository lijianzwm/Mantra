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

class LoginController extends Controller{

    public function login(){
        $this->display();
    }

    public function loginHandler(){
        $username = I("username");
        if( !trim($username) ){
            $this->error("用户名为空");
        }
        $user = UserService::getUserByUsername($username);
        if( !$user ){
            $this->error("用户名为" . $username . "的用户不存在");
        }

        if( $user['password'] == md5(I("password")) ){
            session("userid", $user['id']);
            session("username", $username);
            session("showname", $user['showname']);
            $this->redirect(U("userCenter"));
        }else{
            $this->error("密码错误!");
        }
    }



    /**
     * 查询当前手机号的用户是否存在
     */
    public function isPhoneExist(){
        $phone = I("phone");
        if( UserService::isExistUser($phone) ){
            $ret['regist_state'] = 1;
        }else{
            $ret['regist_state'] = 0;
        }
        echo json_encode($ret);
    }

    public function regist(){
        $phone = I("phone");
        $this->assign("phone", $phone);
        $this->display();
    }

    public function findPassword(){
        $this->display("modifyPassword");
    }

    public function modifyPassword(){
        $phone = I("phone");
        $this->assign("phone", $phone);
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
        session("phone", null);
        session("showname", null);
        redirect(U('Index/index'));
    }



}
