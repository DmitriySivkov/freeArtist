<template>
	<div class="q-pa-md">
		<q-table
			grid
			:rows="producers"
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
									{{ "(" + cart[props.row.id].products.length + ")" }}
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
import { computed, ref } from "vue"
import { useRouter } from "vue-router"
export default ({
	setup() {
		const $store = useStore()
		const $router = useRouter()
		const producers = computed(() => $store.state.producer.data)
		const columns = [
			{ name: "title", align: "center", label: "Название", field: "title", sortable: true },
		]
		const cart = computed(() => $store.state.cart.data)
		const filter = ref("")

		return {
			producers,
			columns,
			cart,
			filter,
			show(id) {
				$router.push({name:"producer_detail", params: { id }})
			},
		}
	}
})
</script>
