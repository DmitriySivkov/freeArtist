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
	authEndpoint: process.env.BACKEND_SERVER + "/api/broadcasting/auth",
	auth: {
		headers: {
			"Accept": "Application/json",
			"Access-Control-Allow-Credentials": true,
		}
	}
})

echo.connector.pusher.connection.bind("connected", function() {
	window.Pusher.isConnected = true
})

echo.connector.pusher.connection.bind("disconnected", function(){
	window.Pusher.isConnected = false
})

export { echo }
