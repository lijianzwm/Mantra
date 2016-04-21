<?php
/**
 * Created by PhpStorm.
 * User: lijian
 * Date: 2016/4/12
 * Time: 10:45
 */

namespace Api\Controller;
use Think\Controller;
use Common\Service\MessageService;

class MessageController extends CommonController{

    public function sendVerifyCode(){
        $phone = I("phone");
        $code = I("code");
        $product = C("SMS_REGISTER_PRODUCT");
        $template = C("SMS_REGISTER_VERIFY_CODE");
        $signName = C("SMS_REGISTER_SIGN_NAME");
        $smsParams = [
            'code'    => "$code",
            'product' => "$product",
        ];
        $result = MessageService::sendMessage($phone, $smsParams, $template, $signName);
        echo json_encode($result);
    }
}