#!/bin/bash

f="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd $f/wordpress;
php -d memory_limit=1024m wp-cron.php
