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

### Cordova build:
chrome dev:
chrome://inspect/#devices

if containers break:
https://github.com/docker/for-win/issues/9272#issuecomment-776225866

cordova build problem:
https://stackoverflow.com/questions/47239251/install-failed-user-restricted-android-studio-using-redmi-4-device

phone and PC shall be on the same IP range e.g 192.168.1.3 - 192.168.1.4 ...

windows blocks connection to itself. Need to turn off some param on windows defender. Brutally - turn off public network defender

when developing mobile - replace backend address with PC ip address (192.168.1.3).
Even possible to leave PC ip address as base API addr for any developing

###q-card
'q-card' inside v-for with columns works this way: 
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

### start websockets
make ws

### start websockets in silent mode
make ws-silent

### start broadcast responsible queue
make broadcast