import Echo from "laravel-echo"
import Pusher from "pusher-js"

window.Pusher = Pusher
window.Pusher.logToConsole = true

const echo = new Echo({
	broadcaster: "pusher",
	key: "local",
	wsHost: process.env.BACKEND_HOST,
	wsPort: 6001,
	wssPort: 6001,
	forceTLS: true,
	disableStats: true,
	enabledTransports: ["ws", "wss"],
})

export { echo }
