<template>
	<q-dialog
		ref="dialogRef"
		@hide="onDialogHide"
	>
		<q-card
			class="q-pa-md"
			style="min-height:200px; width:100%"
		>
			<div class="row">
				<div class="col-12">
					<q-select
						filled
						square
						:model-value="selectedRange"
						:options="options"
						option-disable="disable"
						bg-color="white"
						@update:model-value="setLocationRange"
					>
						<template #before>
							<q-icon
								name="my_location"
							/>
						</template>
					</q-select>
				</div>
			</div>

		</q-card>
	</q-dialog>
</template>

<script setup>
import { computed } from "vue"
import { useDialogPluginComponent } from "quasar"
import { useUserStore } from "src/stores/user"
import { useProducerStore } from "src/stores/producer"
import { LOCATION_RANGE, LOCATION_RANGE_LIST, LOCATION_UNKNOWN_ID } from "src/const/userLocation"

const emit = defineEmits([
	...useDialogPluginComponent.emits,
])

const { dialogRef, onDialogHide, onDialogOK } = useDialogPluginComponent()

const userStore = useUserStore()
const producerStore = useProducerStore()

const userLocation = computed(() => userStore.location)

const options = [
	{
		label: LOCATION_RANGE_LIST[LOCATION_RANGE.nearby],
		value: LOCATION_RANGE.nearby,
		disable: userLocation.value.id === LOCATION_UNKNOWN_ID
	},
	{
		label: LOCATION_RANGE_LIST[LOCATION_RANGE.all],
		value: LOCATION_RANGE.all,
		disable: false
	}
]

const selectedRange = computed(() => options.find((o) => o.value === userStore.location_range).label)

const setLocationRange = (range) => {
	userStore.setLocationRange(range.value)
}
</script>
