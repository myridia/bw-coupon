#!/bin/bash
#note: the value of the 3 variable cannot include characters ( )
_DB_HOST="$(grep -oE "\DB_HOST',.*;" ../wp-config.php | tail -1 | sed "s/DB_HOST',//g;s/'//g"  | sed "s/DB_HOST',//g;s/)//g" | sed "s/DB_HOST',//g;s/;//g" | sed "s/DB_HOST',//g;s/ //g") "

_DB_NAME="$(grep -oE "\DB_NAME',.*;" ../wp-config.php | tail -1 | sed "s/DB_NAME',//g;s/'//g"  | sed "s/DB_NAME',//g;s/)//g" | sed "s/DB_NAME',//g;s/;//g" | sed "s/DB_USER',//g;s/ //g") "

_DB_USER="$(grep -oE "\DB_USER',.*;" ../wp-config.php | tail -1 | sed "s/DB_USER',//g;s/'//g"  | sed "s/DB_USER',//g;s/)//g" | sed "s/DB_USER',//g;s/;//g" | sed "s/DB_USER',//g;s/ //g") "

_DB_PASSWORD="$(grep -oE "\DB_PASSWORD',.*;" ../wp-config.php | tail -1 | sed "s/DB_PASSWORD',//g;s/'//g"  | sed "s/DB_PASSWORD',//g;s/)//g" | sed "s/DB_PASSWORD',//g;s/;//g" | sed "s/DB_PASSWORD',//g;s/ //g") "

_DOCKER_MODE="$(grep -oE "\DOCKER_MODE',.*;" ../wp-config.php | tail -1 | sed "s/DOCKER_MODE',//g;s/'//g"  | sed "s/DOCKER_MODE',//g;s/)//g" | sed "s/DOCKER_MODE',//g;s/;//g" | sed "s/DOCKER_MODE',//g;s/ //g") "

_PRODUCTION_MODE="$(grep -oE "\PRODUCTION_MODE',.*;" ../wp-config.php | tail -1 | sed "s/PRODUCTION_MODE',//g;s/'//g"  | sed "s/PRODUCTION_MODE',//g;s/)//g" | sed "s/PRODUCTION_MODE',//g;s/;//g" | sed "s/PRODUCTION_MODE',//g;s/ //g") "

_LOCAL_MODE="$(grep -oE "\LOCAL_MODE',.*;" ../wp-config.php | tail -1 | sed "s/LOCAL_MODE',//g;s/'//g"  | sed "s/LOCAL_MODE',//g;s/)//g" | sed "s/LOCAL_MODE',//g;s/;//g" | sed "s/LOCAL_MODE',//g;s/ //g") "


DB_HOST="${_DB_HOST//[[:blank:]]/}"
DB_NAME="${_DB_NAME//[[:blank:]]/}"
DB_USER="${_DB_USER//[[:blank:]]/}"
DB_PASSWORD="${_DB_PASSWORD=//[[:blank:]]/}"
DOCKER_MODE="${_DOCKER_MODE=//[[:blank:]]/}"
PRODUCTION_MODE="${_PRODUCTION_MODE=//[[:blank:]]/}"
LOCAL_MODE="${_LOCAL_MODE=//[[:blank:]]/}"

echo "DB HOST: " $DB_HOST
echo "DB: " $DB_NAME 
echo "DB USER: " $DB_USER 
echo "DB PASSWORD: " $DB_PASSWORD
echo "DB File: " "init/${DB_NAME//[[:blank:]]/}.sql.gz"
echo "DOCKER MODE: " $DOCKER_MODE
echo "PRODUCTION_MODE: " $PRODUCTION_MODE
echo "LOCAL_MODE: " $LOCAL_MODE

echo "...import from init"
#gunzip < init/$DB_NAME.sql.gz |  docker  run -i --rm --net=host   xxx/mysql -h 127.0.0.1 --user=$DB_USER -p$DB_PASSWORD $DB_NAME  



