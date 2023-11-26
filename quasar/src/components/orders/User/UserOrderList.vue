<template>
	<div class="column absolute fit no-wrap">
		<div class="row full-height justify-center">
			<div
				v-if="!isMounting"
				class="col-xs-12 col-sm-8 col-md-7 col-lg-6 col-xl-5"
			>
				<div class="column no-wrap">
					<q-card
						v-for="order in orders"
						:key="order.id"
						class="col q-my-xs q-pa-sm"
					>
						<div class="row">
							<div class="col-12">
								<q-img
									v-for="(product, productIndex) in order.products"
									:key="product.product_id"
									no-spinner
									:src="product.thumbnail ?
										backendServer + '/storage/' + product.thumbnail.path :
										(product.images.length > 0 ? backendServer + '/storage/' + product.images[0].path : '/no-image.png')"
									:class="[
										{[$style.rounded__top_left]: productIndex === 0},
										{[$style.rounded__top_right]: productIndex === 0}
									]"
								>
									<div class="absolute-bottom text-center">
										<span class="text-h5">{{ product.title }}</span>
										<span class="text-body1"> ({{ order.order_products[product.id].amount }} шт)</span>
									</div>
								</q-img>
							</div>
							<div class="col-12">
								<q-card
									flat
									square
									class="bg-primary text-white full-height"
									:class="[
										$style.rounded__bottom_left,
										$style.rounded__bottom_right,
									]"
								>
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
											<div class="text-body1 text-uppercase">Статус</div>
											<div class="text-h6">{{ ORDER_STATUS_NAMES[order.status] }}</div>
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
				class="col-xs-12 col-sm-8 col-md-7 col-lg-6 col-xl-5"
			>
				<UserPersonalOrdersSkeleton />
			</div>
		</div>
	</div>
</template>

<script setup>
import { ref, onMounted } from "vue"
import { api } from "src/boot/axios"
import { ORDER_STATUS_NAMES } from "src/const/orderStatuses"
import UserPersonalOrdersSkeleton from "src/components/skeletons/UserPersonalOrdersSkeleton.vue"

const backendServer = process.env.BACKEND_SERVER

const orders = ref([])
const isMounting = ref(true)

onMounted(() => {
	const promise = api.get("orders")

	promise.then((response) => {
		orders.value = response.data
	})

	promise.finally(() => isMounting.value = false)
})
</script>

<style lang="scss" module>
.rounded {
	&__top_left {
		border-top-left-radius: 4px !important;
	}
	&__top_right {
		border-top-right-radius: 4px !important;
	}
	&__bottom_left {
		border-bottom-left-radius: 4px !important;
	}
	&__bottom_right {
		border-bottom-right-radius: 4px !important;
	}
}
</style>
