<template>
	<q-tab-panels
		:model-value="tab"
		animated
		keep-alive
	>
		<q-tab-panel name="common">
			<ProducerProductSettingCommonTab
				ref="producer_product_setting_common_tab"
				:model-value="selectedProduct"
				@update:model-value="$emit('commitProduct', $event)"
			/>
		</q-tab-panel>

		<q-tab-panel name="composition">
			<ProducerProductSettingCompositionTab
				ref="producer_product_setting_composition_tab"
				:model-value="selectedProduct"
				@update:model-value="$emit('commitProduct', $event)"
			/>
		</q-tab-panel>

		<q-tab-panel name="images">
			<ProducerProductSettingImagesTab
				:model-value="selectedProduct"
				@update:model-value="$emit('commitProduct', $event)"
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
			default: () => {}
		},
		isAbleToManageProduct: Boolean,
		tab: String
	},
	emits: [
		"productChanged",
		"commitProduct",
	],
	setup(props, { emit }) {
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
			title: props.selectedProduct.title,
			price: props.selectedProduct.price,
			amount: props.selectedProduct.amount,
			thumbnail_id: props.selectedProduct.thumbnail_id
		}

		const default_composition = props.selectedProduct.composition ?
			_.cloneDeep(props.selectedProduct.composition) :
			[]

		const default_images = props.selectedProduct.images ?
			_.cloneDeep(props.selectedProduct.images) :
			[]

		const is_common_changed = computed(() => !_.isEqual({
			title: props.selectedProduct.title,
			price: props.selectedProduct.price,
			amount: props.selectedProduct.amount,
			thumbnail_id: props.selectedProduct.thumbnail_id
		}, default_common))

		const is_composition_changed = computed(() => props.selectedProduct.composition ?
			!_.isEqual(props.selectedProduct.composition, default_composition) : false
		)

		const is_images_changed = computed(() =>
			(props.selectedProduct.images ? !_.isEqual(props.selectedProduct.images, default_images) : false) ||
			props.selectedProduct.hasOwnProperty("committed_images")
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
			default_common,
			is_common_changed,
			is_composition_changed,
			is_images_changed
		}
	}
}
</script>
