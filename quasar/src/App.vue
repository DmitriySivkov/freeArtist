<template>
	<router-view />
</template>
<script>
import { defineComponent, watch } from "vue"
import { echo } from "boot/ws"
import { useUserProducer } from "src/composables/userProducer"
import { useUserPermission } from "src/composables/userPermission"
import { useUser } from "src/composables/user"
import { useStore } from "vuex"
export default defineComponent({
	name: "App",
	setup() {
		const $store = useStore()
		const { producerTeams } = useUserProducer()
		const { hasPermission } = useUserPermission()
		const { user } = useUser()

		const relationRequestSocket = () => {
			if (!window.Pusher.isConnected)
				echo.connect()

			if (user.value.data.id) {
				echo.private("relation-requests.user." + user.value.data.id)
					.listen(".RelationRequestCreated", (e) => {
						console.log(e.model)
					})

				if (producerTeams.value.length > 0) {
					for (let i in producerTeams.value) {
						if (
							producerTeams.value[i].user_id === user.value.data.id ||
							hasPermission(producerTeams.value[i].id, ["producer_incoming_coworking_requests"])
						) {
							echo.private("relation-requests.producer." + producerTeams.value[i].id)
								.listen(".RelationRequestCreated", (e) => {
									$store.commit("userProducer/SET_PRODUCER_INCOMING_RELATION_REQUESTS", {
										incoming_coworking_requests: [e.model],
										producer_id: producerTeams.value[i].id
									})
								})
						}
					}
				}
			}
		}

		relationRequestSocket()

		watch(() => user.value.data.id, (userId) => {
			if (userId) {
				relationRequestSocket()
			} else {
				echo.disconnect()
			}
		})
	}
})
</script>
