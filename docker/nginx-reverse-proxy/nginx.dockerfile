FROM nginx

ADD docker/nginx-reverse-proxy/config/nginx.conf /etc/nginx/conf.d/default.conf