<?php
/**
 * Created by PhpStorm.
 * User: lijian
 * Date: 2016/3/28
 * Time: 11:53
 */

namespace Common\Service;
use Alidayu\AlidayuClient as Client;
use Alidayu\Request\SmsNumSend;

class MessageService{

    /**
     * 验证码${code}，您正在参加$organization的$$activity活动，请确认系本人申请。
     * @param $phone
     * @param $code
     * @param $activity
     */
    public static function sendVerifyNum( $phone, $code, $organization, $activity ){
        $client  = new Client;
        $request = new SmsNumSend;

        // 短信内容参数
        $smsParams = [
            'code'    => "$code",
            'product' => "$organization",
            'item' => "$activity"
        ];

        // 设置请求参数
        $req = $request->setSmsTemplateCode('SMS_6000004')
            ->setRecNum($phone)
            ->setSmsParam(json_encode($smsParams))
            ->setSmsFreeSignName('活动验证')
            ->setSmsType('normal')
            ->setExtend('demo');

        $result = $client->execute($req);

        dump($result);

        if( $result['alibaba_aliqin_fc_sms_num_send_response']['result']['err_code'] == 0 ){//error_code=0
            return true;
        }else{
            return false;
        }
    }

    /**
     * 发送短信服务
     * @param $phone
     * @param $smsParams
     * @param $template
     * @param $signName
     * @return bool
     */
    public static function sendMessage($phone, $smsParams, $template, $signName){
        $client  = new Client;
        $request = new SmsNumSend;
        $req = $request->setSmsTemplateCode($template)
            ->setRecNum($phone)
            ->setSmsParam(json_encode($smsParams))
            ->setSmsFreeSignName($signName)
            ->setSmsType('normal')
            ->setExtend('demo');
        $result = $client->execute($req);
        /**
         * 发送成功的$result格式：
         *         ["alibaba_aliqin_fc_sms_num_send_response"] => array(2) {
         *              ["result"] => array(3) {
         *                  ["err_code"] => string(1) "0"
         *                  ["model"] => string(26) "101181423839^1101670633148"
         *                  ["success"] => bool(true)
         *              }
         *              ["request_id"] => string(12) "zq8efkmp8yjd"
         *         }
         */
        if( $result['alibaba_aliqin_fc_sms_num_send_response']['result']['err_code'] == 0 ){//error_code=0
            $ret['error_code'] = 0;
            $ret['msg'] = "短信发送成功！";
        }else{
            $ret['error_code'] = 1;
            $ret['msg'] = "短信发送失败！";
            $ret['json'] = $result;
        }
        return $ret;
    }
}