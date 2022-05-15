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
									<q-item-label caption>{{ product.amount}}</q-item-label>
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
export default {
	setup() {
		const $store = useStore()
		const cart = computed(() => Object.values($store.state.cart.data))
		// const columns = [
		// 	{ name: "title", align: "center", label: "Название", field: "title", sortable: true },
		// ]
		return {
			cart
		}
	}
}
</script>
