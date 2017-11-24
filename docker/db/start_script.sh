#!/bin/sh
# MySQL start script

sleep 10;

mysql --host=localhost --port=3306 --user=root --password=$MYSQL_ROOT_PASSWORD -e "CREATE DATABASE event"
mysql --host=localhost --port=3306 --user=root --password=$MYSQL_ROOT_PASSWORD event < /event.sql
