<template>
	<router-view />
</template>

<script setup>
import { watch } from "vue"
import { echo } from "src/boot/ws"
import { useUserStore } from "src/stores/user"
import { usePrivateChannels } from "src/composables/privateChannels"
import { Plugins } from "@capacitor/core"
import { Platform } from "quasar"

const userStore = useUserStore()

const { StatusBar } = Plugins

const privateChannels = usePrivateChannels()

const connectPrivateChannels = () => {
	if (!window.Pusher.isConnected) {
		echo.connect()
	}

	if (userStore.data.id) {
		privateChannels.connectTeams()
		privateChannels.connectPermissions()
		privateChannels.connectRelationRequests()
		privateChannels.connectProducerOrders() // todo - connect on orders calendar mount & disconnect on unmount
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

watch(() => userStore.teams.length, () => {
	echo.disconnect()

	connectPrivateChannels()
})
</script>
