FROM node:20.17.0

WORKDIR /app

RUN npm i -g @vue/cli
RUN npm i -g @quasar/cli

COPY /quasar .

RUN npm install

# todo - нужно чтобы был наполнен node_modules на хосте:
# todo - https://stackoverflow.com/questions/38425996/docker-compose-volume-on-node-modules-but-is-empty
# todo - понять почему выдает ошибку при попытке установить расширение quasar ext add @quasar/qcalendar@next
# todo - возможно убрать анонимный маунт у сервиса quasar       - /app/node_modules
