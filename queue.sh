#!/bin/bash
docker exec -it qw-php sh -c 'php artisan rabbitmq:consume'