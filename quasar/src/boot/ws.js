import Echo from "laravel-echo"

window.Pusher = require("pusher-js")

window.Echo = new Echo({
	broadcaster: "pusher",
	key: "local",
	wsHost: process.env.BACKEND_HOST + "/ws",
	wsPort: 6001,
	forceTLS: false,
	disableStats: true,
})
