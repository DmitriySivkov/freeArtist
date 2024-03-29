<template>
	<div class="row flex flex-center full-height q-pa-md">
		<div class="col-xs-12 col-sm-8 col-lg-6 col-xl-5">
			<q-card class="q-mb-md">
				<q-card-section
					class="text-center bg-primary text-white text-body1"
				>
					Станьте собственным изготовителем.<br/>
					Вы по-прежнему сможете присоединиться к другому изготовителю
				</q-card-section>
			</q-card>

			<q-form @submit="register">
				<q-input
					filled
					class="q-pb-none q-mb-lg"
					v-model="producer"
					label="Введите название *"
					:lazy-rules="true"
					:rules="[
						val => !!val,
					]"
				/>

				<q-select
					filled
					v-model="location"
					use-input
					input-debounce="300"
					label="Начните вводить название города"
					:options="locationOptions"
					@filter="loadLocation"
					:lazy-rules="true"
					:rules="[
						val => !!val,
					]"
				>
					<template v-slot:no-option>
						<q-item>
							<q-item-section class="text-grey">
								Город не найден
							</q-item-section>
						</q-item>
					</template>
				</q-select>

				<div class="row">
					<div class="col">
						<q-btn
							label="Подтвердить"
							type="submit"
							color="primary"
							class="q-pa-lg full-width text-body1"
							:loading="isLoading"
						/>
					</div>
				</div>
			</q-form>
		</div>
	</div>
</template>

<script setup>
import { useRouter } from "vue-router"
import { ref } from "vue"
import { api } from "src/boot/axios"
import { useNotification } from "src/composables/notification"
import { useTeamStore } from "src/stores/team"
import { useRoleStore } from "src/stores/role"

const $router = useRouter()

const teamStore = useTeamStore()
const roleStore = useRoleStore()

const { notifySuccess, notifyError } = useNotification()

const location = ref(null)
const locationOptions = ref(null)

const producer = ref(null)

const isLoading = ref(false)

const register = () => {
	isLoading.value = true

	const promise = api.post("personal/producers/register", {
		display_name: producer.value,
		city_id: location.value.value
	})

	promise.then((response) => {
		teamStore.setUserTeams(response.data.team)

		roleStore.setUserRole(response.data.role)

		notifySuccess("Успешно")

		$router.push({
			name: "personal"
		})
	})

	promise.catch((error) => {
		if (typeof error.response.data.errors === "object") {
			const errors = Object.values(error.response.data.errors)
				.reduce((accum, val) => accum.concat(...val), [])

			for (let i in errors) {
				notifyError(errors[i])
			}
		} else {
			notifyError(error.response.data.errors)
		}

	})

	promise.finally(() => isLoading.value = false)
}

const loadLocation = async (query, update) => {
	if (query.length < 1) return

	const response = await api.get("cities",{
		params: { query }
	})

	update(() => {
		locationOptions.value = response.data.map((location) => {
			return {
				label: location.city + " (" + location.address + ")",
				value: location.id
			}
		})
	})
}
</script>
