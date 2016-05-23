#!/usr/bin/expect
#登录开发机并更新代码
spawn  ssh root@139.129.22.123
send "./update.sh\r"
expect "#"
send "sleep 20\r"
expect "#"
#send "git pull\r"
#expect "#"
send "exit\r"
expect eof




