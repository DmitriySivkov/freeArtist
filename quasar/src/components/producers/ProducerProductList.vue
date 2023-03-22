<template>
	<q-list v-if="isAbleToManageProduct && !selected_product">
		<q-item class="justify-end">
			<q-item-section class="q-pr-none col-xs-3 col-md-2 col-lg-1">
				<q-btn
					icon="add"
					color="secondary"
					@click="addProduct"
				/>
			</q-item-section>
		</q-item>
	</q-list>

	<q-list
		v-if="selected_product === null"
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
				:disable="product.id === loadingProduct"
				v-ripple
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
				<q-item-section style="word-break: break-all;"> <!-- todo word-break only works through inline style -->
					{{ product.title }}
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
	<!-- else: if something is selected - creating or updating -->
	<q-list v-else>
		<q-item
			clickable
			class="q-py-lg q-px-md bg-primary text-white wrap"
			v-ripple
			@click="selectProduct(null)"
		>
			<q-item-section avatar>
				<q-icon
					color="secondary"
					name="edit"
				/>
			</q-item-section>
			<q-item-section style="word-break: break-all;"> <!-- todo word-break only works through inline style -->
				{{ selected_product.title }}
			</q-item-section>
			<q-item-section
				v-if="isAbleToManageProduct"
				class="col-xs-3 col-md-2 col-lg-1 text-right"
			>
				<q-btn
					unelevated
					class="bg-secondary q-pa-md"
					:class="{'composition__button_done_active': !!isProductChanged }"
					icon="done"
					@click.stop="Object.keys(selected_product).length === 0 ? create() : update()"
				/>
			</q-item-section>
		</q-item>
	</q-list>
</template>

<script>
import { ref } from "vue"
import { Dialog } from "quasar"
import _ from "lodash"
export default {
	props: {
		products: {
			type: Array,
			default: () => []
		},
		loadingProduct: Number,
		isAbleToManageProduct: Boolean,
		isProductChanged: Boolean
	},
	emits:[
		"productSelected",
		"deleteProduct",
		"updateProduct",
		"createProduct"
	],
	setup(props, { emit }) {
		const selected_product = ref(null)

		const selectProduct = (product_id) => {
			if (!product_id) {
				selected_product.value = null
				emit("productSelected", null)
				return
			}

			selected_product.value = _.cloneDeep(props.products.find((p) => p.id === product_id))

			emit("productSelected", selected_product.value)
		}

		const addProduct = () => {
			selected_product.value = {}

			emit("productSelected", {})
		}

		const create = () => {
			emit("createProduct")
		}

		const update = () => {
			emit("updateProduct")
		}

		const showDeleteDialog = () => {
			const product = props.products.find((p) => p.id === selected_product.value)

			Dialog.create({
				title: "Подтверждение",
				message: "Удалить продукт: " + product.title + " ?",
				cancel: true,
			}).onOk(() => {
				emit("deleteProduct", product)
				selected_product.value = null
			})
		}

		return {
			selected_product,
			addProduct,
			selectProduct,
			create,
			update,
			showDeleteDialog
		}
	}
}
</script>
