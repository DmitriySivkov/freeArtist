# Quasar App (quasarapp)

A Quasar Framework app

## Install the dependencies
```bash
npm install
```

### Start the app in development mode (hot-code reloading, error reporting, etc.)
```bash
quasar dev
```

### Lint the files
```bash
npm run lint
```

### Build the app for production
```bash
quasar build
```

### quasar workflow
https://quasar.dev/quasar-cli-webpack/boot-files#quasar-app-flow

### Customize the configuration
See [Configuring quasar.conf.js](https://quasar.dev/quasar-cli/quasar-conf-js).

### if localhost is not trusted again
Go to api domen via browser and proceed with unsafe connection

### errors format when returning from backend:
["errors" => <br/> ["<error_bag_name>" => [*array_of_errors*]] <br/>]

### capacitor build:
chrome dev:
chrome://inspect/#devices

if containers break:
https://github.com/docker/for-win/issues/9272#issuecomment-776225866

capacitor build - tools.jar not found: https://stackoverflow.com/questions/47291056/could-not-find-tools-jar-please-check-that-c-program-files-java-jre1-8-0-151-c
build with debug mode works

? leave empty in env when developing mobile: 'SESSION_DOMAIN', 'SANCTUM_STATEFUL_DOMAINS'

чтобы добавить новый плагин в капаситор нужно добавить его и в /quasar/package.json и /quasar/src-capacitor/package.json
после этого нужно открыть android studio чтобы он проиндексировал изменения, иначе билд будет падать в ошибку

###подписать apk
выполняем zipalign: сначала cd "\AppData\Local\Android\Sdk\build-tools\35.0.0", затем
.\zipalign -v 4 "<path_to_apk>\<unsigned-apk-file.apk>" <some_name.apk>

в папке тулзов появится апк - забираем его на подпись

затем подписываем:
сначала cd "<android dir>\Android\Sdk\build-tools\35.0.0", затем
.\apksigner sign --ks "<path_to_generated_key>\<my-release-key.keystore>" --ks-key-alias alias_name "<path_to_apk>\<some_name.apk>"

гайд по генерации ключа и подписи: https://quasar.dev/quasar-cli-vite/developing-capacitor-apps/publishing-to-store
### production билд
- quasar build -m capacitor -T android

### dev билд
- если в android IDE ip адресс отличается от указанного при билде, то нужно "перезапустить" эмулятор в android IDE (как кнопка включения)
- если ошибка "INSTALL_FAILED_INSUFFICIENT_STORAGE", то в android IDE: run -> edit configurations -> clear app storage before deployment
- если не работает HMR в android IDE - попробовать с запуском квазара в контейнере / попробовать "перезапустить" эмулятор

### ошибка при билде: Host version "0.23.1" does not match binary version "0.24.0"
удалить package.json и node-modules. Прописать npm install не из контейнера

### q-card
q-card inside v-for with columns works this way:
dont add gutters to row.
Pull margin classes from q-card to wrapper

### create new DB with postgres
make pg_container + psql -U root -W root + create database <name>

### if pg container wont start (on windows)
net stop winnat + net start winnat

### ping websockets - from quasar dir
node_modules/wscat/bin/wscat --connect wss://freeartist.loc:6001 --no-check

### ping websockets with curl - from container to container
curl -i -N -H "Connection: Upgrade" -H "Upgrade: websocket" -H "Host: <ws host ip>" -H "Origin: <container ip from which the request comes" http://<ws host ip>

### rabbitmq
booting rabbitmq 1st time requires the 'default' queue created manually

### problems with permissions / cors / access to API
make cache

### cant connect to websockets locally
Its self-signed certificate problem. Manually enter websocket url and accept certificate
https://api.freeartist.loc/laravel-websockets

### start websockets
make ws

### start websockets in silent mode
make ws-silent

### start broadcast responsible queue
make broadcast

### to make GEO work
php artisan sxgeo:update & set 'LOCAL_IP' key in 'env'

### WRONG storage permissions cause CORS problem (and probably others)

### certbot для ssl
основной гайд: https://mindsers.blog/en/post/https-using-nginx-certbot-docker/

обновить сертификаты на проде:  docker compose -f docker-compose.production.yml run --rm certbot renew

перед запуском команд - вытащить все прокиды ssl ключей из nginx конфигов
нужно сделать сайт доступным просто по http на 80 порту
