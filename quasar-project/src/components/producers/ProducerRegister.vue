<template>
	<div class="q-pa-md row justify-center">
		<div class="col-xs-12 col-md-6 col-lg-4">
			<q-form
				@submit="onSubmit"
				@reset="onReset"
			>
				<q-card class="q-mb-lg">
					<q-card-section
						class="text-center bg-primary text-white"
					>
						Получите доступ к личному кабинету изготовителя.<br/>
						Вы по-прежнему сможете присоединиться к другому изготовителю
					</q-card-section>
				</q-card>

				<q-input
					filled
					class="q-mt-none q-pb-none q-mb-lg"
					v-model="producer"
					label="Введите название *"
					:lazy-rules="true"
					:rules="[
						val => !!val,
					]"
				/>

				<q-select
					filled
					class="q-mb-lg"
					v-model="location"
					use-input
					input-debounce="1000"
					label="Начните вводить название города"
					:options="location_options"
					@filter="loadLocation"
					behavior="dialog"
				>
					<template v-slot:no-option>
						<q-item>
							<q-item-section class="text-grey">
								Город не найден
							</q-item-section>
						</q-item>
					</template>
				</q-select>

				<div class="row q-col-gutter-sm">
					<div class="col-xs-6">
						<q-btn
							label="Подтвердить"
							type="submit"
							color="primary"
							class="q-pa-lg full-width"
						/>
					</div>
					<div class="col-xs-6">
						<q-btn
							label="Сбросить"
							type="reset"
							color="warning"
							class="q-pa-lg full-width"
						/>
					</div>
				</div>
			</q-form>
		</div>
	</div>
</template>

<script>
import { useRouter } from "vue-router"
import { ref } from "vue"
import { api } from "src/boot/axios"
import { useNotification } from "src/composables/notification"
import { usePrivateChannels } from "src/composables/privateChannels"
import { useUserStore } from "src/stores/user"
export default {
	setup() {
		const $router = useRouter()
    const user_store = useUserStore()
		const { notifySuccess, notifyError } = useNotification()
		const private_channels = usePrivateChannels()
		const location = ref(null)
		const location_options = ref(null)

		const producer = ref(null)

		const onSubmit = () => {
			user_store.registerProducer({
				display_name: producer.value,
				city_id: location.value.value
			}).then(() => {
				private_channels.connectRelationRequestTeam()
				notifySuccess("Успешно")
				$router.push({name:"personal_user"})
			}).catch((error) => {
				// todo - bring errors to this view everywhere
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
		}

		const loadLocation = async (query, update) => {
			if (query.length < 1) return

			const response = await api.get("cities",{
				params: { query }
			})

			update(() => {
				location_options.value = response.data.map((location) => {
					return {
						label: location.city + " (" + location.address + ")",
						value: location.id
					}
				})
			})
		}

		const onReset = () =>
			producer.value = null

		return {
			producer,
			onSubmit,
			onReset,
			location,
			location_options,
			loadLocation
		}
	},
}
</script>
