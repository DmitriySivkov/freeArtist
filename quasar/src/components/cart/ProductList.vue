<template>
	<q-table
		grid
		:rows="cart"
		:row-key="row => row.producer.id"
		hide-header
		hide-pagination
	>
		<template v-slot:item="props">
			<div class="q-pa-xs col-xs-12">
				<q-card>
					<q-card-section>
						{{ props.row.producer.team.display_name }}
					</q-card-section>
					<q-separator />
					<q-card-section>
						<q-list
							v-for="(product, index) in props.row.product_list"
							:key="index"
						>
							<q-item>
								<q-item-section>
									<q-item-label>{{ product.data.title }}</q-item-label>
								</q-item-section>
							</q-item>
							<q-item>
								<q-item-section class="col-xs-12 col-md-3">
									<q-input
										v-model.number="products[product.data.id]"
										@change="orderAmountChanged(props.row.producer, product.data.id, $event)"
										type="number"
										:input-class="products[product.data.id] > 0 ? 'text-center text-white' : 'text-center'"
										:bg-color="products[product.data.id] > 0 ? 'teal' : 'white'"
										standout="bg-teal text-white"
										dense
										class="q-ma-md"
									>
										<template v-slot:before>
											<q-btn
												icon="remove"
												size="md"
												color="primary"
												@click="decrease(props.row.producer, product.data.id)"
												class="full-height"
											/>
										</template>
										<template v-slot:after>
											<q-btn
												icon="add"
												size="md"
												color="primary"
												@click="increase(props.row.producer, product.data.id)"
												class="full-height"
											/>
										</template>
									</q-input>
								</q-item-section>
							</q-item>
						</q-list>
					</q-card-section>
				</q-card>
			</div>
		</template>
		<template v-slot:no-data>
			Корзина пуста
		</template>
	</q-table>
</template>

<script setup>
import { computed } from "vue"
import { useCart } from "src/composables/cart"
import { useCartStore } from "src/stores/cart"

const cart_store = useCartStore()
const cart = computed(() => Object.values(cart_store.data))
const { products, increase, decrease, orderAmountChanged } = useCart(
	cart.value.length > 0
		? cart.value.reduce((accum, cart_item) => ({
			...accum,
			...cart_item.product_list.reduce((ac, product) => ({
				...ac, [product.data.id]:product.amount
			}), {})
		}), {})
		: {})

</script>
