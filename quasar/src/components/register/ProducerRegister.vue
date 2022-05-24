<template>
	<div class="q-pa-md row justify-center">
		<div class="col-xs-12 col-md-6 col-lg-4">
			<q-form
				@submit="onSubmit"
				@reset="onReset"
			>
				<div class="row q-col-gutter-sm">
					<div
						class="col-xs-12 col-md-6"
						v-for="item in producer_radio"
						:key="item.value"
					>
						<q-card
							class="bg-primary"
							dark
						>
							<q-card-section>
								<q-radio
									v-model="producer_radio_model"
									:val="item.value"
									dark
									color="white"
									@update:model-value="this.producerRadioSwitched"
								>
									{{ item.label }}
								</q-radio>
							</q-card-section>
						</q-card>
					</div>
				</div>

				<div
					v-if="producer_radio_model === 1"
					class="q-mt-lg"
				>
					<q-select
						filled
						v-model="producer"
						use-input
						input-debounce="1000"
						label="Найти изготовителя *"
						:options="producerList"
						@filter="loadProducerList"
						behavior="dialog"
						emit-value
					>

					</q-select>
				</div>
				<div
					v-if="producer_radio_model === 2"
					class="q-mt-lg"
				>
					<q-input
						filled
						class="q-mt-none q-pb-none"
						v-model="new_producer_title"
						label="Название изготовителя *"
						:lazy-rules="true"
						:rules="[
							val => !!val || 'Введите название',
						]"
					/>
				</div>

				<div class="row q-col-gutter-sm q-mt-lg">
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
import { useQuasar } from "quasar"
import { ref } from "vue"
import { useStore } from "vuex"
import { api } from "src/boot/axios"

export default {
	setup() {
		const $q = useQuasar()
		const $store = useStore()

		const producer = ref(null)

		const new_producer_title = ref("")

		const producer_radio_model = ref(null)
		const producer_radio = [
			{label:"Присоединиться к изготовителю", value:1},
			{label:"Создать нового изготовителя", value:2}
		]

		const producerList = ref([])

		const onSubmit = () => {
			if (!producer_radio_model.value) {
				$q.notify({
					color: "red-5",
					textColor: "white",
					icon: "warning",
					message: "Выберите одну из опций"
				})
				return
			}
			$store.dispatch("user/signUp", {
				new_producer_title: new_producer_title.value,
				producer: producer.value,
			}).then(() => {

			})
				.catch((error) => {
					const errors = Object.values(error.response.data.errors)
						.reduce((accum, val) => accum.concat(...val), [])
					for (var val of errors) {
						$q.notify({
							color: "red-5",
							textColor: "white",
							multiline: true,
							icon: "warning",
							message: val
						})
					}
				})
		}

		const onReset = () => {
			producer.value = null
			producer_radio_model.value = null
		}

		const loadProducerList = async (text, update) => {
			if (text.length < 1) return

			const response = await api.get("producers")

			update(() => {
				producerList.value = response.data.map((producer) => {
					return {
						label: producer.title,
						value: producer.id
					}
				})
			})
		}

		const producerRadioSwitched = () => {
			producer.value = null
			new_producer_title.value = ""
		}

		return {
			producer,
			producerList,
			producer_radio,
			producer_radio_model,
			new_producer_title,
			onSubmit,
			onReset,
			loadProducerList,
			producerRadioSwitched
		}
	},
}
</script>
