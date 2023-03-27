<template>
	<q-tab-panels
		:model-value="tab"
		animated
	>
		<q-tab-panel name="common">
			<ProducerProductSettingCommonTab
				:model-value="selectedProduct"
				@update:model-value="$emit('commitProduct', $event)"
			/>
		</q-tab-panel>

		<q-tab-panel name="composition">
			<ProducerProductSettingCompositionTab
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
import { computed, watch } from "vue"
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
		const default_common = {
			title: props.selectedProduct.title,
			price: props.selectedProduct.price,
			amount: props.selectedProduct.amount
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
			amount: props.selectedProduct.amount
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
					is_changed: common_changed || composition_changed || images_changed,
					changes: {
						common: common_changed,
						composition: composition_changed,
						images: images_changed
					}
				})
			}
		)
	}
}
</script>
