.PHONY: pg dump import

PG_CONTAINER_NAME = app-pg

APP_PHP_CONTAINER_NAME = app-php
APP_NGINX_CONTAINER_NAME = app-nginx

NOTIFICATIONS_PHP_CONTAINER_NAME = notifications-php
NOTIFICATIONS_NGINX_CONTAINER_NAME = notifications-nginx

WS_PHP_CONTAINER_NAME = ws-php
WS_NGINX_CONTAINER_NAME = ws-nginx

DB_NAME = fa_db
PG_USER = root
PG_PASS = root

EXEC_PG = docker exec -it $(PG_CONTAINER_NAME) bash

EXEC_APP_PHP = docker exec -it $(APP_PHP_CONTAINER_NAME) bash
EXEC_APP_NGINX = docker exec -it $(APP_NGINX_CONTAINER_NAME) bash

EXEC_NOTIFICATIONS_PHP = docker exec -it $(NOTIFICATIONS_PHP_CONTAINER_NAME) bash
EXEC_NOTIFICATIONS_NGINX = docker exec -it $(NOTIFICATIONS_NGINX_CONTAINER_NAME) bash

EXEC_WS_PHP = docker exec -it $(WS_PHP_CONTAINER_NAME) bash
EXEC_WS_NGINX = docker exec -it $(WS_NGINX_CONTAINER_NAME) bash

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

notifications-php_container:
	$(EXEC_NOTIFICATIONS_PHP)

notifications-nginx_container:
	$(EXEC_NOTIFICATIONS_NGINX)

ws-php_container:
	$(EXEC_WS_PHP)

ws-nginx_container:
	$(EXEC_WS_NGINX)
