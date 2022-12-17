#!/bin/bash

# Exit when any command fails
set -e;

# install vendor
docker-compose exec php-fpm rm -rf vendor
docker-compose exec php-fpm composer install

# set permissions
docker-compose exec php-fpm chown -R $(id -u):$(id -g) /application
docker-compose exec php-fpm chmod 777 -R /application
docker-compose exec php-fpm chown -R www-data:www-data /application/vendor
