import { createApp } from "vue";
import router from "Root/router/index"
import App from "Root/App";
import WaveUI from 'wave-ui'
import 'wave-ui/dist/wave-ui.css'

const app = createApp(App);

new WaveUI(app, {});

app.use(router);
app.mount("#app");
