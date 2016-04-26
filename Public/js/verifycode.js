jsSendMessageWait = 60;
jsSendVerifyCode = 10000;

/**
 * 验证码倒计时
 * @param obj
 * @param btnValue
 */
function time(obj,btnValue){
    if( jsSendMessageWait == 0 ){
        obj.val(btnValue);
        obj.attr("disabled",false);
    }else if( jsSendMessageWait == -1 ) {
        obj.val("√");
    }else{
        obj.attr("disabled",true);
        jsSendMessageWait -= 1;
        obj.val(jsSendMessageWait);
        setTimeout(function(){
            time(obj,btnValue);
        },1000);
    }
}

function checkPhoneNum(phoneNum){
    if( phoneNum.length != 11 ){
        return false;
    }else{
        return true;
    }
}

/**
 * 添加发送验证码功能
 * @param buttonId 发送验证码按钮的id
 * @param phoneId 输入手机号的文本框id
 * @param url 后台ajax的链接
 */
function addSendVerifyCode(buttonId,phoneId,url){
    btn = $(buttonId);
    btn.attr("disabled",false);
    $(buttonId).click(function() {
        phoneNum = $(phoneId).val();
        if( checkPhoneNum(phoneNum) ){
            jsSendVerifyCode = Math.round(Math.random()*9000)+1000;
            //发送验证码
            var aj = $.ajax({
                url: url,
                data: {
                    apikey: "mscuuaB24FLrdTgBbNla3nn9WgVVecy8",
                    phone: phoneNum,
                    code: jsSendVerifyCode
                },
                type: 'post',
                cache: false,
                dataType: 'json',
                success: function (data) {
                    if (data.error_code) {
                        alert(data.msg);
                    }
                },
                error: function () {
                    alert("异常！");
                }
            });
            //设置倒计时1分钟
            jsSendMessageWait = 60;
            time(btn,btn.val());
        }else{
            alert("请输入正确的手机号码！");
        }
    });
}

/**
 * 添加校验验证码是否正确的功能
 * @param verifyCodeId 输入验证码的文本框id
 * @param submitButtonId 提交按钮id
 */
function addVolidateVerifyCode( verifyCodeId, sendCodeButton, submitButtonId ){
    $(verifyCodeId).blur(function(){
        if( jsSendVerifyCode == $(verifyCodeId).val() ){
            $(submitButtonId).attr("disabled", false);
            jsSendMessageWait = -1;
            $(sendCodeButton).val("√");
        }else{
            alert("验证码错误，请重新输入");
            $(verifyCodeId).val('');
        }
    });
}


