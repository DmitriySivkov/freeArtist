<template>
	<q-list
		separator
		dark
	>
		<template
			v-for="(product, index) in products"
			:key="index"
		>
			<q-item
				v-if="!selected_product || selected_product === product.id"
				clickable
				class="q-py-lg q-px-md bg-primary text-white"
				@click="productSelected(product.id)"
			>
				<q-item-section side>
					<q-btn
						v-if="isAbleToManageProduct && !!selected_product"
						icon="delete"
						color="negative"
					/>
				</q-item-section>
				<q-item-section avatar>
					<q-icon
						:color="selected_product === product.id ? 'secondary' : 'white'"
						name="edit"
					/>
				</q-item-section>
				<q-item-section>
					{{ product.title }}
				</q-item-section>
				<q-item-section
					v-if="isAbleToManageProduct && !!selected_product"
					class="col-xs-3 col-md-2 col-lg-1 text-right"
				>
					<q-btn
						class="q-pa-md"
						icon="done"
						color="secondary"
					/>
				</q-item-section>
				<q-item-section
					v-if="isAbleToManageProduct && !selected_product"
					class="col-xs-3 col-md-2 col-lg-1 text-right"
				>
					<q-btn
						icon="pause"
						color="warning"
					/>
				</q-item-section>
			</q-item>
		</template>
	</q-list>
</template>

<script>
import { ref } from "vue"
export default {
	props: {
		products: {
			type: Array,
			default: () => []
		},
		isAbleToManageProduct: Boolean,
	},
	emits:["productSelected"],
	setup(props, { emit }) {
		const selected_product = ref(null)

		const productSelected = (product_id) => {
			if (product_id === selected_product.value) {
				selected_product.value = null
				emit("productSelected", null)
				return
			}

			selected_product.value = product_id
			emit("productSelected", product_id)
		}

		return {
			productSelected,
			selected_product
		}
	}
}
</script>
