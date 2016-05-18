function getCurYearMonthDay(){
    var myDate = new Date();
    var year = myDate.getFullYear();    //获取完整的年份(4位,1970-????)
    var month = myDate.getMonth();       //获取当前月份(0-11,0代表1月)
    var day = myDate.getDate();        //获取当前日(1-31)
    if( month < 10 ){
        month =  "0"+month;
    }
    if( day < 10 ){
        day = "0" + day;
    }
    return year + "-" + month + "-" + day;
}