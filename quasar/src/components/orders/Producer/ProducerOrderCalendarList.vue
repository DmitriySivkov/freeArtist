<template>
	<div class="column q-gutter-sm">
		<q-card
			v-for="item in order_list"
			:key="item.id"
			class="row col-auto bg-primary text-white"
		>
			<q-card-section class="col-xs-12 col-md-8">
				<q-list>
					<q-item
						v-for="product in item.products"
						:key="product.product_id"
					>
						<q-item-section avatar>
							<q-img
								:src="product.thumbnail ?
									backend_server + '/storage/' + product.thumbnail.path :
									(product.images.length > 0 ? backend_server + '/storage/' + product.images[0].path : '/no-image.png')"
							/>
						</q-item-section>

						<q-item-section>
							<q-item-label>{{ product.title }}</q-item-label>
						</q-item-section>

					</q-item>
				</q-list>
			</q-card-section>
			<q-card-section class="col-xs-12 col-md-4">
				<q-list dense>
					<!-- todo - format date -->
					<q-item>Номер заказа {{ item.id }}</q-item>
					<q-item>создан: {{ item.created_at }}</q-item>
					<q-item>обновлен: {{ item.updated_at }}</q-item>
					<q-item>исполнитель: {{ item.producer.team.display_name }}</q-item>
				</q-list>
			</q-card-section>
		</q-card>
	</div>
</template>

<script>
import { computed } from "vue"
import { useOrderStore } from "src/stores/order"
export default {
	setup() {
		const order_store = useOrderStore()
		const order_list = computed(() => order_store.data)

		const backend_server = process.env.BACKEND_SERVER

		return {
			order_list,
			backend_server
		}
	}
}
</script>
