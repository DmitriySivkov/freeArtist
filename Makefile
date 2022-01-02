.PHONY: mysql dump import

MYSQL_CONTAINER_NAME = fa-pg
PHP_CONTAINER_NAME = fa-php
NGINX_CONTAINER_NAME = fa-nginx
DB_NAME = fa_db
MYSQL_USER = root
MYSQL_PASS = root

EXEC_PG = docker exec -it $(PG_CONTAINER_NAME) bash
EXEC_PHP = docker exec -it $(PHP_CONTAINER_NAME) bash
EXEC_NGINX = docker exec -it $(NGINX_CONTAINER_NAME) bash

pg_container:
	$(EXEC_PG)

dump:
	pg_dump $(DB_NAME) > dump/new_dump.sql

import:
	psql $(DB_NAME) < dump/new_dump.sql

php_container:
	$(EXEC_PHP)

nginx_container:
	$(EXEC_NGINX)
