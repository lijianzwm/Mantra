<?php
/**
 * Created by PhpStorm.
 * User: lijian
 * Date: 16/5/14
 * Time: 下午1:09
 */

namespace Admin\Controller;


use Think\Controller;

class UserController extends Controller{

    public function index(){
        $this->display();
    }

    public function loginHandler(){
        $username = I("username");
        $password = I("password");//管理后台的密码不用md5加密
        $userInfo = M("admin")->where("username='$username'")->find();
        if( $userInfo && $password == $userInfo['password']){
            session("adminuser", $username);
            session("realname", $userInfo['realname']);
            $this->success("登录成功！", U('Index/index'));
        }else{
            $this->error("用户名/密码错误！");
        }
    }

    public function logout(){
        session("adminuser", null);
        session("realname", null);
        $this->success("退出登录成功！");
    }

    public function modifyPassword(){
        layout(true);
        $this->display();
    }

    public function modifyPasswordHandler(){
        $username = I("username");
        $password = I("password");
        $newPassword = I("newPassword");
        $volidatePassword = I("volidatePassword");
        $oldPassword = M("admin")->where("username='$username'")->find();
        if ($username && $password && $newPassword && $volidatePassword) {
            if ($newPassword != $volidatePassword) {
                $this->error("两次密码不一致！");
            }
            if ($oldPassword != $password) {
                $this->error("原始密码不正确");
            }
            $user['username'] = $username;
            $user['password'] = $newPassword;
            if (M("admin")->save($user)) {
                $this->success("修改成功！", U("Index/index"));
            } else {
                $this->error("数据没有更新");
            }
        } else {
            $this->error("请将信息填写完整");
        }
    }

}