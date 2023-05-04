<template>
	<div class="column full-height">
		<q-list
			v-if="modelValue === null"
			class="col"
			separator
			dark
		>
			<template
				v-for="(product, index) in products"
				:key="index"
			>
				<q-item
					clickable
					class="q-py-lg q-px-md bg-primary text-white wrap"
					@click="selectProduct(product.id)"
					:class="{'no-pointer-events': product.id === loadingProduct}"
					v-ripple
				>
					<q-item-section side>
						<q-btn
							v-if="isAbleToManageProduct && !!modelValue"
							icon="delete"
							color="negative"
							@click.stop="showDeleteDialog"
						/>
					</q-item-section>
					<q-item-section avatar>
						<q-icon
							:color="!!modelValue && modelValue.id === product.id ? 'secondary' : 'white'"
							name="edit"
						/>
					</q-item-section>
					<q-item-section style="word-break: break-all;"> <!-- todo word-break only works through inline style -->
						{{ product.title }}
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
		<q-page-sticky
			v-if="isAbleToManageProduct"
			position="bottom-right"
			class="transform-none"
			:offset="[18,18]"
		>
			<q-btn
				:to="{name:'personal_producer_products_detail_create'}"
				round
				size="1.5em"
				icon="add"
				color="secondary"
			/>
		</q-page-sticky>
	</div>
</template>

<script>
import { Dialog } from "quasar"
export default {
	props: {
		products: {
			type: Array,
			default: () => []
		},
		loadingProduct: Number,
		isAbleToManageProduct: Boolean,
		isProductChanged: Boolean,
		modelValue: {
			required: false
		}
	},
	emits:[
		"deleteProduct",
		"updateProduct",
		"createProduct",
		"update:modelValue"
	],
	setup(props, { emit }) {
		const selectProduct = (product_id) => {
			if (!product_id) {
				emit("update:modelValue", null)
				return
			}

			emit("update:modelValue", props.products.find((p) => p.id === product_id))
		}

		const create = () => {
			emit("createProduct")
		}

		const update = () => {
			emit("updateProduct")
		}

		const showDeleteDialog = () => {
			const product = props.products.find((p) => p.id === props.modelValue.id)

			Dialog.create({
				title: "Подтверждение",
				message: "Удалить продукт: " + product.title + " ?",
				cancel: true,
			}).onOk(() => {
				emit("deleteProduct", product)
			})
		}

		return {
			selectProduct,
			create,
			update,
			showDeleteDialog
		}
	}
}
</script>
