#!/bin/bash
mysql -u root -proot -e "CREATE DATABASE IF NOT EXISTS drupal;"
echo "=================================================="
echo
echo " Pranacssorhoz futtasd a következő parancsot:"
echo
echo "   docker exec -i -t drupalhu_web_1 bash"
echo
echo "=================================================="
/usr/sbin/apache2 -D FOREGROUND
