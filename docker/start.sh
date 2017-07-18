#!/bin/bash
if [ -z $DEPLOY_ENV ]
then
DEPLOY_ENV="dev"
fi
echo $DEPLOY_ENV
usr/local/bin/php /data/www/swoole-crontab/src/admin/admin.php start  && \
usr/local/bin/php /data/www/swoole-crontab/src/center/center_new.php  start -d  && \
sleep 10 && \
usr/local/bin/php /data/www/swoole-crontab/src/agent/agent_new.php start -d  && \
tail -f /data/www/swoole-crontab/docker/docker_init


