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

    /**
     * 检查补报数字是否合法,正数和负数都合法
     * @param $num
     * @return bool
     */
    public static function checkNumFormat($num){
        $p = "/^[0-9]+$/";
        $n = "/^[-][0-9]+$/";

        if( strlen($num) > 7 ){
            return self::returnResult(1, "数字过大!");
        }
        if (preg_match($p, $num) || preg_match($n, $num)) {
            return self::returnResult(0, "数字格式正确!");
        }else{
            return self::returnResult(1, "数字格式非法!");
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
                return self::returnResult(1, "用户名过长!");
            }else if( strlen($username)< 3 ){
                return self::returnResult(1, "用户名过短!");
            }else{
                return self::returnResult(0, "用户名格式正确!");
            }
        }

        //如果全都是中文
        if( preg_match($chinese, $username) ){
            dump(mb_strlen($username, 'UTF8'));
            if( mb_strlen($username, 'UTF8') > 8 ){
                return self::returnResult(1, "用户名过长!");
            }else if( mb_strlen($username, 'UTF8') < 1 ){
                return self::returnResult(1, "用户名过短!");
            }else{
                return self::returnResult(0, "用户名格式正确!");
            }
        }

        //如果是中文和字符混合
        if( preg_match($mixed, $username) ){
            dump("混合!");
            if( mb_strlen($username, 'UTF8') > 9 ){
                return self::returnResult(1, "用户名过长!");
            }else if( mb_strlen($username, 'UTF8') < 2 ){
                return self::returnResult(1, "用户名过短!");
            }else{
                return self::returnResult(0, "用户名格式正确!");
            }
        }
        return self::returnResult(1, "用户名只能由中文,数字和大小写字母组成!");
    }

    /**
     * 校验密码格式是否合法
     * @param $password
     * @return mixed
     */
    public static function checkPasswordFormat( $password ){
        $patrn = '/^(\w){6,20}$/';
        if (preg_match($patrn, $password)) {
            return self::returnResult(0, "密码格式正确!");
        }else{
            return self::returnResult(1, "密码须由6-20个字母、数字、下划线组成!");
        }
    }

}