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
			<ProducerProductSettingCommonTab v-model="product" />
		</q-tab-panel>

		<!--		<q-tab-panel name="composition">-->
		<!--			<ProducerProductSettingCompositionTab-->
		<!--				:selected-product="selectedProduct"-->
		<!--				:is-able-to-manage-product="isAbleToManageProduct"-->
		<!--			/>-->
		<!--		</q-tab-panel>-->

		<!--		<q-tab-panel name="images">-->
		<!--			<ProducerProductSettingImagesTab-->
		<!--				:selected-product="selectedProduct"-->
		<!--				:is-able-to-manage-product="isAbleToManageProduct"-->
		<!--			/>-->
		<!--		</q-tab-panel>-->
	</q-tab-panels>
</template>

<script>
import { ref } from "vue"
import ProducerProductSettingCommonTab from "src/components/producers/producerProductSettingsTabs/ProducerProductSettingCommonTab.vue"
import ProducerProductSettingCompositionTab from "src/components/producers/producerProductSettingsTabs/ProducerProductSettingCompositionTab.vue"
import ProducerProductSettingImagesTab from "src/components/producers/producerProductSettingsTabs/ProducerProductSettingImagesTab.vue"
export default {
	components: {
		ProducerProductSettingCommonTab,
		// ProducerProductSettingCompositionTab,
		// ProducerProductSettingImagesTab
	},
	props: {
		selectedProduct: {
			type: Object,
			default: () => ({})
		},
		isAbleToManageProduct: Boolean
	},
	setup(props) {
		const tab = ref("common")

		const product = ref(props.selectedProduct)

		return {
			tab,
			product
		}
	}
}
</script>
