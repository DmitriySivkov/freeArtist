<template>
	<div class="q-pa-md row">
		<div class="col-xs-12 col-md-6 col-lg-3">
			<q-markup-table
				dark
				class="bg-indigo-8 q-mb-sm"
			>
				<tbody>
					<tr
						v-for="[key, value] in userCommon"
						:key="key"
					>
						<td class="text-left">{{ key }}</td>
						<td class="text-left">{{ value }}</td>
					</tr>
				</tbody>
			</q-markup-table>

			<q-markup-table
				dark
				class="bg-indigo-8"
				separator="vertical"
			>
				<th>Изготовитель</th>
				<th>Привилегии</th>
				<tbody>
					<tr
						v-for="(producer, index) in userProducers"
						:key="index"
					>
						<td class="text-left">{{ producer.title }}</td>
						<td class="text-left">
							<ul>
								<li
									v-for="(rightId, index) in producer.pivot.rights"
									:key="index"
								>
									{{ producerUserRights.find((right) => right.id === rightId).label }}
								</li>
							</ul>
						</td>
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
		const producerUserRights = computed(
			() => $store.state.producer.user_rights
		)
		const userCommon = Object.entries(user.value)
			.filter(([prop]) => ["phone", "name", "email"].includes(prop))

		const userProducers = user.value.producers

		return {
			userCommon,
			userProducers,
			producerUserRights
		}
	},
}
</script>
