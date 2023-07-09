<template>
	<q-dialog
		ref="dialogRef"
		@hide="onDialogHide"
	>
		<q-card class="q-pa-md">
			<span class="text-h5">Выберите ваше местоположение</span>
			<div
				class="row relative-position"
				style="min-height:200px"
			>
				<div
					v-if="!isAnotherLocation"
					class="col-12"
				>
					<q-list
						v-if="locationList"
					>
						<q-item
							v-for="location in locationList"
							:key="location.id"
							class="text-h6 text-white bg-primary rounded-borders q-mb-xs"
							clickable
							@click="setLocation(location)"
						>
							{{ location.address }}
						</q-item>
					</q-list>
					<q-inner-loading :showing="isSearchingLocation">
						<q-spinner-gears
							size="42px"
							color="primary"
						/>
					</q-inner-loading>
				</div>
				<div
					v-else
					class="col-12"
				>
					<q-select
						filled
						autofocus
						v-model="location"
						use-input
						input-debounce="300"
						option-value="id"
						option-label="address"
						label="Введите название города"
						behavior="menu"
						:options="locationOptions"
						@filter="loadLocation"
					>
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
			<div class="row q-col-gutter-md">
				<div class="col-xs-12 col-md-6">
					<q-btn
						v-if="!isAnotherLocation"
						color="positive"
						label="Другое место"
						class="q-pa-md full-height full-width"
						:disable="isSearchingLocation"
						@click="isAnotherLocation = true"
					/>
					<q-btn
						v-else
						color="positive"
						label="Продолжить"
						class="q-pa-md full-height full-width"
						:disable="!location"
						@click="setLocation(location)"
					/>
				</div>
				<div class="col-xs-12 col-md-6">
					<q-btn
						color="red"
						label="Не указывать"
						class="q-pa-md full-height full-width"
						@click="setLocation({id:0, address:'unknown'})"
					/>
				</div>
			</div>
		</q-card>
	</q-dialog>
</template>

<script setup>
import { onMounted, ref } from "vue"
import { useDialogPluginComponent } from "quasar"
import { api } from "src/boot/axios"

defineEmits([
	...useDialogPluginComponent.emits,
])

const { dialogRef, onDialogHide, onDialogOK } = useDialogPluginComponent()

const location = ref(null)
const locationOptions = ref([])

const locationList = ref(null)
const isSearchingLocation = ref(true)
const isAnotherLocation = ref(false)

const setLocation = (location) => {
	onDialogOK(location)
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

onMounted(() => {
	const promise = api.get("user/location")

	promise.then((response) => {
		locationList.value = response.data
	})

	promise.catch(() => {
		// todo
	})

	promise.finally(() =>
		isSearchingLocation.value = false
	)
})
</script>
