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

	<div
		v-if="product"
		class="absolute column full-width no-wrap"
		style="min-height:100vh"
	>
		<div class="col">
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
		</div>
		<div class="col-auto sticky__common_bottom bg-white">
			<div class="row q-pa-sm">
				<q-btn
					round
					class="q-pa-md"
					:class="{'composition__button_done_active': isProductChanged}"
					icon="done"
					:loading="isLoading"
					color="primary"
					@click="updateProduct"
				/>
			</div>
		</div>
	</div>

	<div
		v-else
		class="absolute column full-width no-wrap q-pa-md"
		style="min-height:100vh"
	>
		<q-skeleton
			v-for="i in 3"
			:key="i"
			type="QInput"
			class="q-mb-sm"
			bordered
		/>
	</div>

</template>

<script setup>
import { useRouter } from "vue-router"
import { computed, ref, onMounted } from "vue"
import { useNotification } from "src/composables/notification"
import { cloneDeep, isEqual } from "lodash"
import { api } from "src/boot/axios"
import ProducerProductSettingCommonTab
	from "src/components/producers/producerProductSettingsTabs/ProducerProductSettingCommonTab.vue"
import ProducerProductSettingCompositionTab
	from "src/components/producers/producerProductSettingsTabs/ProducerProductSettingCompositionTab.vue"
import ProducerProductSettingImagesTab
	from "src/components/producers/producerProductSettingsTabs/ProducerProductSettingImagesTab.vue"
import ProducerProductSettingTagsTab
	from "src/components/producers/producerProductSettingsTabs/ProducerProductSettingTagsTab.vue"

const $router = useRouter()

const { notifySuccess, notifyError } = useNotification()

const isLoading = ref(false)

const defaultProduct = ref(null)
const product = ref(null)

const tab = ref("common")
const commonTab = ref(null)
const compositionTab = ref(null)
const tagsTab = ref(null)

const isProductChanged = computed(() =>
	!isEqual(product.value, defaultProduct.value)
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
			defaultProduct.value = cloneDeep(product.value)

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
		defaultProduct.value = cloneDeep(product.value)
	})

	promise.catch((error) => {
		notifyError(error.response.data)
	})
})

</script>
