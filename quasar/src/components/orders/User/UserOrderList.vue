<script setup>
import { ref, onMounted } from "vue"
import { api } from "src/boot/axios"
import { ORDER_STATUS_NAMES } from "src/const/orderStatuses"
import { TRANSACTION_STATUS_NAMES } from "src/const/transactionStatuses"
import { useStorage } from "src/composables/storage"

const backendServer = process.env.BACKEND_SERVER

const orders = ref([])
const storage = useStorage()

const isMounting = ref(true)

onMounted(async() => {
	let unauthTransactionUuids = []

	if (await storage.has("orders")) {
		unauthTransactionUuids = await storage.get("orders")
	}

	// todo - fetch orders by specific amount (10-20 at once?)
	const promise = api.get("orders", {
		params: { unauthTransactionUuids }
	})

	promise.then((response) => {
		orders.value = response.data
	})

	promise.finally(() => isMounting.value = false)
})
</script>

<template>
	<q-linear-progress
		v-if="isMounting"
		indeterminate
		color="grey-4"
	/>
	<q-card
		v-for="order in orders"
		:key="order.id"
		class="q-mb-sm"
	>
		<q-img
			v-for="(product, productIndex) in order.products"
			:key="product.product_id"
			no-spinner
			:ratio="16/9"
			:src="product.thumbnail ?
				backendServer + '/storage/' + product.thumbnail.path :
				(product.images.length > 0 ? backendServer + '/storage/' + product.images[0].path : '/no-image.png')"
			:class="[
				{[$style.rounded__top_left]: productIndex === 0},
				{[$style.rounded__top_right]: productIndex === 0}
			]"
		>
			<div class="absolute-bottom text-center">
				<span class="text-h5">{{ product.title }} ({{ order.order_products[product.id].amount }} шт)</span>
			</div>
		</q-img>
		<div class="q-pa-md text-center bg-secondary text-white">
			<div class="text-body1 text-uppercase">Номер заказа</div>
			<div class="text-body1">{{ order.id }}</div>

			<q-separator class="full-width q-my-sm" />

			<div class="text-body1 text-uppercase">Изготовитель</div>
			<div class="text-body1">{{ order.producer.team.display_name }}</div>

			<q-separator class="full-width q-my-sm" />

			<div class="text-body1 text-uppercase">Создан</div>
			<div class="text-body1">{{ order.created_at }}</div>

			<q-separator class="full-width q-my-sm" />

			<div class="text-body1 text-uppercase">Статус заказа</div>
			<div class="text-body1">{{ ORDER_STATUS_NAMES[order.status] }}</div>

			<q-separator class="full-width q-my-sm" />

			<div class="text-body1 text-uppercase">Статус платежа</div>
			<div class="text-body1">{{ TRANSACTION_STATUS_NAMES[order.transaction.status] }}</div>
		</div>
	</q-card>
</template>

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
