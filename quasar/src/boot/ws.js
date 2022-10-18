import Echo from "laravel-echo"

window.Pusher = require("pusher-js")

window.Echo = new Echo({
	broadcaster: "pusher",
	key: "local",
	wsHost: process.env.BACKEND_HOST + ":6001",
	wsPort: 6001,
	forceTLS: true,
	disableStats: true,
})
