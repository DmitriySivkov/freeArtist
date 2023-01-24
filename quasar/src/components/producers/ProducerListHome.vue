<template>
	<q-table
		grid
		:rows="producers.data"
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
					{{ props.row.display_name }}
					<span v-if="cart.hasOwnProperty(props.row.id)">
						{{ "(" + cart[props.row.id].products.length + ")" }}
					</span>
				</div>
			</div>
		</template>
		<template v-slot:bottom="pagination">
			{{ pagination }}
		</template>
	</q-table>
</template>

<script>
import { useStore } from "vuex"
import { computed, ref } from "vue"
import { useRouter } from "vue-router"
import { Loading } from "quasar"
import { api } from "src/boot/axios"
export default ({
	async setup() {
		const $store = useStore()
		const $router = useRouter()

		const producers = ref([])

		const columns = [
			{ name: "display_name", align: "center", label: "Название", field: row => row.display_name, sortable: true },
		]

		const loadProducers = async() => {
			Loading.show({ spinnerColor: "primary" })
			const response = await api.get("producers")
			producers.value = response.data
			Loading.hide()
		}

		await loadProducers()

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
