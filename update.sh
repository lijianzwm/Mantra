#!/usr/bin/env bash
git commit -am '[lindex $argv 0]'
git push

#!/usr/bin/expect
# 自动发布到服务器
spawn  ssh root@139.129.22.123
send "~/update.sh\r"
expect "#"
send "exit\r"
   expect eof
