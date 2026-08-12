#!/bin/bash

PORT=${PORT:-80}

sed -i "s/listen 80;/listen $PORT;/g" /etc/nginx/sites-available/default 2>/dev/null
sed -i "s/listen \[::\]:80;/listen \[::\]:$PORT;/g" /etc/nginx/sites-available/default 2>/dev/null

php-fpm -D

nginx -g 'daemon off;'