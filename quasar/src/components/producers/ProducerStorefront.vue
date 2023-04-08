<template>
	<div class="q-pa-md row">
		<div class="col-xs-12 col-md-6 col-lg-3">
			<q-markup-table
				dark
				class="bg-indigo-8"
			>
				<tbody>
					<tr>
						<td class="text-left">{{ producer.team.display_name }}</td>
					</tr>
				</tbody>
			</q-markup-table>
		</div>
	</div>
	<div class="q-pa-md row q-col-gutter-sm">
		<div class="col-xs-12 col-md-6">
			<q-markup-table
				dark
				class="bg-indigo-8"
			>
				<th
					class="bg-indigo-10 text-body"
					style="white-space: normal"
				>
					Нажмите на продукт чтобы увидеть состав
				</th>
				<tbody>
					<tr
						v-for="(product, index) in producer.products"
						:key="index"
					>
						<div class="row items-center">
							<div
								class="col-xs-12 col-md-7 q-pa-md cursor-pointer"
								@click="toggleComposition({
									product_id: product.id,
									composition: product.composition
								})"
							>
								<div class="text-center">{{ product.title }}</div>
								<div class="text-center">{{ product.price }} р.</div>
							</div>
							<div class="col-xs-12 col-md-5">
								<q-input
									v-model.number="products[product.id]"
									@change="orderAmountChanged(producer, product.id, $event)"
									type="number"
									:input-class="products[product.id] > 0 ? 'text-center text-white' : 'text-center'"
									:bg-color="products[product.id] > 0 ? 'teal' : 'white'"
									standout="bg-teal text-white"
									dense
									class="q-ma-md"
								>
									<template v-slot:before>
										<q-btn
											icon="remove"
											size="md"
											color="primary"
											@click="decrease(producer, product.id)"
											class="full-height"
										/>
									</template>
									<template v-slot:after>
										<q-btn
											icon="add"
											size="md"
											color="primary"
											@click="increase(producer, product.id)"
											class="full-height"
										/>
									</template>
								</q-input>
							</div>
						</div>
						<hr v-if="index !== (producer.products.length - 1)"/>
					</tr>
				</tbody>
			</q-markup-table>
		</div>
		<div
			v-if="visible_composition"
			class="col-xs-12 col-md-6"
		>
			<q-card
				class="full-height bg-indigo-8"
				dark
			>
				<q-list>
					<q-item
						v-for="(ingredient, index) in product_composition"
						:key="index"
						class="text-center"
					>
						<q-item-section>
							<q-item-label>{{ ingredient.name }}</q-item-label>
							<span v-if="ingredient.description"> {{ ingredient.description }}</span>
						</q-item-section>
					</q-item>
				</q-list>
			</q-card>
		</div>
	</div>
</template>

<script>
import { computed, ref } from "vue"
import { useCart } from "src/composables/cart"
import { useCartStore } from "src/stores/cart"
import { useProducerStore } from "src/stores/producer"
export default {
	setup() {
		const cart_store = useCartStore()
		const producer_store = useProducerStore()
		const producer = computed(() => producer_store.detail)
		const cart = computed(() => cart_store.data)
		const product_composition = ref({})
		const visible_composition = ref(null)

		const { products, increase, decrease, orderAmountChanged } =
			useCart(cart.value.hasOwnProperty(producer.value.id) ?
				producer.value.products.reduce(function(accum, product) {
					let cart_product = cart.value[producer.value.id].product_list.find((cart_product) => cart_product.data.id === product.id)
					return {...accum, [product.id]: typeof cart_product !== "undefined" ? cart_product.amount : 0}
				}, {}) :
				producer.value.products.reduce(
					(accum, product) => ({...accum, [product.id]:0}), {}
				)
			)

		const toggleComposition = ({product_id, composition}) => {
			if (visible_composition.value === product_id) {
				visible_composition.value = null
				return
			}

			product_composition.value = composition
			visible_composition.value = product_id
		}

		return {
			producer,
			products,
			product_composition,
			visible_composition,
			toggleComposition,
			increase,
			decrease,
			orderAmountChanged
		}
	}
}
</script>
