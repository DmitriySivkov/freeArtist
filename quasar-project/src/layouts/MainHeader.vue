<template>
	<q-header
		elevated
		class="header"
	>
		<q-toolbar
			style="min-height:90px"
			class="q-pa-md justify-center"
		>
			<div class="col-xs-12 col-lg-8">
				<div
					v-if="route.name === 'home'"
					class="row items-center justify-end"
				>
					<div class="col-xs-12 col-md-4">
						<q-select
							filled
							square
							v-model="selected_option"
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
import { ref } from "vue"
import { api } from "src/boot/axios"
import { useUserStore } from "src/stores/user"
export default {
	setup () {
		const user_store = useUserStore()
		const route = useRoute()

		// const location = ref(
		// 	user.value.location ? user.value.location.city.name_ru : null
		// )

		const options = [
			{ label: "Рядом с вами", value: 1 },
			{ label: "Все", value: 2 }
		]
		const selected_option = ref(options.find((o) => o.value === 1).label)

		const setLocationRange = (range) => {
			user_store.setLocationRange(range.value)
		}

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
			setLocationRange
		}
	}
}
</script>
