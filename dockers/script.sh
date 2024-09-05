#!/bin/bash

DB_HOST=10.5.0.6
echo "Wp Info"
wp --info

cd /var/www/html/

wp plugin install woocommerce --activate


