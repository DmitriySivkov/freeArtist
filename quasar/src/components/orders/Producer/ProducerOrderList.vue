<template>
	<div class="column q-gutter-xs">
		<q-card
			v-for="order in orderList"
			:key="order.id"
			class="row col-auto bg-primary text-white text-body1"
		>
			<q-card-section class="col-12 q-py-none">
				<q-item class="q-pa-none">
					<q-item-section
						side
						class="text-white"
					>
						#{{ order.id }}
					</q-item-section>
					<q-item-section class="text-right">
						для {{ order.user }}
					</q-item-section>
				</q-item>
			</q-card-section>
			<q-separator class="full-width" />
			<q-card-section class="col-12">
				<div
					v-for="product in order.products"
					:key="product.product_id"
					class="row q-py-md justify-center"
				>
					<div class="col-xs-12 col-sm-11">
						<div class="row">
							<div class="col">
								{{ product.title }}
							</div>
							<div class="col-shrink text-right">
								{{ order.order_products[product.id] }} шт
							</div>
						</div>
						<q-separator class="full-width" />
					</div>
				</div>
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

const emit = defineEmits([
	"load"
])

const $router = useRouter()

const orderList = ref([])

onMounted(() => {
	loadOrders(props.date)
})

const loadOrders = (date) => {
	emit("load", true)

	const promise = api.get(`personal/producers/${$router.currentRoute.value.params.producer_id}/orders`,{
		params: {
			date
		}
	})

	// todo - catch
	promise.then((response) => {
		orderList.value = response.data
	})

	promise.finally(() => emit("load", false))
}

watch(
	() => props.date,
	(date) => loadOrders(date)
)
</script>
