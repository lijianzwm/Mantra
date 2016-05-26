#!/usr/bin/expect
#登录开发机并更新代码
spawn  ssh root@139.129.22.123
send "./update.sh\r"
expect "#"
exec sleep 20
#send "git pull\r"
#expect "#"
send "exit\r"
expect eof




