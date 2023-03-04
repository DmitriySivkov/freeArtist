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
				:disable="product.id === loadingProduct"
			>
				<q-item-section side>
					<q-btn
						v-if="isAbleToManageProduct && !!selected_product"
						icon="delete"
						color="negative"
						@click.stop="showDeleteDialog"
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
						@click.stop="save"
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
				<q-inner-loading :showing="product.id === loadingProduct">
					<q-spinner-gears
						size="42px"
						color="primary"
					/>
				</q-inner-loading>
			</q-item>
		</template>
	</q-list>
</template>

<script>
import { ref } from "vue"
import { Dialog } from "quasar"
export default {
	props: {
		products: {
			type: Array,
			default: () => []
		},
		loadingProduct: Number,
		isAbleToManageProduct: Boolean,
	},
	emits:[
		"productSelected",
		"productDeleted"
	],
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

		const save = () => {
			console.log(123)
		}

		const showDeleteDialog = () => {
			const product = props.products.find((p) => p.id === selected_product.value)

			Dialog.create({
				title: "Подтверждение",
				message: "Удалить продукт: " + product.title + " ?",
				cancel: true,
			}).onOk(() => {
				emit("productDeleted", product)
				selected_product.value = null
			})
		}

		return {
			productSelected,
			selected_product,
			save,
			showDeleteDialog
		}
	}
}
</script>
