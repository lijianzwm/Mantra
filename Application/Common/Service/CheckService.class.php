<?php
/**
 * Created by PhpStorm.
 * User: lijian
 * Date: 16/5/19
 * Time: 上午9:11
 */

namespace Common\Service;

/**
 * 检查一些数据是否合法的服务
 * Class CheckService
 * @package Common\Service
 */
class CheckService{

    /**
     * 返回校验结果
     * @param $status 0:成功; 1:失败
     * @param $msg
     * @return mixed
     */
    private static function returnResult( $status, $msg ){
        $result['status'] = $status;
        $result['msg'] = $msg;
        return $result;
    }

    private static function success($msg){
        return self::returnResult(0, $msg);
    }

    private static function error($msg){
        return self::returnResult(1, $msg);
    }

    /**
     * 校验各种传过来的参数,如果未通过校验,停止执行程序,直接返回错误信息!
     * @param $paramName
     * @param $value
     * @param null $tpl
     */
    public static function checkApiParam($paramName, $value, $tpl=null ){
        $checkResult = null;
        switch($paramName){
            case "userid":
                $checkResult = self::checkUseridFormat($value);
                break;
            case "username":
                $checkResult =  self::checkUsernameFormat($value);
                break;
            case "password":
                $checkResult =  self::checkPasswordFormat($value);
                break;
            case "date":
                $checkResult =  self::checkDateFormat($value, $tpl);
                break;
            case "num":
                $checkResult =  self::checkNumFormat($value);
                break;
            case "goal":
                $checkResult =  self::checkGoalNum($value);
                break;
            case "realname":
                $checkResult =  self::checkRealname($value);
                break;
            case "dharma":
                $checkResult =  self::checkDharma($value);
                break;
            default:
                $checkResult =  self::error("代码中不存在校验" . $paramName . "的功能!");
                break;
        }
        if( $checkResult['status'] ){
            echoError($checkResult['msg']);
        }
    }

    /**
     * 检查补报数字是否合法,正数和负数都合法
     * @param $num
     * @return bool
     */
    public static function checkNumFormat($num){
        $p = "/^[0-9]+$/";
        $n = "/^[-][0-9]+$/";

        if( strlen($num) > 7 ){
            return self::error("数字过大!");
        }
        if (preg_match($p, $num) || preg_match($n, $num)) {
            return self::success("数字格式正确!");
        }else{
            return self::error("数字格式非法!");
        }
    }

    /**
     * 校验账号是否合法,如果是英文和数字:3<长度<15,如果是中文:1<长度<8,中英数混合:2<长度<9
     * @param $username
     * @return mixed
     */
    public static function checkUsernameFormat( $username ){
        $char = '/^[A-Za-z0-9_]+$/';
        $chinese = '/^[\x{4e00}-\x{9fa5}]+$/u';
        $mixed = '/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u';
        //如果全都是字符
        if( preg_match($char, $username) ){
            if( strlen($username ) > 15 ){
                return self::error("用户名过长!");
            }else if( strlen($username)< 3 ){
                return self::error("用户名过短!");
            }else{
                return self::success("用户名格式正确!");
            }
        }

        //如果全都是中文
        if( preg_match($chinese, $username) ){
            if( mb_strlen($username, 'UTF8') > 8 ){
                return self::error("用户名过长!");
            }else if( mb_strlen($username, 'UTF8') < 1 ){
                return self::error("用户名过短!");
            }else{
                return self::success("用户名格式正确!");
            }
        }

        //如果是中文和字符混合
        if( preg_match($mixed, $username) ){
            if( mb_strlen($username, 'UTF8') > 9 ){
                return self::error("用户名过长!");
            }else if( mb_strlen($username, 'UTF8') < 2 ){
                return self::error("用户名过短!");
            }else{
                return self::success("用户名格式正确!");
            }
        }
        return self::error("用户名只能由中文,数字和大小写字母组成!");
    }

    /**
     * 校验密码格式是否合法
     * @param $password
     * @return mixed
     */
    public static function checkPasswordFormat( $password ){
        $patrn = '/^(\w){6,20}$/';
        if (preg_match($patrn, $password)) {
            return self::success("密码格式正确!");
        }else{
            return self::error("密码须由6-20个字母、数字、下划线组成!");
        }
    }

    public static function checkUseridFormat( $userid ){
        if (self::isPositiveNum($userid)) {
            return self::success("userid格式正确!");
        }else{
            return self::error("userid非法!");
        }
    }

    /**
     * 校验日期格式
     * @param $date
     * @param $tpl
     * @return mixed
     */
    public static function checkDateFormat( $date, $tpl ){

        switch($tpl){
            case "yyyy-mm-dd":
                $p = "/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/";
                break;
            case "yyyy-mm":
                $p = "/^[0-9]{4}-[0-9]{2}$/";
                break;
            default:
                return self::error("代码中传入的模板格式不正确!");
        }

        if (preg_match($p, $date)) {
            return self::success("日期格式正确!");
        }else{
            return self::error("日期格式错误!");
        }
    }

    public static function checkGoalNum($num){
        if( self::isPositiveNum($num)){
            return self::success("目标数字合法!");
        }else{
            return self::error("目标数字非法!");
        }
    }

    private static function isPositiveNum($num){
        $p = "/^[0-9]+$/";
        if (preg_match($p, $num)) {
            return true;
        }else{
            return false;
        }
    }

    private static function checkRealname( $realname ){
        $chinese = '/^[\x{4e00}-\x{9fa5}]+$/u';
        if( preg_match($chinese, $realname)){
            if( mb_strlen($realname,'UTF8') < 1 ){
                return self::error("真实姓名过短!");
            }
            if( mb_strlen($realname, 'UTF8') > 6 ){
                return self::error("真实姓名过长!");
            }
            return self::success("真实姓名格式合法!");
        }else{
            return self::error("真实姓名须为中文!");
        }
    }

    private static function checkDharma( $dharma ){
        $chinese = '/^[\x{4e00}-\x{9fa5}]+$/u';
        if( preg_match($chinese, $dharma)){
            if( mb_strlen($dharma, 'UTF8') < 1 ){
                return self::error("法名过短!");
            }
            if( mb_strlen($dharma, 'UTF8') > 8 ){
                return self::error("师兄...这么长的法名真的是师父给你起的吗?");
            }
            return self::success("法名格式正确!");
        }else{
            return self::error("师兄,咱的法名啥时候改成外文了?!");
        }
    }

}