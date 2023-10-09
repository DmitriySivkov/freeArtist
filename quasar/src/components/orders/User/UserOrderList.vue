<template>
	<div class="column absolute fit">
		<div class="row full-height justify-center">
			<div
				v-if="!isMounting"
				class="col-xs-12 col-md-9 col-lg-8 q-py-xs"
			>
				<div class="column no-wrap">
					<q-card
						v-for="(order, orderIndex) in orders"
						:key="order.id"
						class="col"
						:class="{'q-mb-sm': orderIndex !== orders.length - 1}"
					>
						<div class="row">
							<div class="col-xs-12 col-sm">
								<div class="column no-wrap full-height">
									<q-img
										v-for="(product, productIndex) in order.products"
										:key="product.product_id"
										no-spinner
										:src="product.thumbnail ?
											backendServer + '/storage/' + product.thumbnail.path :
											(product.images.length > 0 ? backendServer + '/storage/' + product.images[0].path : '/no-image.png')"
										class="col rounded-borders"
										:class="{'q-mb-xs': productIndex !== order.products.length - 1}"
									>
										<div class="absolute-bottom text-center">
											<span class="text-h5">{{ product.title }}</span>
											<span class="text-body1"> ({{ order.order_products[product.id].amount }} шт)</span>
										</div>
									</q-img>
								</div>
							</div>
							<div class="col-xs-12 col-sm-5">
								<q-card class="bg-primary text-white full-height">
									<div class="column full-height q-pa-md text-center relative-position">
										<div class="sticky__common_top q-pt-lg">
											<div class="text-body2 text-uppercase">Номер заказа</div>
											<div class="text-h6">{{ order.id }}</div>
											<q-separator
												color="white"
												class="full-width q-my-sm"
											/>
											<div class="text-body1 text-uppercase">Изготовитель</div>
											<div class="text-h6">{{ order.producer.team.display_name }}</div>
											<q-separator
												color="white"
												class="full-width q-my-sm"
											/>
											<div class="text-body1 text-uppercase">Создан</div>
											<div class="text-h6">{{ order.created_at }}</div>
											<q-separator
												color="white"
												class="full-width q-my-sm"
											/>
											<div class="text-body1 text-uppercase">Обновлен</div>
											<div class="text-h6">{{ order.updated_at }}</div>
										</div>
									</div>
								</q-card>
							</div>
						</div>
					</q-card>
				</div>
			</div>
			<div
				v-else
				class="col-xs-12 col-md-9 q-pt-xs"
			>
				<UserPersonalOrdersSkeleton />
			</div>
		</div>
	</div>
</template>

<script setup>
import { ref, onMounted } from "vue"
import { api } from "src/boot/axios"
import UserPersonalOrdersSkeleton from "src/components/skeletons/UserPersonalOrdersSkeleton.vue"

const backendServer = process.env.BACKEND_SERVER

const orders = ref([])
const isMounting = ref(true)

onMounted(() => {
	const promise = api.get("personal/orders", {
		params: {}
	})

	promise.then((response) => {
		orders.value = response.data
	})

	promise.finally(() => isMounting.value = false)
})
</script>
