import Echo from "laravel-echo"
import Pusher from "pusher-js"
import { api } from "src/boot/axios"

window.Pusher = Pusher
window.Pusher.logToConsole = false

const echo = new Echo({
	broadcaster: "pusher",
	key: "local",
	wsHost: process.env.BACKEND_HOST,
	wsPort: 6001,
	wssPort: 6001,
	forceTLS: true,
	disableStats: true,
	enabledTransports: ["ws", "wss"],
	authorizer: (channel, options) => {
		return {
			authorize: (socketId, callback) => {
				api.post("broadcasting/auth", {
					socket_id: socketId,
					channel_name: channel.name
				})
					.then(response => {
						callback(false, response.data)
					})
					.catch(error => {
						callback(true, error)
					})
			}
		}
	},
})

echo.connector.pusher.connection.bind("connected", function() {
	window.Pusher.isConnected = true
})

echo.connector.pusher.connection.bind("disconnected", function(){
	window.Pusher.isConnected = false
})

export { echo }
