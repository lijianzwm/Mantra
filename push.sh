#!/usr/bin/env bash
git commit -am '[lindex $argv 0]'
git push
./update.sh
