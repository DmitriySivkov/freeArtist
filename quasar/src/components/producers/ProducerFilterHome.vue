<script setup>
import { ref, computed } from "vue"
import { Dialog } from "quasar"
import { LOCATION_RANGE, LOCATION_UNKNOWN_ID } from "src/const/userLocation.js"
import { useUserStore } from "src/stores/user"
import SelectLocationDialog from "src/components/dialogs/SelectLocationDialog.vue"
import SettingsDialog from "src/components/dialogs/SettingsDialog.vue"

const userStore = useUserStore()

const range = computed(() => userStore.location_range)
const location = computed(() => userStore.location)

const setLocationRange = (range) => {
	if (location.value.id !== LOCATION_UNKNOWN_ID) {
		userStore.setLocationRange(range)
	} else {
		Dialog.create({
			component: SelectLocationDialog,
		}).onOk((location) => {
			userStore.setLocation(location)

			userStore.setLocationRange(
				location.id === LOCATION_UNKNOWN_ID ?
					LOCATION_RANGE.all :
					LOCATION_RANGE.nearby
			)
		})
	}
}

const showSettings = () => {
	Dialog.create({
		component: SettingsDialog,
		componentProps: {},
	})
}

const search = ref("")
const isSearchVisible = ref(false)

const toggleSearchVisibility = (isVisible) => {
	isSearchVisible.value = isVisible
}
</script>

<template>
	<div class="row items-center">
		<div
			v-if="!isSearchVisible"
			class="col-shrink"
		>
			<q-icon
				name="search"
				size="2em"
				class="cursor-pointer q-my-sm"
				color="white"
				@click="toggleSearchVisibility(true)"
			/>
		</div>
		<div
			v-else
			class="col"
		>
			<q-input
				dense
				outlined
				filled
				v-model="search"
			>
				<template v-slot:append>
					<q-icon
						name="clear"
						class="cursor-pointer"
						@click="toggleSearchVisibility(false)"
					/>
				</template>
			</q-input>
		</div>
		<div
			v-if="!isSearchVisible"
			class="col text-right q-pr-md"
		>
			<q-toggle
				:model-value="range"
				:true-value="LOCATION_RANGE.nearby"
				:false-value="LOCATION_RANGE.all"
				size="2em"
				color="indigo-8"
				label="Рядом с Вами"
				left-label
				class="text-white text-h6 range-toggle"
				@update:model-value="setLocationRange"
			/>
		</div>
		<div class="col-shrink">
			<q-icon
				name="settings"
				size="2em"
				class="cursor-pointer q-my-sm"
				color="white"
				@click="showSettings"
			/>
		</div>
	</div>
</template>

<style>
.range-toggle > .q-toggle__inner {
	padding-right: 0;
}
.range-toggle .q-toggle__inner--truthy .q-toggle__thumb {
	left: 0.9em;
}
</style>
