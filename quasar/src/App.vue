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
			if (!window.Pusher.isConnected)
				echo.connect()

			if (userStore.data.id) {
				private_channels.connectTeams()
				private_channels.connectRelationRequestUser()
				private_channels.connectRelationRequestTeam()
				private_channels.connectPermissions()
			}
		}

		const setStatusBar = () => {
			StatusBar.setBackgroundColor({
				color: "#1952a6"
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
