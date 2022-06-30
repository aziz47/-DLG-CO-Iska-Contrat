#!/bin/sh
export EXEC_TIME=/saves/"$(date +"%d-%m-%Y-%H-%M")"
docker-compose -f ../docker-compose.yml exec www php bin/console messenger:consume async -vv

echo $EXEC_TIME
echo "======================================================================================"
echo
echo
echo
