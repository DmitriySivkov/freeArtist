<template>
	<q-table
		grid
		:rows="producers"
		:columns="columns"
		row-key="id"
		v-model:pagination="pagination"
		hide-header
		@request="onRequest"
		class="home__producers-table"
	>
		<template v-slot:item="props">
			<div
				class="q-table__grid-item col-xs-12 col-md-6"
				style="min-height:300px"
			>
				<q-card
					class="full-height bg-primary text-white q-pa-xs"
					:class="{ 'bg-light-green-2': cart.hasOwnProperty(props.row.id) }"
					@click="show(props.row.id)"
				>
					<div class="row q-mb-xs">
						<q-card
							flat
							class="col-12 bg-indigo-10 q-pa-sm"
						>
							<span class="text-h6">{{ props.row.display_name }}</span>
							<span v-if="cart.hasOwnProperty(props.row.id)">
								{{ " (" + cart[props.row.id].products.length + ")" }}
							</span>
						</q-card>
					</div>
					<div class="row">
						<q-card
							flat
							class="col-xs-12 col-md-4 bg-indigo-10"
							:class="{'q-pa-xs':props.row.logo}"
						>
							<q-img
								:class="{'bg-white':!props.row.logo}"
								:src="props.row.logo ? backend_server + '/storage/' + props.row.logo : 'no-image.png'"
								fit="contain"
							/>
						</q-card>
					</div>
				</q-card>
			</div>
		</template>
		<template
			v-slot:bottom="pagination"
			class="q-pl-xs q-pr-xs"
		>
			<div class="col-xs-12 col-md-7">
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
import { useStore } from "vuex"
import { computed, ref, watch } from "vue"
import { useRouter } from "vue-router"
import { api } from "src/boot/axios"
import { Loading } from "quasar"
export default ({
	async setup() {
		const $store = useStore()
		const $router = useRouter()
		const user = computed(() => $store.state.user)
		const backend_server = process.env.BACKEND_SERVER

		const user_city = ref(user.value.location ?
			user.value.location.city.name_ru : null
		)

		const producers = ref([])
		const pagination = ref({
			page: 1,
			rowsPerPage: 4
		})

		const columns = [
			{ name: "display_name", align: "center", label: "Название", field: row => row.display_name, sortable: true },
		]

		const cart = computed(() => $store.state.cart.data)

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

		await loadProducers()

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
