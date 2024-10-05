<script setup>
import { computed } from "vue"
import { useUserStore } from "src/stores/user"

const props = defineProps({
	userId: Number,
	team: {
		type: Object,
		default: () => ({})
	},
	isAbleToEditUserPermissions: {
		type: Boolean,
		default: true
	}
})

const permissionStore = usePermissionStore()
const userStore = useUserStore()
const producerPermissions = computed(() => permissionStore.producer)

const getTeamUser = (teamId, userId) =>
	userStore.teams.find((t) => t.id === teamId)
		.users
		.find((user) => user.id === userId)
//todo - rework permissions
const selectedPermissions = computed({
	get: () => {
		return props.userId === props.team.user_id ?
			producerPermissions.value.map((p) => p.id) :
			getTeamUser(props.team.id, props.userId).permissions.map((p) => p.id)
	},
	set: (permission_ids) => {
		let permissions = JSON.parse(JSON.stringify(producerPermissions.value)) // computed prop deep copy
		userStore.commitTeamUserPermissions({
			team_id: props.team.id,
			user_id: props.userId,
			permissions: permissions.filter((p) => permission_ids.includes(p.id))
		})
	}
})
</script>

<template>
	<q-list>
		<q-item
			tag="label"
			v-for="(permission, index) in producerPermissions"
			:key="index"
		>
			<q-item-section
				side
				top
			>
				<q-checkbox
					v-model="selectedPermissions"
					:val="permission.id"
					:disable="!isAbleToEditUserPermissions"
				/>
			</q-item-section>

			<q-item-section>
				<q-item-label>{{ permission.display_name }}</q-item-label>
			</q-item-section>
		</q-item>
	</q-list>
</template>
