<template>
	<router-view />
</template>
<script>
import {defineComponent, watch} from "vue"
import {echo} from "boot/ws"
import {useUserProducer} from "src/composables/userProducer"
import {useUserPermission} from "src/composables/userPermission"
import {useUser} from "src/composables/user"
export default defineComponent({
	name: "App",
	setup() {
		const { producerTeams } = useUserProducer()
		const { hasPermission } = useUserPermission()
		const { user } = useUser()

		watch(() => user.value.data.id, (userId) => {
			if (userId) {
				if (!window.Pusher.isConnected)
					echo.connect()

				echo.private("relation-requests.user." + user.value.data.id)
					.listen(".RelationRequestCreated", (e) => {
						console.log(e)
					})

				if (producerTeams.value.length > 0) {
					for (let i in producerTeams.value) {
						if (
							producerTeams.value[i].user_id === user.value.data.id ||
							hasPermission(producerTeams.value[i].id, ["producer_incoming_coworking_requests"])
						) {
							echo.private("relation-requests.producer." + producerTeams.value[i].id)
								.listen(".RelationRequestCreated", (e) => {
									console.log(e)
								})
						}
					}
				}
			} else {
				echo.disconnect()
			}
		})
	}
})
</script>
