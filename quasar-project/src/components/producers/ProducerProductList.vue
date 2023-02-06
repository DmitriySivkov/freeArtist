<template>
	<q-list>
		<q-item
			tag="label"
			v-for="(product, index) in dynamicProducts"
			:key="index"
		>
			<q-item-section
				side
				top
			>
				<q-radio
					:model-value="modelValue"
					@update:model-value="productChanged"
					:val="product.id"
				/>
			</q-item-section>

			<q-item-section>
				<q-item-label>{{ product.title }}</q-item-label>
			</q-item-section>
		</q-item>
	</q-list>
</template>

<script>
import { computed } from "vue"
export default {
	props: {
		modelValue: Number,
		products: {
			type: Array,
			default: () => []
		}
	},
	setup(props, context) {
		const dynamicProducts = computed(
			() => props.modelValue ?
				props.products.filter((p) => p.id === props.modelValue) :
				props.products
		)

		const productChanged = (product_id) => {
			context.emit("update:modelValue", product_id)
		}
		return {
			productChanged,
			dynamicProducts
		}
	}
}
</script>
