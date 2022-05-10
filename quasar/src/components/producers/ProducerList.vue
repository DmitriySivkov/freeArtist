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
			<template v-slot:top-right>
				<q-input
					borderless
					dense
					debounce="300"
					v-model="filter"
					placeholder="Найти"
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
				$router.push("/producers/" + id)
			},
		}
	}
})
</script>
