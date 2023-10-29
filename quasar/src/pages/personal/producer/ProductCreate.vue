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
		<q-page>
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
					@click="storeProduct"
				/>
			</q-page-sticky>
		</q-page>
	</q-page-container>
</template>

<script setup>
import { useRouter } from "vue-router"
import { computed, ref, defineComponent } from "vue"
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
import _ from "lodash"
import { api } from "src/boot/axios"

defineComponent({
	ProducerProductSettingCommonTab,
	ProducerProductSettingCompositionTab,
	ProducerProductSettingImagesTab,
	ProducerProductSettingTagsTab
})

const teamStore = useTeamStore()
const producerStore = useProducerStore()
const $router = useRouter()

const { notifySuccess, notifyError } = useNotification()

const isLoading = ref(false)

const defaultProduct = {
	title: "",
	price: null,
	amount: "",
	composition: [],
	images: [],
	tags: [],
	keywords: []
}

const product = ref({...defaultProduct})

const tab = ref("common")
const commonTab = ref(null)
const compositionTab = ref(null)
const tagsTab = ref(null)

const user_teams = computed(() => teamStore.user_teams)

const team = computed(() =>
	user_teams.value.find((t) => t.detailed.id === parseInt($router.currentRoute.value.params.producer_id))
)

const isProductChanged = computed(() =>
	!_.isEqual(product.value, defaultProduct)
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
				name: "personal_producer_products_detail",
				params: { producer_id: $router.currentRoute.value.params.producer_id },
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
	data.append("team_id", team.value.id)

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
