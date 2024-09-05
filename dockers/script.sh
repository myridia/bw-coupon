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
wp plugin install bw-coupon --activate --allow-root
wp plugin install wordpress-importer --activate --allow-root
wp theme install starter-shop --activate --allow-root 

#wp wc product list --user=1 --allow-root
wp wc payment_gateway update cheque --user=1 --enabled=true --allow-root
wp import wp-content/plugins/woocommerce/sample-data/sample_products.xml --authors=skip --quiet --allow-root
#wp import  wc-products.csv --authors=skip --quiet --allow-root
#ping 10.5.0.3




