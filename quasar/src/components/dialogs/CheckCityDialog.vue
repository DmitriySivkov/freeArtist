<template>
	<q-dialog
		ref="dialogRef"
		@hide="onDialogHide"
	>
		<q-card class="q-dialog-plugin q-pa-md">
			<span class="text-h5">Выберите ваше местоположение</span>
			<div
				class="row relative-position"
				style="min-height:200px"
			>
				<div class="col-12">
					<q-list
						v-if="locationList"
					>
						<q-item
							v-for="location in locationList"
							:key="location.id"
							class="text-h6"
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
			</div>
			<div class="row q-col-gutter-md">
				<div class="col-xs-12 col-md-6">
					<q-btn
						color="positive"
						label="Другое место"
						class="q-pa-md full-height full-width"
						:disable="isSearchingLocation"
					/>
				</div>
				<div class="col-xs-12 col-md-6">
					<q-btn
						color="red"
						label="Не указывать"
						class="q-pa-md full-height full-width"
						@click="setLocation('unknown')"
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

const locationList = ref(null)
const isSearchingLocation = ref(true)

const setLocation = (location) => {
	onDialogOK(location)
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
