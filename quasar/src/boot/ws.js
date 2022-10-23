import Echo from "laravel-echo"

window.Pusher = require("pusher-js")

window.Echo = new Echo({
	broadcaster: "pusher",
	key: "local",
	wsHost: process.env.BACKEND_HOST + ":6001",
	wsPort: 6001,
	forceTLS: true,
	disableStats: true,
	encrypted: true
})

export default ({ store }) => {
	let userProducers = store.state.userProducer.producers

	if (userProducers.length !== 0) {
		let ownProducer = userProducers.find((p) => p.user_id === store.state.user.data.id)

		if (ownProducer) {
			window.Echo.channel("relation-request")
				.listen("RelationRequestCreated", (e) => {
					console.log(e)
				})
		}
	}
}


