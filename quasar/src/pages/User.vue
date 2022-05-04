<template>
	<div class="q-pa-md row">
		<div class="col-xs-12 col-md-6 col-lg-3">
			<q-markup-table
				dark
				class="bg-indigo-8"
			>
				<tbody>
					<tr
						v-for="(value, key) in {name:user.name, phone:user.phone, email:user.email, status:user.role.title}"
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
		return {
			user
		}
	},
}
</script>
