include ./main/.env

.PHONY: pg dump import docker

MYSQL_CONTAINER_NAME = app-mysql

APP_PHP_CONTAINER_NAME = app-php
APP_NGINX_CONTAINER_NAME = app-nginx

QUEUE_WORKER_PHP_CONTAINER_NAME = qw-php
WEBSOCKETS_PHP_CONTAINER_NAME = ws-php

DB_NAME = fa_db
MYSQL_USER = root
MYSQL_PASS = root

EXEC_MYSQL = docker exec -it $(MYSQL_CONTAINER_NAME) bash

EXEC_APP_PHP = docker exec -it $(APP_PHP_CONTAINER_NAME) bash
EXEC_APP_NGINX = docker exec -it $(APP_NGINX_CONTAINER_NAME) bash

EXEC_QUEUE_WORKER_PHP = docker exec -it $(QUEUE_WORKER_PHP_CONTAINER_NAME) bash
EXEC_WS_PHP = docker exec -it $(WEBSOCKETS_PHP_CONTAINER_NAME) bash

mysql_container:
	$(EXEC_MYSQL)

dump:
	mysqldump -u$(MYSQL_USER) -p$(MYSQL_PASS) $(DB_NAME) > dump/new_dump.sql

import:
	mysql -u$(MYSQL_USER) -p$(MYSQL_PASS) $(DB_NAME) < dump/new_dump.sql

app-php_container:
	$(EXEC_APP_PHP)

app-nginx_container:
	$(EXEC_APP_NGINX)

qw-php_container:
	$(EXEC_QUEUE_WORKER_PHP)

ws-php_container:
	$(EXEC_WS_PHP)

ws:
	./ws.sh

ws-silent:
	./ws.sh silent

queue:
	./queue.sh

broadcast:
	./broadcast.sh

cache:
	./cache.sh

front:
	./quasar.sh

wipe-public:
	./wipe-public.sh

log:
	./log.sh

docker:
	./docker.sh $(APP_ENV)
