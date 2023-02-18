<template>
	<q-table
		grid
		:rows="producers"
		:columns="columns"
		row-key="id"
		:rows-per-page-options="[0]"
		:pagination="pagination"
		hide-header
		@request="onRequest"
		class="home__producers-table"
	>
		<template v-slot:item="props">
			<q-card
				class="col-12 bg-primary text-white q-mb-sm"
				:class="{ 'bg-light-green-2': cart.hasOwnProperty(props.row.id) }"
				@click="show(props.row.id)"
			>
				<q-card-section
					horizontal
					class="row"
				>
					<div class="col-12 bg-indigo-10 q-pa-md">
						<span class="text-h6">{{ props.row.display_name }}</span>
						<span v-if="cart.hasOwnProperty(props.row.id)">
							{{ " (" + cart[props.row.id].products.length + ")" }}
						</span>
					</div>
				</q-card-section>
				<q-card-section
					horizontal
					class="row"
				>
					<div
						style="height:250px"
						class="col-3"
					>
						<q-img
							style="height:100%"
							:src="props.row.logo ? backend_server + '/storage/' + props.row.logo : 'no-image.png'"
							fit="contain"
						/>
					</div>
				</q-card-section>
				<!--								<div class="row">-->
				<!--									<q-card-->
				<!--										style=""-->
				<!--										flat-->
				<!--										class="col-xs-12 col-md-4 bg-indigo-10"-->
				<!--										:class="{'q-pa-xs':props.row.logo}"-->
				<!--									>-->
				<!--										<q-img-->
				<!--											:class="{'bg-white':!props.row.logo}"-->
				<!--											:src="props.row.logo ? backend_server + '/storage/' + props.row.logo : 'no-image.png'"-->
				<!--											fit="cover"-->
				<!--										/>-->
				<!--									</q-card>-->
				<!--								</div>-->
			</q-card>
		</template>
		<template v-slot:bottom="pagination">
			<div class="col-xs-12 col-md-7 q-pl-xs q-pr-xs">
				<div class="row q-col-gutter-sm">
					<div class="col-3">
						<q-btn
							class="full-width text-h6"
							icon="keyboard_double_arrow_left"
							color="primary"
							:disable="pagination.isFirstPage"
							@click="pagination.firstPage"
						/>
					</div>
					<div class="col-3">
						<q-btn
							class="full-width text-h6"
							icon="chevron_left"
							color="primary"
							:disable="pagination.isFirstPage"
							@click="pagination.prevPage"
						/>
					</div>
					<div class="col-3">
						<q-btn
							class="full-width text-h6"
							icon="chevron_right"
							color="primary"
							:disable="pagination.isLastPage"
							@click="pagination.nextPage"
						/>
					</div>
					<div class="col-3">
						<q-btn
							class="full-width text-h6"
							icon="keyboard_double_arrow_right"
							color="primary"
							:disable="pagination.isLastPage"
							@click="pagination.lastPage"
						/>
					</div>
				</div>
			</div>
		</template>
	</q-table>
</template>

<script>
import { computed, ref, watch } from "vue"
import { useRouter } from "vue-router"
import { api } from "src/boot/axios"
import { Loading } from "quasar"
import { useCartStore } from "src/stores/cart"
import { useUserStore } from "src/stores/user"
export default ({
	setup() {
		const cart_store = useCartStore()
		const user_store = useUserStore()
		const $router = useRouter()
		const user = computed(() => user_store.$state)
		const backend_server = process.env.BACKEND_SERVER

		const user_city = ref(user.value.location ?
			user.value.location.city.name_ru : null
		)

		const producers = ref([])

		const pagination = ref({ rowsPerPage: 0 })

		const columns = [
			{ name: "display_name", align: "center", label: "Название", field: row => row.display_name, sortable: true },
		]

		const cart = computed(() => cart_store.data)

		const show = (producer_id) => {
			$router.push({name:"producer_detail", params: { producer_id }})
		}

		const loadProducers = async(page) => {
			Loading.show({ spinnerColor: "primary" })

			const response = await api.get("producers", {
				params: {
					page: page ?? pagination.value.page,
					per_page: pagination.value.rowsPerPage,
					location_range: user.value.location_range,
					city: user_city.value,
				}
			})
			pagination.value = {
				...pagination.value,
				page: response.data.current_page,
				rowsNumber: response.data.total
			}
			producers.value = response.data.data

			Loading.hide()
		}

		watch(() => user.value.location_range, () => loadProducers())

		loadProducers()

		const onRequest = async(props) => {
			await loadProducers(props.pagination.page)
		}

		return {
			producers,
			columns,
			cart,
			show,
			onRequest,
			pagination,
			backend_server
		}
	}
})
</script>
