<script setup>
import { useUserStore } from "src/stores/user"
import { useProducerStore } from "src/stores/producer"
import { computed, ref } from "vue"
import { LOCATION_RANGE, LOCATION_RANGE_LIST, LOCATION_UNKNOWN_ID } from "src/const/userLocation"

const props = defineProps({
	userLocation: Object
})

const emit = defineEmits([
	"change-range"
])

const user_store = useUserStore()
const producer_store = useProducerStore()

const isUserLocationUnknown = computed(() =>
	props.userLocation.id === LOCATION_UNKNOWN_ID
)

const options = [
	{
		label: LOCATION_RANGE_LIST[LOCATION_RANGE.nearby],
		value: LOCATION_RANGE.nearby,
		disable: isUserLocationUnknown.value
	},
	{
		label: LOCATION_RANGE_LIST[LOCATION_RANGE.all],
		value: LOCATION_RANGE.all,
		disable: false
	}
]

const selectedRange = ref(
	!isUserLocationUnknown.value ?
		options.find((o) => o.value === LOCATION_RANGE.nearby).label :
		options.find((o) => o.value === LOCATION_RANGE.all).label
)

const setLocationRange = (range) => {
	selectedRange.value = range.label
	emit("change-range", range.value)
}

const isFetchingProducers = computed(() => producer_store.is_fetching)
</script>

<template>
	<div class="row items-center justify-end">
		<div class="col-xs-12 col-md-4">
			<q-select
				:disable="isFetchingProducers"
				filled
				square
				:model-value="selectedRange"
				:options="options"
				option-disable="disable"
				bg-color="white"
				@update:model-value="setLocationRange"
			/>
		</div>
	</div>
</template>
