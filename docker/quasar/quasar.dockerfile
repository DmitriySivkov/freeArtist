FROM node:20.17.0

WORKDIR /app

RUN npm i -g @vue/cli
RUN npm i -g @quasar/cli

COPY /quasar .

RUN npm install
