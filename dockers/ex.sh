#!/bin/bash

_DB_NAME="$(grep -oE "\DB_NAME',.*;" ../wp-config.php | tail -1 | sed "s/DB_NAME',//g;s/'//g"  | sed "s/DB_NAME',//g;s/)//g" | sed "s/DB_NAME',//g;s/;//g" | sed "s/DB_USER',//g;s/ //g") "
_DB_USER="$(grep -oE "\DB_USER',.*;" ../wp-config.php | tail -1 | sed "s/DB_USER',//g;s/'//g"  | sed "s/DB_USER',//g;s/)//g" | sed "s/DB_USER',//g;s/;//g" | sed "s/DB_USER',//g;s/ //g") "
_DB_PASSWORD="$(grep -oE "\DB_PASSWORD',.*;" ../wp-config.php | tail -1 | sed "s/DB_PASSWORD',//g;s/'//g"  | sed "s/DB_PASSWORD',//g;s/)//g" | sed "s/DB_PASSWORD',//g;s/;//g" | sed "s/DB_PASSWORD',//g;s/ //g") "

DB_NAME="${_DB_NAME//[[:blank:]]/}"
DB_USER="${_DB_USER//[[:blank:]]/}"
DB_PASSWORD="${_DB_PASSWORD=//[[:blank:]]/}"

echo $DB_NAME
echo $DB_USER
echo $DB_PASSWORD

docker  run -i --rm --net=host  salamander1/mysqldump --verbose -h 127.0.0.1 -u "${DB_NAME//[[:blank:]]/}" -p"${DB_PASSWORD//[[:blank:]]/}"  "${DB_NAME//[[:blank:]]/}" | gzip > "init/${DB_NAME//[[:blank:]]/}.sql.gz"


echo "DB: " $DB_NAME 
echo "DB USER: " $DB_USER 
echo "DB PASSWORD: " $DB_PASSWORD
echo "DB exported to: " "init/${DB_NAME//[[:blank:]]/}.sql.gz"
