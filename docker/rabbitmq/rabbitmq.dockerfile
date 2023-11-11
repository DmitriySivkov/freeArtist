FROM rabbitmq:3.11.2-management

COPY init.sh /init.sh
RUN chmod +x /init.sh
CMD ["/init.sh"]
