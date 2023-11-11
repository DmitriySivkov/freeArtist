#!/bin/sh

echo "Rabbit init script";
( rabbitmqctl wait --timeout 10 /var/lib/rabbitmq/mnesia/rabbitmq ; \
rabbitmqadmin --username root --password root declare queue name=broadcast ; \
echo "Rabbit init fired"; ) &

rabbitmq-server $@
