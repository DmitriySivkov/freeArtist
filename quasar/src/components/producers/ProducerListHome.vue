<template>
	<q-table
		grid
		:rows="producers"
		:columns="columns"
		row-key="id"
		:pagination="{rowsNumber:10}"
		hide-header
	>
		<template v-slot:item="props">
			<div
				class="q-table__grid-item col-xs-12 col-md-4"
				style="min-height:300px"
			>
				<div
					class="q-table__grid-item-card q-table__card full-height"
					:class="{ 'bg-light-green-2': cart.hasOwnProperty(props.row.id) }"
					@click="show(props.row.id)"
				>
					{{ props.row.team.display_name }}
					<span v-if="cart.hasOwnProperty(props.row.id)">
						{{ "(" + cart[props.row.id].products.length + ")" }}
					</span>
				</div>
			</div>
		</template>
	</q-table>
</template>

<script>
import _ from "lodash"
import { useStore } from "vuex"
import { computed } from "vue"
import { useRouter } from "vue-router"
export default ({
	setup() {
		const $store = useStore()
		const $router = useRouter()
		const producers = computed(() => _.orderBy(
			$store.state.producer.data, producer => producer.team.display_name, "asc"
		))

		const columns = [
			{ name: "display_name", align: "center", label: "Название", field: row => row.team.display_name, sortable: true },
		]

		const cart = computed(() => $store.state.cart.data)

		const show = (producer_id) => {
			$router.push({name:"producer_detail", params: { producer_id }})
		}

		return {
			producers,
			columns,
			cart,
			show
		}
	}
})
</script>
