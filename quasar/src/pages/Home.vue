<template>
	<div
		v-if="userLocation"
		class="column no-wrap absolute full-height full-width"
	>
		<div class="col-3">
			<ProducerFilterHome />
		</div>
		<div class="col-grow">
			<ProducerListHome />
		</div>
	</div>
</template>

<script setup>
import { computed, defineComponent, onMounted } from "vue"
import { Dialog } from "quasar"
import ProducerListHome from "src/components/producers/ProducerListHome.vue"
import ProducerFilterHome from "src/components/producers/ProducerFilterHome.vue"
import CheckCityDialog from "components/dialogs/CheckCityDialog.vue"
import { useUserStore } from "src/stores/user"

defineComponent({
	ProducerListHome,
	ProducerFilterHome
})

const userStore = useUserStore()

const userLocation = computed(() => userStore.location)

onMounted(() => {
	if (!userLocation.value) {
		Dialog.create({
			component: CheckCityDialog,
			componentProps: {
				persistent: true
			},
		}).onOk((location) => {
			userStore.setLocation(location)
		})
	}
})

</script>
