<template>
	<div class="q-pa-md row">
		<div class="col-xs-12 col-md-6 col-lg-3">
			<q-markup-table
				dark
				class="bg-indigo-8"
			>
				<tbody>
					<tr
						v-for="(value, key) in {title:producer.title}"
						:key="key"
					>
						<td class="text-left">{{ key }}</td>
						<td class="text-left">{{ value }}</td>
					</tr>
				</tbody>
			</q-markup-table>
		</div>
	</div>
	<div class="q-pa-md row">
		<div class="col-xs-12 col-md-4">
			<q-markup-table
				dark
				class="bg-indigo-8"
			>
				<th
					class="bg-indigo-10 text-body"
					style="white-space: normal"
				>
					Нажмите / наведите на продукт чтобы увидеть состав</th>
				<tbody>
					<tr
						v-for="(product, index) in producer.products"
						:key="index"
					>
						<div class="row items-center">
							<div class="col-xs-12 col-md-7 q-pa-md">
								<div class="text-center cursor-pointer">{{ product.title }}</div>
								<q-tooltip
									anchor="bottom middle"
									class="text-body2"
								>
									<div
										v-for="(description, ingredient) in JSON.parse(product.composition)"
										:key="ingredient"
										class="text-center"
									>
										{{ ingredient }}<span v-if="description">: {{ description }}</span>
									</div>
								</q-tooltip>
								<div class="text-center cursor-pointer">{{ product.price }} р.</div>
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
	</div>
</template>

<script>
import { useStore } from "vuex"
import { computed } from "vue"
import { Loading } from "quasar"
import { useCartManager } from "src/composables/cartManager"

export default {
	async preFetch ({ store, currentRoute, previousRoute, redirect, ssrContext, urlPath, publicPath }) {
		Loading.show({
			spinnerColor: "primary",
		})
		await store.dispatch("producer/getProducer", parseInt(currentRoute.params.id)).then(() =>
			Loading.hide()
		)
	},
	setup() {
		const $store = useStore()
		const producer = computed(() => $store.state.producer.detail)
		const cart = computed(() => $store.state.cart.data)

		const { products, increase, decrease, orderAmountChanged } =
			useCartManager(cart.value.hasOwnProperty(producer.value.id) ?
				producer.value.products.reduce(function(accum, product) {
					let cart_product = cart.value[producer.value.id].products.find((cart_product) => cart_product.data.id === product.id)
					return {...accum, [product.id]: typeof cart_product !== "undefined" ? cart_product.amount : 0}
				}, {}) :
				producer.value.products.reduce(
					(accum, product) => ({...accum, [product.id]:0}), {}
				)
			)

		return {
			producer,
			products,
			increase,
			decrease,
			orderAmountChanged
		}
	}
}
</script>
