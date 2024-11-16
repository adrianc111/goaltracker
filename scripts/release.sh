#!/usr/bin/env bash

echo 'Running migrations'
php /var/www/html/artisan migrate --force

echo 'Clearing the cache'
php /var/www/html/artisan optimize:clear
