#!/bin/bash
docker exec -it app-php sh -c 'php artisan websockets:serve'