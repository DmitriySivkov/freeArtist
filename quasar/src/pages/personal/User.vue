<template>
	<div class="q-pa-md row">
		<div class="col-xs-12 col-md-6 col-lg-3">
			<q-markup-table
				dark
				class="bg-secondary q-mb-sm"
			>
				<tbody>
					<tr
						v-for="[key, value] in user_common"
						:key="key"
					>
						<td class="text-left">{{ key }}</td>
						<td class="text-left">{{ value }}</td>
					</tr>
				</tbody>
			</q-markup-table>

			<q-markup-table
				v-if="hasUserRole('producer')"
				dark
				class="bg-secondary"
				separator="vertical"
			>
				<th>Изготовитель</th>
				<th>Привилегии</th>
				<tbody>
					<tr
						v-for="(team, index) in user_teams"
						:key="index"
					>
						<td class="text-left">{{ team.display_name }}</td>
						<td class="text-left">
							<ul>
								<li v-if="userOwnTeam && userOwnTeam.id === team.id">
									Владелец
								</li>
								<!--								<li-->
								<!--									v-else-->
								<!--									v-for="(rightId, index) in producer.pivot.rights"-->
								<!--									:key="index"-->
								<!--								>-->
								<!--									{{ producerUserRights.find((right) => right.id === rightId).label }}-->
								<!--								</li>-->
							</ul>
						</td>
					</tr>
				</tbody>
			</q-markup-table>
		</div>
	</div>
</template>

<script>
import { useUserRole } from "src/composables/userRole"
import { useUserTeam } from "src/composables/userTeam"
import { useUserStore } from "src/stores/user"
export default {
	setup() {
		const user_store = useUserStore()
		const { hasUserRole } = useUserRole()
		const { user_teams, userOwnTeam } = useUserTeam()

		const user_common = Object.entries(user_store.data)
			.filter(([prop]) => ["phone", "name", "email"].includes(prop))

		return {
			user_common,
			user_teams,
			userOwnTeam,
			hasUserRole
		}
	},
}
</script>
