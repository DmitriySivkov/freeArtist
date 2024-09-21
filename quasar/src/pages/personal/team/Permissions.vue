<script setup>
import TeamUserList from "src/components/teams/TeamUserList.vue"
import TeamPermissionList from "src/components/teams/TeamPermissionList.vue"
import { useRouter } from "vue-router"
import { useUserTeam } from "src/composables/userTeam"
import { computed, ref, onMounted } from "vue"
import { useUserPermission } from "src/composables/userPermission"
import { useNotification } from "src/composables/notification"
import { useUserStore } from "src/stores/user"
import { useTeamStore } from "src/stores/team"
import { api } from "src/boot/axios"

const $router = useRouter()

const { syncTeamUserPermissions } = useUserTeam()
const { hasPermission } = useUserPermission()
const { notifySuccess, notifyError } = useNotification()

const userStore = useUserStore()
const teamStore = useTeamStore()

const isLoading = ref(true)

const team = computed(() =>
	teamStore.user_teams.find((team) =>
		team.id === parseInt($router.currentRoute.value.params.team_id)
	)
)

const selectedUserId = ref(userStore.data.id)

const isAbleToEditUserPermissions = computed(() =>
	(
		hasPermission(team.value.id,"producer_permissions") ||
				team.value.user_id === userStore.data.id
	) &&
	selectedUserId.value !== userStore.data.id &&
	selectedUserId.value !== team.value.user_id
)

const setUserPermissions = ({ teamId, userId }) => {
	let user = team.value.users.find((u) => u.id === selectedUserId.value)
	let permissions = user.permissions.map((p) => p.id)

	syncTeamUserPermissions(teamId, userId, permissions)
		.then(() => {
			notifySuccess(
				"Права пользователя: '" +
						(user.name ?? user.phone) +
						"' успешно изменены"
			)
		}).catch((error) => {
			notifyError(error.response.data)
		})
}

onMounted(() => {
	const promise = api.get(`personal/teams/${$router.currentRoute.value.params.team_id}/users`)

	promise.then((response) => {
		teamStore.setTeamUsers({
			teamId: parseInt($router.currentRoute.value.params.team_id),
			users: response.data
		})
	})

	promise.finally(() => isLoading.value = false)
})
</script>

<template>
	<q-scroll-area style="height: 50vh;">
		<div class="text-body1 q-pa-sm">Выберите пользователя</div>
		<q-linear-progress
			v-if="isLoading"
			indeterminate
			rounded
			color="primary"
		/>
		<TeamUserList
			v-model="selectedUserId"
			:users="team.users"
		/>
	</q-scroll-area>
	<q-separator />
	<q-scroll-area style="height: 50vh;">
		<TeamPermissionList
			:user-id="selectedUserId"
			:team="team"
			:is-able-to-edit-user-permissions="isAbleToEditUserPermissions"
		/>
	</q-scroll-area>
	<q-btn
		v-if="isAbleToEditUserPermissions"
		label="Сохранить"
		type="submit"
		color="primary"
		class="q-pa-lg full-width"
		@click="setUserPermissions({
			teamId: team.id,
			userId: selectedUserId
		})"
	/>
</template>
