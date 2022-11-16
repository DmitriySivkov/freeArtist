<template>
	<router-view />
</template>
<script>
import { defineComponent, watch } from "vue"
import { echo } from "boot/ws"
import { useUser } from "src/composables/user"
import { usePrivateChannels } from "src/composables/privateChannels"

export default defineComponent({
	name: "App",
	setup() {
		const { user } = useUser()
		const private_channels = usePrivateChannels()

		const connectPrivateChannels = () => {
			if (!window.Pusher.isConnected)
				echo.connect()

			if (user.value.data.id) {
				private_channels.connectRelationRequestUser()
				private_channels.connectRelationRequestIncomingProducer()
				private_channels.connectPermissions()
			}
		}

		connectPrivateChannels()

		watch(() => user.value.data.id, (userId) => {
			if (userId) {
				connectPrivateChannels()
			} else {
				echo.disconnect()
			}
		})
	}
})
</script>
