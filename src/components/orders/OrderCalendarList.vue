<template>
	<div v-if="orderList.length > 0">
		<q-virtual-scroll
			style="max-height: 376px;"
			:items="orderList"
			separator
		>
			<template v-slot="{ item, index }">
				<q-card
					:key="index"
					bordered
					class="bg-indigo-8 text-white"
					:class="{'q-mb-sm': index !== (orderList.length - 1)}"
				>
					<div class="text-h5 text-center q-pa-lg">
						Заказ от: {{ item.customer.name }}
					</div>
					<q-separator dark />
					<q-card-section
						v-for="product in item.products"
						:key="product.product_id"
						class="text-subtitle1"
					>
						{{ product.title }}
						<q-separator
							dark
							inline
						/>
					</q-card-section>
				</q-card>
			</template>
		</q-virtual-scroll>
	</div>
	<div v-else>
		<q-card
			dark
			bordered
			class="bg-indigo-8"
		>
			<div class="text-h5 text-center q-pa-lg">
				Заказов нет
			</div>
			<q-separator dark />
		</q-card>
	</div>
</template>

<script>
import { computed } from "vue"
import { useStore } from "vuex"

export default {
	setup() {
		const $store = useStore()
		const orderList = computed(() => $store.state.order.data)
		return {
			orderList
		}
	}
}
</script>
