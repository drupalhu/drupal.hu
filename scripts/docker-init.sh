#!/bin/bash
mysql -u root -proot -e "CREATE DATABASE IF NOT EXISTS drupal;"
echo "=================================================="
echo
echo " Pranacssorhoz futtasd a következő parancsot:"
echo
echo "   fig run --rm --entrypoint bash web"
echo
echo "=================================================="
/usr/sbin/apache2 -D FOREGROUND
