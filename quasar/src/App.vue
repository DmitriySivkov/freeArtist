<template>
	<router-view />
</template>

<script>
import { defineComponent, watch } from "vue"
import { echo } from "boot/ws"
import { useUser } from "src/composables/user"
import { usePrivateChannels } from "src/composables/privateChannels"
import { Plugins } from "@capacitor/core"
import { useQuasar } from "quasar"
export default defineComponent({
	name: "App",
	setup() {
		const $q = useQuasar()
		const { StatusBar } = Plugins
		const { user } = useUser()
		const private_channels = usePrivateChannels()

		const connectPrivateChannels = () => {
			if (!window.Pusher.isConnected)
				echo.connect()

			if (user.value.data.id) {
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

		if ($q.platform.is.capacitor) {
			setStatusBar()
		}

		connectPrivateChannels()

		watch(() => user.value.data.id, (userId) => {
			if (userId) {
				connectPrivateChannels()
			} else {
				echo.disconnect()
			}
		})
	},
})
</script>
