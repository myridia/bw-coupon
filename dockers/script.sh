#!/bin/bash

DB_HOST=10.5.0.3
#echo "Wp Info"
#wp --info

cd /var/www/html/
ls -all
#wp db check


while ! mysqladmin ping -h"$DB_HOST" --silent; do
   echo "...sleep 1s until database docker server is up and ready..."    
   sleep 1
done

wp plugin install woocommerce --activate --allow-root

#ping 10.5.0.3




