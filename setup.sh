#!/bin/bash

cp .env.dist .env
docker-compose down
docker-compose build

cp docker/nginx/ssl/cert.key.dist docker/nginx/ssl/cert.key
cp docker/nginx/ssl/cert.pem.dist docker/nginx/ssl/cert.pem

docker-compose up -d

docker-compose exec --user www-data phpfpm composer install

sleep 25
docker-compose exec --user www-data phpfpm bin/digicli enqueue:setup-broker
docker-compose exec phpfpm supervisorctl restart consumer
docker-compose exec --user www-data phpfpm vendor/bin/doctrine orm:schema-tool:update --force
docker-compose exec --user www-data phpfpm bin/digicli digikala:admin:create
