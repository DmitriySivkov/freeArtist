# Quasar App (frontend)

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

### Format the files

```bash
npm run format
```

### Build the app for production

```bash
quasar build
```

### Customize the configuration

See [Configuring quasar.conf.js](https://quasar.dev/quasar-cli/quasar-conf-js).

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
