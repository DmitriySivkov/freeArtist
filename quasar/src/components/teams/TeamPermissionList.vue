<template>
	<q-list>
		<q-item-label
			header
			v-if="isAbleToEditUserPermissions"
		>
			Задайте права
		</q-item-label>
		<q-item
			tag="label"
			v-for="(permission, index) in all_producer_permissions"
			:key="index"
		>
			<q-item-section
				side
				top
			>
				<q-checkbox
					v-model="selected_permissions"
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

<script>
import { computed } from "vue"
import { useUserTeam } from "src/composables/userTeam"
import { useTeamStore } from "src/stores/team"
import { usePermissionStore } from "src/stores/permission"
export default {
	props: {
		userId: Number,
		team: {
			type: Object,
			default: () => ({})
		},
		isAbleToEditUserPermissions: {
			type: Boolean,
			default: true
		}
	},
	setup(props) {
		const permission_store = usePermissionStore()
		const team_store = useTeamStore()
		const { getTeamUser } = useUserTeam()
		const all_producer_permissions = computed(() => permission_store.producer)

		const selected_permissions = computed({
			get: () => {
				return props.userId === props.team.user_id ?
					all_producer_permissions.value.map((p) => p.id) :
					getTeamUser(props.team.id, props.userId).permissions.map((p) => p.id)
			},
			set: (permission_ids) => {
				let permissions = JSON.parse(JSON.stringify(all_producer_permissions.value)) // computed prop deep copy
				team_store.commitTeamUserPermissions({
					team_id: props.team.id,
					user_id: props.userId,
					permissions: permissions.filter((p) => permission_ids.includes(p.id))
				})
			}
		})

		return {
			all_producer_permissions,
			selected_permissions
		}
	}
}
</script>
