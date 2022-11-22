FROM node:19-alpine

RUN npm install -g @quasar/cli && \
    npm install -g @vue/cli && \
    npm install -g @vue/cli-init

RUN mkdir /home/node/quasar

WORKDIR /home/node/quasar

EXPOSE 8081 9000