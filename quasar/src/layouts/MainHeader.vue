<template>
	<q-header
		elevated
		class="header"
	>
		<q-toolbar class="q-pa-sm justify-center">
			<div class="col-xs-12 col-lg-8">
				<div
					v-if="route.name === 'home'"
					class="row items-center justify-end"
				>
					<div class="col-xs-12 col-md-4">
						<q-select
							:disable="is_fetching_producers"
							filled
							square
							:model-value="selected_option"
							:options="options"
							bg-color="white"
							@update:model-value="setLocationRange"
						>
							<!--							<template v-slot:no-option>-->
							<!--								<q-item>-->
							<!--									<q-item-section class="text-grey">-->
							<!--										Город не найден-->
							<!--									</q-item-section>-->
							<!--								</q-item>-->
							<!--							</template>-->
						</q-select>
					</div>
				</div>
				<div
					v-else
					class="row items-center"
				>
					<div class="col-xs-12 col-md-8">
						<q-toolbar-title class="text-h5">
							{{ route.meta.route_name || ''}}
						</q-toolbar-title>
					</div>
				</div>
			</div>
		</q-toolbar>
	</q-header>
</template>

<script>
import { useRoute } from "vue-router"
import { ref, computed } from "vue"
import { api } from "src/boot/axios"
import { useUserStore } from "src/stores/user"
import { useProducerStore } from "src/stores/producer"
export default {
	setup () {
		const user_store = useUserStore()
		const producer_store = useProducerStore()
		const route = useRoute()

		// const location = ref(
		// 	user.value.location ? user.value.location.city.name_ru : null
		// )

		const options = [
			{ label: "Рядом с вами", value: 1 },
			{ label: "Все", value: 2 }
		]

		const selected_option = ref(
			options.find((o) => o.value === 1).label
		)

		const setLocationRange = (range) => {
			selected_option.value = range.label
			user_store.setLocationRange(range.value)
		}

		const is_fetching_producers = computed(() => producer_store.is_fetching)

		// const loadLocation = async (query, update) => {
		// 	if (query.length < 1) return
		//
		// 	const response = await api.get("cities",{
		// 		params: { query }
		// 	})
		//
		// 	update(() => {
		// 		location_options.value = response.data.map((location) => {
		// 			return {
		// 				label: location.city + " (" + location.address + ")",
		// 				value: location.id
		// 			}
		// 		})
		// 	})
		// }

		return {
			route,
			selected_option,
			options,
			setLocationRange,
			is_fetching_producers
		}
	}
}
</script>
