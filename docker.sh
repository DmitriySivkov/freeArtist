#!/bin/bash
if [ "$1" == "local" ]
then
		docker-compose -f docker-compose.local.yml up
else
    docker-compose -f docker-compose.production.yml up
fi
# todo - return '-d' for production
