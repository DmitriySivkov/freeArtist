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
import { useStore } from "vuex"
import {useNotification} from "src/composables/notification"

export default {
	setup() {
		const $router = useRouter()
		const $store = useStore()
		const { notifySuccess, notifyError } = useNotification()

		const producer = ref(null)

		const onSubmit = () => {
			$store.dispatch("user/registerProducer", {
				producer: producer.value,
			}).then(() => {
				notifySuccess("Заявка успешно отправлена")
				$router.push("personal_user")
			})
				.catch((error) => {
					const errors = Object.values(error.response.data.errors)
						.reduce((accum, val) => accum.concat(...val), [])
					notifyError(errors)
				})
		}

		const onReset = () =>
			producer.value = null

		return {
			producer,
			onSubmit,
			onReset
		}
	},
}
</script>
