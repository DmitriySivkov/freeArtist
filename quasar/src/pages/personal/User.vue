<script setup>
import { computed } from "vue"
import { useUser } from "src/composables/user"
import { useUserStore } from "src/stores/user"
import { ROLES } from "src/const/roles"

const userStore = useUserStore()
const { hasRole } = useUser()

const userTeams = computed(() => userStore.teams)

const userCommon = computed(() =>
	Object.entries(userStore.data)
		.filter(([prop]) => ["phone", "name", "email"].includes(prop))
)

const userOwnTeam = computed(() =>
	userStore.teams.find((t) => t.user_id === userStore.data.id)
)
</script>

<template>
	<div class="q-pa-md row">
		<div class="col-xs-12 col-md-6 col-lg-3">
			<q-markup-table
				dark
				class="bg-secondary q-mb-sm"
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
				v-if="hasRole(ROLES.PRODUCER)"
				dark
				class="bg-secondary"
				separator="vertical"
			>
				<th>Изготовитель</th>
				<th>Привилегии</th>
				<tbody>
					<tr
						v-for="(team, index) in userTeams"
						:key="index"
					>
						<td class="text-left">{{ team.display_name }}</td>
						<td
							v-if="userOwnTeam"
							class="text-left"
						>
							{{ userOwnTeam.display_name }}
						</td>
					</tr>
				</tbody>
			</q-markup-table>
		</div>
	</div>
</template>
