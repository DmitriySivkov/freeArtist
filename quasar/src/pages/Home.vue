<template>
	<div
		v-if="userLocation"
		class="column no-wrap absolute full-height full-width"
	>
		<div class="col-3">
			<ProducerFilterHome
				:user-location="userLocation"
				@change-range="userRange = $event"
			/>
		</div>
		<div class="col-grow">
			<ProducerListHome
				:user-range="userRange"
			/>
		</div>
	</div>
</template>

<script setup>
import { ref, computed, defineComponent, onMounted } from "vue"
import { Dialog } from "quasar"
import ProducerListHome from "src/components/producers/ProducerListHome.vue"
import ProducerFilterHome from "src/components/producers/ProducerFilterHome.vue"
import CheckCityDialog from "components/dialogs/CheckCityDialog.vue"
import { useUserStore } from "src/stores/user"
import { LOCATION_RANGE, LOCATION_UNKNOWN_ID } from "src/const/userLocation"

defineComponent({
	ProducerListHome,
	ProducerFilterHome
})

const userStore = useUserStore()

const userLocation = computed(() => userStore.location)

const userRange = ref(
	userStore.location && userStore.location.id === LOCATION_UNKNOWN_ID ?
		LOCATION_RANGE.all :
		LOCATION_RANGE.nearby
)

onMounted(() => {
	if (!userLocation.value) {
		Dialog.create({
			component: CheckCityDialog,
			componentProps: {
				persistent: true
			},
		}).onOk((location) => {
			userStore.setLocation(location)

			userRange.value = location.id === LOCATION_UNKNOWN_ID ?
				LOCATION_RANGE.all :
				LOCATION_RANGE.nearby
		})
	}
})

</script>
