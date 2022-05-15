<template>
	<div class="q-pa-md">
		<q-table
			grid
			:rows="cart"
			:columns="columns"
			row-key="id"
			:filter="filter"
			:pagination="{rowsPerPage:10}"
			hide-header
		>
			<template v-slot:item="props">
				<div class="q-table__grid-item col-xs-12 col-sm-6 col-md-4 col-lg-3">
					<div
						:class="{ 'bg-light-green-2': cart.hasOwnProperty(props.row.id) }"
						class="q-table__grid-item-card q-table__card cursor-pointer"
						@click="show(props.row.id)"
					>
						<div class="q-table__grid-item-row">
							<div class="q-table__grid-item-value">
								{{ props.row.title }}
								<span v-if="cart.hasOwnProperty(props.row.id)">
									{{ "(" + Object.keys(cart[props.row.id]).length + ")" }}
								</span>
							</div>
						</div>
					</div>
				</div>
			</template>
			<template v-slot:top-left>
				<q-input
					borderless
					dense
					debounce="300"
					v-model="filter"
					placeholder="Искать..."
				>
					<template v-slot:append>
						<q-icon name="search" />
					</template>
				</q-input>
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
		const cart = computed(() => $store.state.cart.data)
		return {
			cart
		}
	}
}
</script>
