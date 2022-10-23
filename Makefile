.PHONY: pg dump import

PG_CONTAINER_NAME = app-pg

APP_PHP_CONTAINER_NAME = app-php
APP_NGINX_CONTAINER_NAME = app-nginx

QUEUE_WORKER_PHP_CONTAINER_NAME = qw-php
WEBSOCKETS_PHP_CONTAINER_NAME = ws-php

DB_NAME = fa_db
PG_USER = root
PG_PASS = root

EXEC_PG = docker exec -it $(PG_CONTAINER_NAME) bash

EXEC_APP_PHP = docker exec -it $(APP_PHP_CONTAINER_NAME) bash
EXEC_APP_NGINX = docker exec -it $(APP_NGINX_CONTAINER_NAME) bash

EXEC_QUEUE_WORKER_PHP = docker exec -it $(QUEUE_WORKER_PHP_CONTAINER_NAME) bash
EXEC_WS_PHP = docker exec -it $(WEBSOCKETS_PHP_CONTAINER_NAME) bash

pg_container:
	$(EXEC_PG)

dump:
	pg_dump $(DB_NAME) > dump/new_dump.sql

import:
	psql $(DB_NAME) < dump/new_dump.sql

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

queue:
	./queue.sh

broadcast:
	./broadcast.sh

cache:
	./cache.sh