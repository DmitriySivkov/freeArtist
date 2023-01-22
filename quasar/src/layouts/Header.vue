<template>
	<q-header
		elevated
		class="header"
	>
		<q-toolbar class="q-pa-md justify-center">
			<div class="col-xs-12 col-lg-8">
				<div class="row items-center justify-end">
					<div class="col-xs-12 col-md-4">
						<q-select
							filled
							square
							v-model="location"
							use-input
							input-debounce="1000"
							label="Город"
							:options="location_options"
							@filter="loadLocation"
							bg-color="white"
						>
							<template v-slot:no-option>
								<q-item>
									<q-item-section class="text-grey">
										Город не найден
									</q-item-section>
								</q-item>
							</template>
						</q-select>
					</div>
					<!--					<div class="col-xs-12 col-md-8">-->
					<!--						<q-toolbar-title class="text-h5">-->
					<!--							{{ route.meta.route_name || ''}}-->
					<!--						</q-toolbar-title>-->
					<!--					</div>-->
				</div>
			</div>
		</q-toolbar>
	</q-header>
</template>

<script>
import { useRoute } from "vue-router"
import { computed, ref } from "vue"
import { api } from "boot/axios"
import { useStore } from "vuex"
export default {
	setup () {
		const $store = useStore()
		const route = useRoute()
		const user = computed(() => $store.state.user)

		const location = ref(
			user.value.location ? user.value.location.city.name_ru : null
		)
		const location_options = ref(null)

		const loadLocation = async (query, update) => {
			if (query.length < 1) return

			const response = await api.get("cities",{
				params: { query }
			})

			update(() => {
				location_options.value = response.data.map((location) => {
					return {
						label: location.city + " (" + location.address + ")",
						value: location.id
					}
				})
			})
		}

		return {
			route,
			location,
			location_options,
			loadLocation
		}
	}
}
</script>
