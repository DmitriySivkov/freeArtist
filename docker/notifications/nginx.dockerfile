FROM nginx

ADD docker/notifications/config/nginx.conf /etc/nginx/conf.d/default.conf

WORKDIR /var/www/notifications
