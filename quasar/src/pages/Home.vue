<template>
	<div
		v-if="userLocation"
		class="column"
	>
		<div class="col-auto">
			<div class="row justify-center">
				<div class="col-xs-12 col-sm-8 col-lg-6 col-xl-5">
					<ProducerFilterHome />
				</div>
			</div>
		</div>
		<div class="col-auto q-mb-sm">
			<div class="row justify-center">
				<div class="col-xs-12 col-sm-8 col-lg-6 col-xl-5">
					<ProducerCategoriesHome
						@change="setCategories"
					/>
				</div>
			</div>
		</div>
		<div class="col-grow">
			<div class="row justify-center">
				<div class="col-xs-12 col-sm-8 col-lg-6 col-xl-5">
					<ProducerListHome
						:categories="selectedCategories"
					/>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue"
import { Dialog } from "quasar"
import ProducerListHome from "src/components/producers/ProducerListHome.vue"
import ProducerFilterHome from "src/components/producers/ProducerFilterHome.vue"
import ProducerCategoriesHome from "src/components/producers/ProducerCategoriesHome.vue"
import SelectLocationDialog from "components/dialogs/SelectLocationDialog.vue"
import { useUserStore } from "src/stores/user"
import { LOCATION_RANGE, LOCATION_UNKNOWN_ID } from "src/const/userLocation"

const userStore = useUserStore()

const userLocation = computed(() => userStore.location)

const selectedCategories = ref([])

const setCategories = (categoryIds) => {
	selectedCategories.value = categoryIds
}

onMounted(() => {
	if (!userLocation.value) {
		Dialog.create({
			component: SelectLocationDialog,
			componentProps: {
				persistent: true
			},
		}).onOk((location) => {
			userStore.setLocation(location)

			userStore.setLocationRange(
				location.id === LOCATION_UNKNOWN_ID ?
					LOCATION_RANGE.all :
					LOCATION_RANGE.nearby
			)
		})
	}
})

</script>
