<template>
	<q-tabs
		v-model="tab"
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
		v-model="tab"
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
		isAbleToManageProduct: Boolean
	},
	emits: ["isProductChanged", "commitProduct"],
	setup(props, { emit }) {
		const tab = ref("common")

		// const product = ref(
		// 	Object.keys(props.selectedProduct).length === 0 ?
		// 		{
		// 			title: "",
		// 			price: null,
		// 			amount: 0
		// 		} :
		// 		props.selectedProduct
		// )

		const default_common = {
			title: props.selectedProduct.title,
			price: props.selectedProduct.price,
			amount: props.selectedProduct.amount
		}

		const default_composition = props.selectedProduct.composition ? _.cloneDeep(props.selectedProduct.composition) : []

		const common_changed = computed(() => !_.isEqual({
			title: props.selectedProduct.title,
			price: props.selectedProduct.price,
			amount: props.selectedProduct.amount
		}, default_common))

		const composition_changed = computed(() => props.selectedProduct.composition ?
			!_.isEqual(props.selectedProduct.composition, default_composition) : false
		)
		const images_changed = computed(() => props.selectedProduct.hasOwnProperty("committed_images"))

		const is_product_changed = computed(() =>
			common_changed.value ||
			composition_changed.value ||
			images_changed.value
		)

		watch(
			() => is_product_changed.value,
			(val) => emit("isProductChanged", val),
		)

		return {
			tab,
			common_changed
		}
	}
}
</script>
