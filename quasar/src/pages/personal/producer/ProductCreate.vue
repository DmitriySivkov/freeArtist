<template>
	<q-tabs
		:model-value="tab"
		@update:model-value="tab = $event"
		active-color="white"
		active-bg-color="secondary"
		indicator-color="transparent"
		align="justify"
		class="text-white bg-indigo-10"
	>
		<q-tab
			name="common"
			label="Общие"
			class="q-pa-md"
		/>
		<q-tab
			name="composition"
			label="Состав"
			class="q-pa-md"
		/>
		<q-tab
			name="images"
			label="Изображения"
			class="q-pa-md"
		/>
	</q-tabs>

	<q-tab-panels
		:model-value="tab"
		animated
		keep-alive
	>
		<q-tab-panel name="common">
			<ProducerProductSettingCommonTab
				ref="commonTab"
				:model-value="product"
				@update:model-value="product = $event"
			/>
		</q-tab-panel>

		<q-tab-panel name="composition">
			<ProducerProductSettingCompositionTab
				ref="compositionTab"
				:model-value="product"
				@update:model-value="product = $event"
			/>
		</q-tab-panel>

		<q-tab-panel name="images">
			<ProducerProductSettingImagesTab
				:model-value="product"
				@update:model-value="product = $event"
			/>
		</q-tab-panel>
	</q-tab-panels>

	<q-page-sticky
		position="bottom-right"
		class="transform-none"
		:offset="[18,18]"
	>
		<q-btn
			round
			size="1.5em"
			:class="{'composition__button_done_active': isProductChanged}"
			icon="done"
			color="secondary"
			@click="storeProduct"
		/>
	</q-page-sticky>
</template>

<script>
import { useRouter } from "vue-router"
import { computed, ref } from "vue"
import { useProducerStore } from "src/stores/producer"
import { useTeamStore } from "src/stores/team"
import { useNotification } from "src/composables/notification"
import ProducerProductSettingCommonTab
	from "src/components/producers/producerProductSettingsTabs/ProducerProductSettingCommonTab.vue"
import ProducerProductSettingCompositionTab
	from "src/components/producers/producerProductSettingsTabs/ProducerProductSettingCompositionTab.vue"
import ProducerProductSettingImagesTab
	from "src/components/producers/producerProductSettingsTabs/ProducerProductSettingImagesTab.vue"
import _ from "lodash"
export default {
	components: {
		ProducerProductSettingCommonTab,
		ProducerProductSettingCompositionTab,
		ProducerProductSettingImagesTab
	},
	setup() {
		const team_store = useTeamStore()
		const producer_store = useProducerStore()
		const $router = useRouter()

		const { notifySuccess, notifyError } = useNotification()

		const defaultProduct = {
			title: "",
			price: null,
			amount: "",
			composition: [],
			images: []
		}

		const product = ref({...defaultProduct})

		const tab = ref("common")

		const commonTab = ref(null)
		const compositionTab = ref(null)

		const user_teams = computed(() => team_store.user_teams)

		const team = computed(() =>
			user_teams.value.find((t) => t.detailed.id === parseInt($router.currentRoute.value.params.producer_id))
		)

		const isProductChanged = computed(() => !_.isEqual(product.value, defaultProduct))

		const storeProduct = () => {
			let validation = validate()

			validation.then((tab_validations) => {
				if (tab_validations.includes(false))
					return

				let tmp_uuid = crypto.randomUUID()

				producer_store.commitProducerNewProduct({
					producer_id: parseInt($router.currentRoute.value.params.producer_id),
					product: {...product.value, tmp_uuid },
				})

				$router.push({
					name: "personal_producer_products_detail",
					params: { producer_id: $router.currentRoute.value.params.producer_id },
					query: { no_fetch: true }
				})

				const promise = producer_store.createProducerProduct({
					product: product.value,
					team_id: team.value.id
				})

				promise.then((response) => {
					producer_store.commitProducerNewProduct({
						producer_id: response.data.producer_id,
						product: response.data,
						tmp_uuid
					})

					notifySuccess("Успешно")
				})

				promise.catch((error) => {

					notifyError(error.response.data)
				})
			})
		}

		const validate = () => {
			let validations = []

			if (!!commonTab.value)
				validations.push(commonTab.value.validate())

			if (!!compositionTab.value)
				validations.push(compositionTab.value.validate())

			return Promise.all(validations)
		}

		return {
			team,
			storeProduct,
			product,
			tab,
			isProductChanged,
			defaultProduct,
			commonTab,
			compositionTab
		}
	}
}
</script>
