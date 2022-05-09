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
			<q-table
				dark
				class="bg-indigo-8"
				v-if="orderList.value"
				:loading="isLoading"
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
									v-model.number="orderList.value[product.id]"
									@change="this.orderAmountChanged($event, product.id)"
									type="number"
									:input-class="orderList.value[product.id] > 0 ? 'text-center text-white' : 'text-center'"
									:bg-color="orderList.value[product.id] > 0 ? 'teal' : 'white'"
									standout="bg-teal text-white"
									dense
									class="q-ma-md"
								>
									<template v-slot:before>
										<q-btn
											icon="remove"
											size="md"
											color="primary"
											@click="this.decrease(product.id)"
											class="full-height"
										/>
									</template>
									<template v-slot:after>
										<q-btn
											icon="add"
											size="md"
											color="primary"
											@click="this.increase(product.id)"
											class="full-height"
										/>
									</template>
								</q-input>
							</div>
						</div>
						<hr v-if="index !== (producer.products.length - 1)"/>
					</tr>
				</tbody>
			</q-table>
		</div>
	</div>
</template>

<script>
import { useStore } from "vuex"
import { computed, reactive, ref } from "vue"
import { useRouter } from "vue-router"

export default {
	setup() {
		const $store = useStore()
		const $router = useRouter()
		const orderList = reactive({})
		const producer = computed(() => $store.state.producer.detail)
		const isLoading = ref(true)

		$store.dispatch("producer/getProducer", parseInt($router.currentRoute.value.params.id)).then(() => {
			orderList.value = producer.value.products.reduce(
				(accum, product) => ({ ...accum, [product.id]:
							$store.state.cart.data.hasOwnProperty(producer.value.id) && $store.state.cart.data[producer.value.id].hasOwnProperty(product.id) ?
								$store.state.cart.data[producer.value.id][product.id] :
								0
				}),
				{}
			)
			isLoading.value = false
		})

		const increase = (product_id) => {
			if (orderList.value[product_id] === 999) return
			orderList.value[product_id]++

			orderAmountChanged()
		}
		const decrease = (product_id) => {
			if (orderList.value[product_id] === 0) return
			orderList.value[product_id]--

			orderAmountChanged()
		}

		const orderAmountChanged = (product_amount, product_id) => {
			if (product_amount === "") {
				orderList.value[product_id] = 0
			}

			if (parseInt(product_amount) >= 999)
				orderList.value[product_id] = 999

			let order =  Object.assign({}, orderList.value)

			$store.dispatch("cart/addToCart", {
				producerId: producer.value.id,
				producerProductList: order
			})
		}

		return {
			producer,
			orderList,
			isLoading,
			increase,
			decrease,
			orderAmountChanged
		}
	}
}
</script>
