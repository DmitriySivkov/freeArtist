<template>
	<q-markup-table
		dark
		class="bg-indigo-8"
	>
		<tbody>
			<tr
				v-for="product in products"
				:key="product.id"
			>
				<div class="row items-center">
					<div class="col-xs-12 col-md-7 q-pa-md">
						<div class="text-center">{{ product.title }}</div>
						<div
							v-for="(description, ingredient) in product.composition"
							:key="ingredient"
							class="text-center"
						>
							{{ ingredient }}<span v-if="description">: {{ description }}</span>
						</div>
					</div>
					<div class="col-xs-12 col-md-5">
						<q-input
							mask="###"
							v-model.number="amount_to_order[product.id]"
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
	setup() {
		const amount_to_order = reactive({})

		const increase = (product_id) => {
			if (amount_to_order[product_id] === 999) return
			amount_to_order.hasOwnProperty(product_id) ?
				amount_to_order[product_id]++ :
				amount_to_order[product_id] = 1
		}
		const decrease = (product_id) => {
			if (!amount_to_order.hasOwnProperty(product_id)) return
			if (amount_to_order[product_id] === 0) return
			amount_to_order[product_id]--
		}
		return {
			amount_to_order,
			increase,
			decrease
		}
	}
}
</script>
