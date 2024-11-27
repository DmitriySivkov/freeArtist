<script setup>
import { ref, computed, onMounted } from "vue"
import { Dialog } from "quasar"
import ProducerShowcaseHome from "src/components/producers/ProducerShowcaseHome.vue"
import ProducerFilterHome from "src/components/producers/ProducerFilterHome.vue"
import ProducerCategoriesHome from "src/components/producers/ProducerCategoriesHome.vue"
import SelectLocationDialog from "src/components/dialogs/SelectLocationDialog.vue"
import { useUserStore } from "src/stores/user"
import { useMiscStore } from "src/stores/misc"
import { LOCATION_RANGE, LOCATION_UNKNOWN_ID } from "src/const/userLocation"

const userStore = useUserStore()
const miscStore = useMiscStore()

const userLocation = computed(() => userStore.location)

const setCategories = (categoryIds) => {
	miscStore.setHomePageSelectedCategories(categoryIds)
}

const homePage = ref(null)
const homeHeader = ref(null)
const homeCategories = ref(null)

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

<template>
	<q-page
		ref="homePage"
		class="q-px-sm bg-primary scroll"
		id="home-scroll-container"
		style="height:1px"
	>
		<q-header class="main-layout-header">
			<div class="column no-wrap q-px-sm">
				<div class="col-auto q-mb-xs">
					<div class="row justify-center">
						<div class="col-xs-12 col-sm-8 col-lg-6 col-xl-5">
							<ProducerFilterHome ref="homeHeader"/>
						</div>
					</div>
				</div>
				<div class="col-auto q-mb-xs">
					<div class="row justify-center">
						<div class="col-xs-12 col-sm-8 col-lg-6 col-xl-5">
							<ProducerCategoriesHome
								ref="homeCategories"
								@change="setCategories"
							/>
						</div>
					</div>
				</div>
			</div>
		</q-header>

		<div class="row justify-center">
			<div class="col-xs-12 col-sm-8 col-lg-6 col-xl-5">
				<ProducerShowcaseHome />
			</div>
		</div>

	</q-page>
</template>

<style>
.main-layout-header {
	z-index: 999;
}
</style>
