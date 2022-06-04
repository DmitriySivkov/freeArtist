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
						Присоединиться к существующему изготовителю.<br/>
						Вы по-прежнему сможете зарегистрировать собственное название изготовителя
					</q-card-section>
				</q-card>

				<q-select
					class="q-pb-none"
					filled
					v-model="producer"
					use-input
					input-debounce="1000"
					label="Начните вводить название изготовителя *"
					:options="producerList"
					@filter="loadProducerList"
					behavior="dialog"
					:rules="[
						val => !!val,
					]"
				/>

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

		const producerList = ref([])

		const onSubmit = () => {
			$store.dispatch("user/joinProducer", {
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

		const onReset = () =>
			producer.value = null

		const loadProducerList = async (query, update) => {
			if (query.length < 1) return

			const response = await api.get("personal/producers/producersToAttach", { params: { query } })

			update(() => {
				producerList.value = response.data.map((producer) => {
					return {
						label: producer.title,
						value: producer.id
					}
				})
			})
		}

		return {
			producer,
			producerList,
			onSubmit,
			onReset,
			loadProducerList
		}
	},
}
</script>
