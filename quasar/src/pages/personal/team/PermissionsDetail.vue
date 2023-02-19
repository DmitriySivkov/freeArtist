<template>
	<q-scroll-area style="height: 250px;">
		<TeamUserList
			v-model="selected_user"
			:users="team.users"
		/>
	</q-scroll-area>
	<q-separator />
	<q-scroll-area style="height: 400px;">
		<TeamPermissionList
			:user-id="selected_user"
			:team="team"
			:is-able-to-edit-user-permissions="is_able_to_edit_user_permissions"
		/>
	</q-scroll-area>
	<q-btn
		label="Сохранить"
		type="submit"
		color="primary"
		class="q-pa-lg full-width"
		@click="setUserPermissions(team.id, selected_user)"
		v-if="is_able_to_edit_user_permissions"
	/>
</template>

<script>
import TeamUserList from "src/components/teams/TeamUserList.vue"
import TeamPermissionList from "src/components/teams/TeamPermissionList.vue"
import { useRoute } from "vue-router"
import { useUserTeam } from "src/composables/userTeam"
import { Loading } from "quasar"
import { computed, ref } from "vue"
import { useUserPermission } from "src/composables/userPermission"
import { useNotification } from "src/composables/notification"
import { useUserStore } from "src/stores/user"
export default {
	preFetch ({ store, currentRoute, previousRoute, redirect, ssrContext, urlPath, publicPath }) {
		Loading.show({
			spinnerColor: "primary",
		})
		return store.dispatch("team/getUserList", parseInt(currentRoute.params.team_id))
			.then(() => Loading.hide())
	},
	components: { TeamUserList, TeamPermissionList },
	setup() {
		const $route = useRoute()
		const { user_teams, syncTeamUserPermissions } = useUserTeam()
		const { hasPermission } = useUserPermission()
		const { notifySuccess, notifyError } = useNotification()
		const team = computed(() =>
			user_teams.value.find((team) => team.id === parseInt($route.params.team_id))
		)

		const user_store = useUserStore()

		const user = computed(() => user_store.$state)
		const selected_user = ref(
			team.value.users.find((u) => u.id === user.value.data.id).id
		)

		const is_able_to_edit_user_permissions = computed(() =>
			(
				hasPermission(team.value.id,"producer_permissions") ||
				team.value.user_id === user.value.data.id
			) &&
			selected_user.value !== user.value.data.id &&
			selected_user.value !== team.value.user_id
		)

		const setUserPermissions = (team_id, user_id) => {
			let team_user = team.value.users.find((u) => u.id === selected_user.value)
			let permissions = team_user.permissions.map((p) => p.id)

			syncTeamUserPermissions(team_id, user_id, permissions)
				.then(() => {
					notifySuccess(
						"Права пользователя: '" +
						(team_user.name ?? team_user.phone) +
						"' успешно изменены"
					)
				}).catch((error) => {
					notifyError(error.response.data)
				})
		}

		return {
			team,
			selected_user,
			is_able_to_edit_user_permissions,
			setUserPermissions
		}
	}
}
</script>
