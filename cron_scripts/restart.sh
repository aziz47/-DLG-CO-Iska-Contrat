#!/bin/sh

docker-compose -f ../docker-compose.yml stop
docker system prune -f
docker-compose -f ../docker-compose.yml start

echo "=========================================================================="
echo
echo
echo
