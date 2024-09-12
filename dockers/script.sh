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



if [ ! -f "wp-config.php" ]; then
    echo "...wp-config.php does not exit, download and install "

fi


count=`mysql -h"$DB_HOST" -uroot -pPassPass123#  -e "select count(*) as c from information_schema.tables where table_schema = 'wordpress';"`
c=${count:2:1}
echo "$c - checked if wordpress tables are installed"

if [ $c == "0" ]; then
    echo "...wordpress tables do not exit, install and setup base admin user "
    wp core install --url=127.0.0.1 --title=BW-Coupon --admin_user=admin --admin_password=PassPass123# --admin_email=wordpress@myridia.com --allow-root
    sleep 10
fi

wp plugin install woocommerce --activate --allow-root
wp plugin install bw-coupon --activate --allow-root
wp plugin install wordpress-importer --activate --allow-root
wp theme install starter-shop --activate --allow-root 

wp wc payment_gateway update cheque --enabled=true --user=1 --allow-root


#wp wc product_cat create --name=Coupons  --user=1 --allow-root

#wp wc import-csv p.csv --mappings=map.csv --user=1 --allow-root

#wp wc product list --user=1 --allow-root

#wp import wp-content/plugins/woocommerce/sample-data/sample_products.xml --authors=skip --quiet --allow-root
#wp import  wc-products.csv --authors=skip --quiet --allow-root
#ping 10.5.0.3




