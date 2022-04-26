<template>
	<q-markup-table
		dark
		class="bg-indigo-8"
	>
		<th class="bg-indigo-10 text-body1">Нажмите / наведите на продукт чтобы увидеть состав</th>
		<tbody>
			<tr
				v-for="(product, index) in products"
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
							v-model.number="orderList[product.id]"
							@change="this.orderAmountChanged($event, product.id)"
							type="number"
							input-class="text-center"
							bg-color="white"
							dense
							standout="bg-teal text-white"
							class="q-pa-md"
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
				<hr v-if="index !== (products.length - 1)"/>
			</tr>
		</tbody>
	</q-markup-table>
</template>

<script>
import { reactive } from "vue"
export default {
	props: {
		products: Object
	},
	setup(props) {
		const orderList = reactive(
			props.products.reduce((accum, product) => ({ ...accum, [product.id]: 0 }), {})
		)

		const increase = (product_id) => {
			if (orderList[product_id] === 999) return
			orderList[product_id]++
		}
		const decrease = (product_id) => {
			if (orderList[product_id] === 0) return
			orderList[product_id]--
		}

		const orderAmountChanged = (product_amount, product_id) => {
			if (parseInt(product_amount) >= 999)
				orderList[product_id] = 999
			if (product_amount === "")
				orderList[product_id] = 0
		}

		return {
			orderList,
			increase,
			decrease,
			orderAmountChanged
		}
	}
}
</script>
