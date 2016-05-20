#!/usr/bin/expect
#登录服务器并更新代码
spawn  ssh root@139.129.22.123
send "cd /alidata/www/default\r"
expect "#"
send "git pull\r"
expect "#"
send "exit\r"
expect eof


spawn scp Application/Common/Conf/config.php root@139.129.22.123:/alidata/www/default/Application/Common/Conf/config.php

