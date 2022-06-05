<template>
	<div class="q-pa-md row">
		<div class="col-xs-12 col-md-6 col-lg-3">
			<q-markup-table
				dark
				class="bg-indigo-8"
			>
				<tbody>
					<tr
						v-for="(value, key) in userCommon"
						:key="key"
					>
						<td class="text-left">{{ key }}</td>
						<td class="text-left">{{ value }}</td>
					</tr>
				</tbody>
			</q-markup-table>
		</div>
	</div>
</template>

<script>
import { computed } from "vue"
import { useStore } from "vuex"

export default {
	setup() {
		const $store = useStore()
		const user = computed(() => $store.state.user.data)

		const userCommon = computed(() => ({
			phone: user.value.phone ?? "",
			name: user.value.name ?? "",
			email: user.value.email ?? "",
		}))
		return {
			userCommon
		}
	},
}
</script>
