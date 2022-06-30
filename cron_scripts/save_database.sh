#!/bin/sh
docker-compose -f ../docker-compose.yml exec mariadb bash /saves/save_database.sh
