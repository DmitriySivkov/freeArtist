<template>
	<q-card
		v-for="item in order_list"
		:key="item.id"
		class="row bg-primary text-white q-ma-md"
	>
		<q-card-section class="col-12">
			Номер заказа {{ item.id }}
		</q-card-section>
		<q-card-section
			class="col-12"
			horizontal
			bordered
		>
			<q-card-section class="col-6">
				<q-card-section
					v-for="product in item.products"
					:key="product.product_id"
				>
					{{ product.title }}
				</q-card-section>
			</q-card-section>
			<!-- todo - format date -->
			<q-card-section class="col-6">
				<div>создан: <span>{{ item.created_at }}</span></div>
				<div>обновлен: <span>{{ item.updated_at }}</span></div>
				<div>исполнитель: <span>{{ item.producer.team.display_name }}</span></div>
			</q-card-section>
		</q-card-section>
	</q-card>
</template>

<script>
import { computed } from "vue"
import { useOrderStore } from "src/stores/order"
export default {
	setup() {
		const order_store = useOrderStore()
		const order_list = computed(() => order_store.data)

		return {
			order_list
		}
	}
}
</script>
