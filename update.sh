#!/usr/bin/expect
spawn  ssh root@139.129.22.123
send "cd /alidata/www/default\r"
expect "#"
send "git pull\r"
expect "#"
send "exit\r"
expect eof
