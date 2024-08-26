<template>
	<router-view />
</template>

<script>
import { watch } from "vue"
import { echo } from "src/boot/ws"
import { useUserStore } from "src/stores/user"
import { usePrivateChannels } from "src/composables/privateChannels"
import { Plugins } from "@capacitor/core"
import { Platform } from "quasar"
export default {
	setup() {
		const userStore = useUserStore()

		const { StatusBar } = Plugins

		const private_channels = usePrivateChannels()

		const connectPrivateChannels = () => {
			if (!window.Pusher.isConnected) {
				echo.connect()
			}

			if (userStore.data.id) {
				private_channels.connectTeams()
				private_channels.connectPermissions()
				private_channels.connectRelationRequests()
				private_channels.connectProducerOrders() // todo - connect on orders calendar mount & disconnect on unmount
			}
		}

		const setStatusBar = () => {
			StatusBar.setBackgroundColor({
				color: "#fe724c"
			})
		}

		if (Platform.is.capacitor) {
			setStatusBar()
		}

		connectPrivateChannels()

		watch(() => userStore.data.id, (userId) => {
			if (userId) {
				connectPrivateChannels()
			} else {
				echo.disconnect()
			}
		})
	}
}
</script>
