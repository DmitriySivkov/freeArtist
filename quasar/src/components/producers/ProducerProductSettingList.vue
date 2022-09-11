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
			class="q-pa-sm"
		/>
		<q-tab
			name="composition"
			label="Состав"
			class="q-pa-sm"
		/>
		<q-tab
			name="images"
			label="Изображения"
			class="q-pa-sm"
		/>
	</q-tabs>

	<q-tab-panels
		v-model="tab"
		animated
	>
		<q-tab-panel name="common">
			<ProducerProductSettingCommonTab :selected-product="selectedProduct"/>
		</q-tab-panel>

		<q-tab-panel name="composition">
			<ProducerProductSettingCompositionTab :selected-product="selectedProduct"/>
		</q-tab-panel>

		<q-tab-panel name="images">
			<ProducerProductSettingImagesTab :selected-product="selectedProduct"/>
		</q-tab-panel>
	</q-tab-panels>
</template>

<script>
import { ref } from "vue"
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
			default: () => {}
		}
	},
	setup() {
		const tab = ref("common")

		return {
			tab
		}
	}
}
</script>
