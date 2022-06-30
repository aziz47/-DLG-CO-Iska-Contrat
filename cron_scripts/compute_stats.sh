#!/bin/sh
export EXEC_TIME="$(date +"%d-%m-%Y-%H-%M")"
docker-compose -f ../docker-compose.yml exec www php bin/console stats:perf:avis
docker-compose -f ../docker-compose.yml exec www php bin/console stats:perf:contrat

echo $EXEC_TIME
echo "========================================================================================"
echo
echo
echo
