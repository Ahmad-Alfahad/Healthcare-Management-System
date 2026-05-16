#!/bin/bash
cp /home/site/wwwroot/nginx.conf /etc/nginx/sites-available/default
service nginx restart
php /home/site/wwwroot/artisan config:cache
php /home/site/wwwroot/artisan route:cache