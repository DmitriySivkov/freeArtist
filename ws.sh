#!/bin/bash
if [ "$1" == "silent" ]
then
    docker exec -it ws-php sh -c 'php artisan websockets:serve >/dev/null 2>&1'
else
    docker exec -it ws-php sh -c 'php artisan websockets:serve'
fi