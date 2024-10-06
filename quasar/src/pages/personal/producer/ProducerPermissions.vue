<script setup>
import { useRouter } from "vue-router"
import { ref, computed, onMounted } from "vue"
import { useUser } from "src/composables/user"
import { useNotification } from "src/composables/notification"
import { useUserStore } from "src/stores/user"
import { api } from "src/boot/axios"
import { PRODUCER_PERMISSION_NAMES } from "src/const/permissions"
import TeamUserList from "src/components/teams/TeamUserList.vue"
import TeamPermissionList from "src/components/teams/TeamPermissionList.vue"
import PersonalProducerPermissionsFooter from "layouts/PersonalProducerPermissionsFooter.vue"

const $router = useRouter()

const { hasPermission, syncTeamUserPermissions } = useUser()
const { notifySuccess, notifyError } = useNotification()

const userStore = useUserStore()

const isLoading = ref(false)
const isMounting = ref(true)

const team = computed(() =>
	userStore.teams.find((t) =>
		t.detailed_id === parseInt($router.currentRoute.value.params.producer_id)
	)
)

const users = ref([])

const selectedUserId = ref(userStore.data.id)

const selectedUser = computed(() =>
	users.value.find((u) => u.id === selectedUserId.value)
)

const isAbleToEditUserPermissions = computed(() =>
	(
		hasPermission(team.value.id,PRODUCER_PERMISSION_NAMES.PERMISSIONS) ||
				team.value.user_id === userStore.data.id
	) &&
	selectedUserId.value !== userStore.data.id &&
	selectedUserId.value !== team.value.user_id
)

const setUserPermissions = ({ userId, permissions }) => {
	users.value.find((u) => u.id === userId).permissions = permissions
}

const syncUserPermissions = () => {
	isLoading.value = true

	const promise = api.post(
		`personal/teams/${team.value.id}/users/${selectedUser.value.id}/permissions`, {
			permissions: selectedUser.value.permissions
		}
	)

	promise.then(() => {
		notifySuccess("Успешно")
	})

	promise.catch(() => {
		notifyError("Что-то пошло не так")
	})

	promise.finally(() => isLoading.value = false)
}

onMounted(() => {
	// todo - fix response on backend
	const promise = api.get(`personal/teams/${team.value.id}/users/permissions`)

	promise.then((response) => {
		users.value = response.data
	})

	promise.finally(() => isMounting.value = false)
})
</script>

<template>
	<q-page class="row justify-center">
		<div class="col-xs-12 col-sm-9 col-md-6 col-lg-5 bg-white">
			<div class="column full-height">
				<q-scroll-area
					class="col"
					visible
				>
					<q-linear-progress
						v-if="isMounting"
						indeterminate
						rounded
						color="primary"
					/>
					<TeamUserList
						v-model="selectedUserId"
						:users="users"
					/>
				</q-scroll-area>
				<q-separator />
				<q-scroll-area
					class="col"
					visible
				>
					<TeamPermissionList
						v-if="selectedUser"
						:user="selectedUser"
						:owner-id="team.user_id"
						:is-able-to-edit-user-permissions="isAbleToEditUserPermissions"
						@change="setUserPermissions"
					/>
				</q-scroll-area>
			</div>
		</div>
	</q-page>

	<PersonalProducerPermissionsFooter
		:is-loading="isLoading || isMounting"
		@confirm="syncUserPermissions"
	/>
</template>
