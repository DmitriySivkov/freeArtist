<script setup>
import { Dialog } from "quasar"
import SettingsDialog from "src/components/dialogs/SettingsDialog.vue"

const showSettings = () => {
	Dialog.create({
		component: SettingsDialog,
		componentProps: {},
	})
}
</script>

<template>
	<div class="row">
		<div class="col-12">
			<q-icon
				name="settings"
				size="2.5em"
				class="cursor-pointer q-my-sm"
				color="white"
				@click="showSettings"
			/>
		</div>
	</div>
</template>
