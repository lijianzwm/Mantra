#!/usr/bin/env bash
# 提交修改并push,传入commit message
git commit -am $1
git push
./update.sh
