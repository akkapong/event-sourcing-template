#!/bin/sh

docker rm -f event_test_api
docker rm -f event_test_mongo
docker rm -f event_test_maria
docker-compose rm


docker-compose build event_test_mongo
DB_ID=$(docker-compose up -d event_test_mongo)

docker-compose build event_test_maria
MARIA_DB_ID=$(docker-compose up -d event_test_maria)

docker-compose build event_test_api
APP_ID=$(docker-compose up -d event_test_api)


# sleep 3

# docker exec -it event_test_api composer install

