#!/bin/bash
docker exec -it ws-php sh -c 'php artisan websockets:serve >/dev/null 2>&1'
