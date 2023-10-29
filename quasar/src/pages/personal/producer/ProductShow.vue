<template>
	<q-tabs
		:model-value="tab"
		@update:model-value="tab = $event"
		active-color="white"
		active-bg-color="primary"
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
		<q-tab
			name="tags"
			label="Теги"
			class="q-pa-md"
		/>
	</q-tabs>

	<q-page-container>
		<q-page v-if="product">
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

				<q-tab-panel name="tags">
					<ProducerProductSettingTagsTab
						ref="tagsTab"
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
				<!-- todo - button reloads app on click on mobile resolution -->
				<q-btn
					round
					size="1.5em"
					:class="{'composition__button_done_active': isProductChanged}"
					icon="done"
					:loading="isLoading"
					color="primary"
					@click="updateProduct"
				/>
			</q-page-sticky>
		</q-page>
		<q-page
			v-else
			class="absolute column fit q-pa-md"
		>
			<q-skeleton
				v-for="i in 3"
				:key="i"
				type="QInput"
				class="col-1 q-mb-md"
				bordered
			/>
		</q-page>
	</q-page-container>

</template>

<script setup>
import { useRouter } from "vue-router"
import { computed, defineComponent, ref, onMounted } from "vue"
import { useProducerStore } from "src/stores/producer"
import { useTeamStore } from "src/stores/team"
import { useNotification } from "src/composables/notification"
import ProducerProductSettingCommonTab
	from "src/components/producers/producerProductSettingsTabs/ProducerProductSettingCommonTab.vue"
import ProducerProductSettingCompositionTab
	from "src/components/producers/producerProductSettingsTabs/ProducerProductSettingCompositionTab.vue"
import ProducerProductSettingImagesTab
	from "src/components/producers/producerProductSettingsTabs/ProducerProductSettingImagesTab.vue"
import ProducerProductSettingTagsTab
	from "src/components/producers/producerProductSettingsTabs/ProducerProductSettingTagsTab.vue"
// todo - replace all '_' lodash imports for specific functions
import _ from "lodash"
import { api } from "src/boot/axios"

defineComponent({
	ProducerProductSettingCommonTab,
	ProducerProductSettingCompositionTab,
	ProducerProductSettingImagesTab,
	ProducerProductSettingTagsTab
})

const team_store = useTeamStore()
const producer_store = useProducerStore()
const $router = useRouter()

const { notifySuccess, notifyError } = useNotification()

const user_teams = computed(() => team_store.user_teams)

const team = computed(() =>
	user_teams.value.find((t) => t.detailed.id === parseInt($router.currentRoute.value.params.producer_id))
)

const isLoading = ref(false)

const defaultProduct = ref(null)
const product = ref(null)

const tab = ref("common")
const commonTab = ref(null)
const compositionTab = ref(null)
const tagsTab = ref(null)

const isProductChanged = computed(() =>
	!_.isEqual(product.value, defaultProduct.value)
)

const updateProduct = () => {
	if (!isProductChanged.value) return

	let validation = validate()

	validation.then((tab_validations) => {
		if (tab_validations.includes(false))
			return

		isLoading.value = true

		const promise = update()

		promise.then((response) => {
			product.value = response.data
			defaultProduct.value = _.cloneDeep(product.value)

			notifySuccess(`Продукт «${product.value.title}» успешно обновлён`)
		})

		promise.catch((error) => {
			product.value = defaultProduct.value

			notifyError(error.response.data)
		})

		promise.finally(() => isLoading.value = false)
	})
}

const update = () => {
	let data = new FormData()

	data.append("product", JSON.stringify(product.value))

	for (let i in product.value.committed_images) {
		data.append("images[]", product.value.committed_images[i].instance)
	}

	return api.post("personal/products/" + product.value.id, data)
}

const validate = () => {
	let validations = []

	if (!!commonTab.value)
		validations.push(commonTab.value.validate())

	if (!!compositionTab.value)
		validations.push(compositionTab.value.validate())

	return Promise.all(validations)
}

onMounted(() => {
	const promise = api.get(
		`personal/producers/${$router.currentRoute.value.params.producer_id}/products/${$router.currentRoute.value.params.product_id}`
	)

	promise.then((response) => {
		product.value = response.data
		defaultProduct.value = _.cloneDeep(product.value)
	})

	promise.catch((error) => {
		notifyError(error.response.data)
	})
})

</script>
