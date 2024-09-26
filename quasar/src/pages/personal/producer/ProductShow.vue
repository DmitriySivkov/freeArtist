<script setup>
import { useRouter } from "vue-router"
import { computed, ref, onMounted } from "vue"
import { useNotification } from "src/composables/notification"
import { cloneDeep, isEqual } from "lodash"
import { api } from "src/boot/axios"
import PersonalProducerProductFooter from "src/layouts/PersonalProducerProductFooter.vue"
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

<template>
	<q-header>
		<q-tabs
			:model-value="tab"
			@update:model-value="tab = $event"
			active-color="white"
			active-bg-color="primary"
			indicator-color="transparent"
			align="justify"
			class="text-white bg-indigo-8"
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
	</q-header>

	<q-page class="row justify-center">
		<div class="col-xs-12 col-md-8 col-lg-7 col-xl-6 bg-white">
			<template v-if="!product">
				<q-linear-progress
					indeterminate
					color="primary"
				/>
			</template>
			<template v-else>
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
			</template>
		</div>
	</q-page>

	<PersonalProducerProductFooter
		:is-loading="isLoading"
		:is-product-changed="isProductChanged"
		@confirm="updateProduct"
	/>
</template>
