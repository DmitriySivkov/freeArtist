FROM nginx

ADD docker/nginx-reverse-proxy/config/nginx-production.conf /etc/nginx/conf.d/default.conf
