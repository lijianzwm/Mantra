function myAjax(url, data, succFunc, errorFunc){
    $.ajax({
        url: url,
        data: data,
        type: 'post',
        cache: false,
        dataType: 'json',
        success: function (data) {
            if (data.error_code) {
                errorFunc(data);
            }else{
                succFunc(data);
            }
        },
        error: function () {
            dialog.error("ajax请求失败!");
        }
    });
}