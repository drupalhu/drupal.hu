#!/bin/bash
mysql -u root -proot -e "CREATE DATABASE IF NOT EXISTS drupal;"
if [ ! -d /var/www/html/sites/default/files/private ]
then
  mkdir /var/www/html/sites/default/files/private
  chown www-data.www-data /var/www/html/sites/default/files/private
fi
echo "=================================================="
echo
echo " Pranacssorhoz futtasd a következő parancsot:"
echo
echo "   docker exec -i -t drupalhu_web_1 bash"
echo
echo "=================================================="
/usr/sbin/apache2 -D FOREGROUND
