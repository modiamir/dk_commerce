#!/bin/bash

cp .env.dist .env
docker-compose down
docker-compose build

cp docker/nginx/ssl/cert.key.dist docker/nginx/ssl/cert.key
cp docker/nginx/ssl/cert.pem.dist docker/nginx/ssl/cert.pem

docker-compose up -d

composer install --no-scripts

sleep 10

docker-compose exec --user www-data phpfpm vendor/bin/doctrine orm:schema-tool:update --force
docker-compose exec --user www-data phpfpm bin/digicli digikala:admin:create
