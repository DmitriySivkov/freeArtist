<script setup>
import { useRouter } from "vue-router"
import { computed, ref, onMounted } from "vue"
import { useUser } from "src/composables/user"
import { useNotification } from "src/composables/notification"
import { useUserStore } from "src/stores/user"
import { api } from "src/boot/axios"
import { PRODUCER_PERMISSION_NAMES } from "src/const/permissions"
import TeamUserList from "src/components/teams/TeamUserList.vue"
import TeamPermissionList from "src/components/teams/TeamPermissionList.vue"

const $router = useRouter()

const { hasPermission, syncTeamUserPermissions } = useUser()
const { notifySuccess, notifyError } = useNotification()

const userStore = useUserStore()

const isLoading = ref(true)

const team = computed(() =>
	userStore.teams.find((t) =>
		t.detailed_id === parseInt($router.currentRoute.value.params.producer_id)
	)
)

const selectedUserId = ref(userStore.data.id)

const isAbleToEditUserPermissions = computed(() =>
	(
		hasPermission(team.value.id,PRODUCER_PERMISSION_NAMES.PERMISSIONS) ||
				team.value.user_id === userStore.data.id
	) &&
	selectedUserId.value !== userStore.data.id &&
	selectedUserId.value !== team.value.user_id
)

const setUserPermissions = ({ teamId, userId }) => {
	let user = team.value.users.find((u) => u.id === selectedUserId.value)
	let permissions = user.permissions.map((p) => p.id)

	userStore.syncTeamUserPermissions(teamId, userId, permissions)
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
	const promise = api.get(`personal/teams/${team.value.id}/users`)

	promise.then((response) => {
		// teamStore.setTeamUsers({
		// 	teamId: team.value.id,
		// 	users: response.data
		// })
		// todo - collect users in some ref
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
