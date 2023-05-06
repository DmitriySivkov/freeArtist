<template>
	<div class="column full-height">
		<div class="col">
			<q-card
				square
				class="bg-primary"
			>
				<template
					v-for="(product, index) in products"
					:key="index"
				>
					<q-card-section
						horizontal
						class="justify-between"
						:class="{'no-pointer-events': product.id === loadingProduct || !!product.tmp_uuid}"
					>
						<div class="col-xs-9 col-md-11 cursor-pointer q-hoverable">
							<span class="q-focus-helper"></span>
							<div class="row full-height items-center">
								<div class="col-xs-3 col-md-1 text-center">
									<q-icon
										size="1.7em"
										color="white"
										name="edit"
									/>
								</div>
								<div class="col text-white">
									{{ product.title }}
								</div>
							</div>
						</div>
						<div class="col-grow">
							<q-btn
								flat
								square
								icon="more_vert"
								text-color="white"
								class="full-width q-py-lg"
							>
								<q-menu fit>
									<q-list>
										<q-item
											clickable
											v-close-popup
											:disable="!isAbleToManageProduct"
											@click="showDeleteDialog(product)"
										>
											<q-item-section class="text-center">
												Удалить
											</q-item-section>
										</q-item>
									</q-list>
								</q-menu>
							</q-btn>
						</div>
						<q-inner-loading :showing="product.id === loadingProduct || !!product.tmp_uuid">
							<q-spinner-gears
								size="42px"
								color="primary"
							/>
						</q-inner-loading>
					</q-card-section>
					<q-separator dark />
				</template>
			</q-card>
		</div>
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
	},
	emits:[
		"deleteProduct",
		"updateProduct",
	],
	setup(props, { emit }) {
		const selectProduct = (product_id) => {
			if (!product_id) {
				emit("update:modelValue", null)
				return
			}

			emit("update:modelValue", props.products.find((p) => p.id === product_id))
		}

		const update = () => {
			emit("updateProduct")
		}

		const showDeleteDialog = (product) => {
			Dialog.create({
				title: "Подтверждение",
				message: "Удалить: " + product.title + " ?",
				cancel: true,
			}).onOk(() => {
				emit("deleteProduct", product)
			})
		}

		return {
			selectProduct,
			update,
			showDeleteDialog
		}
	}
}
</script>
