<template>
	<q-list
		separator
		dark
	>
		<q-item
			v-for="(product, index) in products"
			:key="index"
			clickable
			class="q-py-lg q-px-md bg-primary text-white"
			@click="productSelected(product.id)"
		>
			<q-item-section
				v-if="selected_product === product.id"
				side
				top
			>
				<q-radio
					color="white"
					:val="product.id"
					:model-value="selected_product"
				/>
			</q-item-section>
			<q-item-section avatar>
				<q-icon name="edit" />
			</q-item-section>
			<q-item-section>
				{{ product.title }}
			</q-item-section>
			<q-item-section side>
				<q-btn
					icon="pause"
					color="warning"
				/>
			</q-item-section>
		</q-item>
	</q-list>
</template>

<script>
import { ref } from "vue"
export default {
	props: {
		products: {
			type: Array,
			default: () => []
		}
	},
	emits:["productChanged"],
	setup(props, { emit }) {
		const selected_product = ref(false)
		const productSelected = (product_id) => {
			selected_product.value = product_id
			emit("update:modelValue", product_id)
		}
		return {
			productSelected,
			selected_product
		}
	}
}
</script>
