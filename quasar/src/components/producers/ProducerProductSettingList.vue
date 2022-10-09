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
			:disable="is_empty_product"
			name="composition"
			label="Состав"
			class="q-pa-md"
		/>
		<q-tab
			:disable="is_empty_product"
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
				:selected-product="selectedProduct"
				:permissions="permissions"
				@product-created="$emit('productCreated', $event)"
				@product-deleted="$emit('productDeleted', $event)"
			/>
		</q-tab-panel>

		<q-tab-panel name="composition">
			<ProducerProductSettingCompositionTab
				:selected-product="selectedProduct"
				:permissions="permissions"
			/>
		</q-tab-panel>

		<q-tab-panel name="images">
			<ProducerProductSettingImagesTab
				:selected-product="selectedProduct"
				:permissions="permissions"
			/>
		</q-tab-panel>
	</q-tab-panels>
</template>

<script>
import { ref, computed } from "vue"
import ProducerProductSettingCommonTab from "src/components/producers/producerProductSettingsTabs/ProducerProductSettingCommonTab"
import ProducerProductSettingCompositionTab from "src/components/producers/producerProductSettingsTabs/ProducerProductSettingCompositionTab"
import ProducerProductSettingImagesTab from "src/components/producers/producerProductSettingsTabs/ProducerProductSettingImagesTab"
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
		permissions: {
			type: Object,
			default: () => ({})
		}
	},
	emits: ["productCreated", "productDeleted"],
	setup(props) {
		const tab = ref("common")
		const is_empty_product = computed(() => Object.keys(props.selectedProduct).length === 0)

		return {
			tab,
			is_empty_product,
		}
	}
}
</script>
