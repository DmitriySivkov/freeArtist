<template>
	<div class="q-pa-md row">
		<div class="col-xs-12 col-md-6 col-lg-3">
			<q-markup-table
				dark
				class="bg-indigo-8"
			>
				<tbody>
					<tr
						v-for="(value, key) in {title:producer.title}"
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
import { useStore } from "vuex"
import { computed } from "vue"
import { useRouter } from "vue-router"
export default ({
	setup() {
		const $store = useStore()
		const $router = useRouter()

		const producer = computed(
			() => $store.state.producer.data.find(
				(producer) => producer.id == $router.currentRoute.value.params.id)
		)

		return {
			producer,
		}
	}
})
</script>
