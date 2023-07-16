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
						autofocus
						:model-value="location"
						use-input
						input-debounce="300"
						option-value="id"
						option-label="address"
						label="Введите название города"
						behavior="menu"
						:options="locationOptions"
						@filter="loadLocation"
						@update:model-value="setLocation"
					>
						<template #before>
							<q-icon name="place" />
						</template>
						<template v-slot:no-option>
							<q-item>
								<q-item-section class="text-grey">
									Город не найден
								</q-item-section>
							</q-item>
						</template>
					</q-select>
				</div>
			</div>

		</q-card>
	</q-dialog>
</template>

<script setup>
import { computed, ref } from "vue"
import { useDialogPluginComponent } from "quasar"
import { useUserStore } from "src/stores/user"
import { api } from "src/boot/axios"

const emit = defineEmits([
	...useDialogPluginComponent.emits,
])

const { dialogRef, onDialogHide, onDialogOK } = useDialogPluginComponent()

const userStore = useUserStore()

const locationOptions = ref([])
const location = computed(() => userStore.location)

const setLocation = (location) => {
	userStore.setLocation(location)
}

const loadLocation = async (query, update) => {
	if (query.length < 1) return

	const response = await api.get("cities",{
		params: { query }
	})

	update(() => {
		locationOptions.value = response.data.map((location) =>
			({
				id: location.id,
				address: location.address
			})
		)
	})
}
</script>
