<template>
	<q-page
		ref="homePage"
		class="column no-wrap q-px-sm bg-primary"
	>
		<div class="col-auto">
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
		<div
			class="col-grow scroll"
			:style="{'max-height': `${producerListMaxHeight}px`}"
		>
			<div class="row justify-center">
				<div class="col-xs-12 col-sm-8 col-lg-6 col-xl-5">
					<ProducerShowcaseHome
						:categories="selectedCategories"
					/>
				</div>
			</div>
		</div>
	</q-page>
</template>

<script setup>
import { ref, computed, onMounted } from "vue"
import { Dialog } from "quasar"
import ProducerShowcaseHome from "src/components/producers/ProducerShowcaseHome.vue"
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

const homePage = ref(null)
const homeHeader = ref(null)
const homeCategories = ref(null)

//todo - посчитать нормально, сейчас есть отклонение в несколько пикселей (хардкод "-4" в подсчете)
const producerListMaxHeight = computed(() =>
	homePage.value?.$el.clientHeight - homeHeader.value?.$el?.offsetHeight - homeCategories.value?.$el?.offsetHeight - 4
)

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
