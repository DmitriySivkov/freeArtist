<script setup>
import { useRouter } from "vue-router"
import { computed, ref } from "vue"
import { useNotification } from "src/composables/notification"
import { isEqual } from "lodash"
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

const defaultProduct = {
	title: "",
	price: null,
	amount: "",
	composition: [],
	images: [],
	tags: []
}

const product = ref({...defaultProduct})

const tab = ref("common")
const commonTab = ref(null)
const compositionTab = ref(null)
const tagsTab = ref(null)

const tabOffsetPixels = 64

const tabPanelStyleFn = (offset) => {
	return { minHeight: `calc(100vh - ${offset}px)` }
}

const isProductChanged = computed(() =>
	!isEqual(product.value, defaultProduct)
)

const storeProduct = () => {
	if (!isProductChanged.value) return

	let validation = validate()

	validation.then((tab_validations) => {
		if (tab_validations.includes(false))
			return

		isLoading.value = true

		const promise = store()

		promise.then(() => {
			$router.push({
				name: "personal_producer_products",
				params: { team_id: $router.currentRoute.value.params.team_id },// todo - решить что прокидывать producer_id или team_id
			})

			notifySuccess(`Продукт «${product.value.title}» успешно создан`)
		})

		promise.catch((error) => {
			notifyError(error.response.data)
		})

		promise.finally(() => isLoading.value = false)
	})
}

const store = () => {
	let data = new FormData()

	data.append("product", JSON.stringify(product.value))
	data.append("producer_id", $router.currentRoute.value.params.producer_id)

	for (let i in product.value.committed_images) {
		data.append("images[]", product.value.committed_images[i].instance)
	}

	return api.post("personal/products", data)
}

const validate = () => {
	let validations = []

	if (!!commonTab.value)
		validations.push(commonTab.value.validate())

	if (!!compositionTab.value)
		validations.push(compositionTab.value.validate())

	return Promise.all(validations)
}
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
			:style="{height: `${tabOffsetPixels}px`}"
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
			<q-tab-panels
				:model-value="tab"
				animated
				keep-alive
				class="full-width"
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
	</q-page>

	<PersonalProducerProductFooter
		:is-loading="isLoading"
		:is-product-changed="isProductChanged"
		@confirm="storeProduct"
	/>
</template>
