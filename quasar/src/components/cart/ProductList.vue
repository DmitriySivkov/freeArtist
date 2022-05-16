<template>
	<div class="q-pa-md">
		<q-table
			grid
			:rows="cart"
			:row-key="row => row.producer.id"
			hide-header
		>
			<template v-slot:item="props">
				<div class="q-pa-xs col-xs-12">
					<q-card>
						<q-card-section>
							{{ props.row.producer.title }}
						</q-card-section>
						<q-separator />
						<q-card-section>
							<q-item
								v-for="(product, index) in props.row.products"
								:key="index"
							>
								<q-item-section>
									<q-item-label>{{ product.data.title }}</q-item-label>
								</q-item-section>
								<q-item-section side>
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
						</q-card-section>
					</q-card>
				</div>
			</template>
			<template v-slot:no-data>
				Корзина пуста
			</template>
		</q-table>
	</div>
</template>

<script>
import { useStore } from "vuex"
import { computed } from "vue"
import { useCartManager } from "src/composables/cartManager"
export default {
	setup() {
		const $store = useStore()
		const cart = computed(() => Object.values($store.state.cart.data))
		const { products, increase, decrease, orderAmountChanged } = useCartManager(
			cart.value.length > 0
				? cart.value.reduce((accum, cart_item) => ({
					...accum,
					...cart_item.products.reduce((ac, product) => ({
						...ac, [product.data.id]:product.amount
					}), {})
				}), {})
				: {})
		return {
			cart,
			products,
			increase,
			decrease,
			orderAmountChanged
		}
	}
}
</script>
