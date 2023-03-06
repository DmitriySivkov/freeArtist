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

		<q-tab-panel name="composition">
			<ProducerProductSettingCompositionTab v-model="product" />
		</q-tab-panel>

		<q-tab-panel name="images">
			<ProducerProductSettingImagesTab
				v-model="product"
				:is-able-to-manage-product="isAbleToManageProduct"
			/>
		</q-tab-panel>
	</q-tab-panels>
</template>

<script>
import { ref, watch } from "vue"
import _ from "lodash"
import ProducerProductSettingCommonTab from "src/components/producers/producerProductSettingsTabs/ProducerProductSettingCommonTab.vue"
import ProducerProductSettingCompositionTab from "src/components/producers/producerProductSettingsTabs/ProducerProductSettingCompositionTab.vue"
import ProducerProductSettingImagesTab from "src/components/producers/producerProductSettingsTabs/ProducerProductSettingImagesTab.vue"
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
		isCreatingProduct: {
			type: Boolean,
			default: false
		}
	},
	emits: ["productChanged"],
	setup(props, { emit }) {
		const tab = ref("common")

		// whenever new field is added to default tab -
		// its needed to be added to 'default_product' with matching type
		// in order for 'save' button to work accordingly
		const product = ref(
			!props.isCreatingProduct ?
				props.selectedProduct :
				{
					title: "",
					price: null,
					amount: NaN
				}
		)

		const default_product = _.cloneDeep(product.value)

		watch(
			() => product.value,
			(val) => emit("productChanged", !_.isEqual(val, default_product)),
			{ deep: true }
		)

		return {
			tab,
			product,
		}
	}
}
</script>
