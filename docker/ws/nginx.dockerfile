FROM nginx

ADD docker/ws/config/nginx.conf /etc/nginx/conf.d/default.conf

WORKDIR /var/www/ws
