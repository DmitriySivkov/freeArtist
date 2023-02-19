<template>
	<div v-if="order_list.length > 0">
		<q-virtual-scroll
			style="max-height: 376px;"
			:items="order_list"
			separator
		>
			<template v-slot="{ item, index }">
				<div class="row q-col-gutter-xs">
					<div class="col-xs-12 col-md-6">
						<q-card
							:key="index"
							bordered
							class="bg-indigo-8 text-white"
						>
							<div class="text-h5 text-center q-pa-lg">
								Номер заказа {{ item.id }}
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
					</div>
					<div class="col-xs-12 col-md-6">
						<q-card
							:key="index"
							bordered
							class="bg-indigo-8 text-white q-pa-lg full-height"
						>
							<div>создан: <span>{{ item.created_at_parts.hi }}</span></div>
							<div>обновлен: <span>{{ item.updated_at_parts.hi }} {{ item.updated_at_parts.date }}</span></div>
							<div>заказчик: <span>{{ item.customer.name }}</span></div>
						</q-card>
					</div>
				</div>
				<q-separator
					v-if="index !== (order_list.length - 1)"
					class="bg-indigo-8"
					:class="{'q-mb-sm q-mt-sm': index !== (order_list.length - 1)}"
				/>
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
