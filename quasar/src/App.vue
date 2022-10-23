<template>
	<router-view />
</template>
<script>
import {defineComponent, onMounted} from "vue"
import { echo } from "src/boot/ws"
export default defineComponent({
	name: "App",
	setup() {
		onMounted(() => {
			echo.channel("relation-request")
				.listen("RelationRequestCreated", (e) => {
					console.log(e)
				})

			// let userProducers = store.state.userProducer.producers
			//
			// if (userProducers.length !== 0) {
			// 	let ownProducer = userProducers.find((p) => p.user_id === store.state.user.data.id)
			//
			// 	if (ownProducer) {
			// 		window.Echo.channel("relation-request")
			// 			.listen("RelationRequestCreated", (e) => {
			// 				console.log(e)
			// 			})
			// 	}
			// }
		})
	}
})
</script>
