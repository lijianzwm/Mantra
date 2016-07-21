<?php
/**
 * Created by PhpStorm.
 * User: lijian
 * Date: 16/7/21
 * Time: 下午9:28
 */

namespace Common\Service;


class BrowserService{

    /**
     * @return bool
     * 判断是否为腾讯系浏览器(手机QQ,微信内置浏览器)
     */
    public static function isMobileTencentBrowser(){
        if ( preg_match('/QQBrowser/i',$_SERVER['HTTP_USER_AGENT' ]) ||
            strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false
        ){
            return true;
        }else{
            return false;
        }
    }

}