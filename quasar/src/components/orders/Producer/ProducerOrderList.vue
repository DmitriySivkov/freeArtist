<template>
	<div class="column q-gutter-xs">
		<q-card
			v-for="order in orderList"
			:key="order.id"
			class="row col-auto bg-primary text-white"
		>
			<q-card-section class="col-xs-12 col-md-8">
				<q-list>
					<q-item
						v-for="product in order.products"
						:key="product.product_id"
					>
						<q-item-section avatar>
							<q-img
								no-spinner
								:src="product.thumbnail ?
									backendServer + '/storage/' + product.thumbnail.path :
									(product.images.length > 0 ? backendServer + '/storage/' + product.images[0].path : '/no-image.png')"
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
					<q-item>Номер заказа {{ order.id }}</q-item>
					<q-item>создан: {{ order.created_at }}</q-item>
					<q-item>обновлен: {{ order.updated_at }}</q-item>
				</q-list>
			</q-card-section>
		</q-card>
	</div>
</template>

<script setup>
import { ref, watch, onMounted } from "vue"
import { api } from "src/boot/axios"
import { useRouter } from "vue-router"

const props = defineProps({
	date: [String, Object]
})

const $router = useRouter()

const backendServer = process.env.BACKEND_SERVER

const orderList = ref([])

onMounted(() => {
	loadOrders(props.date)
})

const loadOrders = (date) => {
	const promise = api.get(`personal/producers/${$router.currentRoute.value.params.producer_id}/orders`,{
		params: {
			date
		}
	})

	// todo - catch
	promise.then((response) => {
		orderList.value = response.data
	})
}

watch(
	() => props.date,
	(date) => loadOrders(date)
)
</script>
