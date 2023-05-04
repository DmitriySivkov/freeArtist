<template>
	<q-tab-panels
		:model-value="tab"
		animated
		keep-alive
	>
		<q-tab-panel name="common">
			<ProducerProductSettingCommonTab
				ref="producer_product_setting_common_tab"
				:model-value="product"
				@update:model-value="product = $event"
			/>
		</q-tab-panel>

		<q-tab-panel name="composition">
			<ProducerProductSettingCompositionTab
				ref="producer_product_setting_composition_tab"
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
</template>

<script>
import { ref, computed, watch } from "vue"
import ProducerProductSettingCommonTab from "src/components/producers/producerProductSettingsTabs/ProducerProductSettingCommonTab.vue"
import ProducerProductSettingCompositionTab from "src/components/producers/producerProductSettingsTabs/ProducerProductSettingCompositionTab.vue"
import ProducerProductSettingImagesTab from "src/components/producers/producerProductSettingsTabs/ProducerProductSettingImagesTab.vue"
import _ from "lodash"
export default {
	components: {
		ProducerProductSettingCommonTab,
		ProducerProductSettingCompositionTab,
		ProducerProductSettingImagesTab
	},
	props: {
		selectedProduct: {
			type: Object,
			default: () => ({})
		},
		isAbleToManageProduct: Boolean,
		tab: String
	},
	emits: [
		"productChanged",
		"commitProduct",
	],
	setup(props, { emit }) {
		const product = ref({})

		const producer_product_setting_common_tab = ref(null)
		const producer_product_setting_composition_tab = ref(null)

		const validate = () => {
			let validations = []

			if (!!producer_product_setting_common_tab.value)
				validations.push(producer_product_setting_common_tab.value.validate())

			if (!!producer_product_setting_composition_tab.value)
				validations.push(producer_product_setting_composition_tab.value.validate())

			return Promise.all(validations)
		}

		const default_common = {
			title: product.value.title,
			price: product.value.price,
			amount: product.value.amount,
			thumbnail_id: product.value.thumbnail_id
		}

		const default_composition = product.value.composition ?
			_.cloneDeep(product.value.composition) :
			[]

		const default_images = product.value.images ?
			_.cloneDeep(product.value.images) :
			[]

		const is_common_changed = computed(() => !_.isEqual({
			title: product.value.title,
			price: product.value.price,
			amount: product.value.amount,
			thumbnail_id: product.value.thumbnail_id
		}, default_common))

		const is_composition_changed = computed(() => product.value.composition ?
			!_.isEqual(product.value.composition, default_composition) : false
		)

		const is_images_changed = computed(() =>
			(product.value.images ? !_.isEqual(product.value.images, default_images) : false) ||
			product.value.hasOwnProperty("committed_images")
		)

		watch(
			[is_common_changed, is_composition_changed, is_images_changed],
			([common_changed, composition_changed, images_changed]) => {
				emit("productChanged", {
					common: common_changed,
					composition: composition_changed,
					images: images_changed
				})
			}
		)

		return {
			producer_product_setting_common_tab,
			producer_product_setting_composition_tab,
			validate,
			is_common_changed,
			is_composition_changed,
			is_images_changed,
			product
		}
	}
}
</script>
