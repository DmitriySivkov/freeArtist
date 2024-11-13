<template>
	<router-view />
</template>

<script setup>
import { watch } from "vue"
import { echo } from "src/boot/ws"
import { useUserStore } from "src/stores/user"
import { usePrivateChannels } from "src/composables/privateChannels"
import { StatusBar } from "@capacitor/status-bar"
import { Platform } from "quasar"

const userStore = useUserStore()

const privateChannels = usePrivateChannels()

const connectPrivateChannels = () => {
	if (!window.Pusher.isConnected) {
		echo.connect()
	}

	if (userStore.data.id) {
		privateChannels.connectTeams()
		privateChannels.connectPermissions()
		privateChannels.connectRelationRequests()
	}
}

if (Platform.is.capacitor) {
	StatusBar.setBackgroundColor({
		color: "#fe724c"
	})
}

connectPrivateChannels()

watch(() => userStore.data.id, (userId) => {
	if (userId) {
		connectPrivateChannels()
	} else {
		echo.disconnect()
	}
})

watch(() => userStore.teams.length, () => {
	echo.disconnect()

	connectPrivateChannels()
})
</script>
