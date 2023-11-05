#!/bin/bash
echo -e '\e[1;42m emptying public storage \e[0m'
docker exec -it app-php sh -c 'cd storage/app/public && find . ! -path . -type d -exec rm -rf {} +'
