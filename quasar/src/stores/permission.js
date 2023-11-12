import { defineStore } from "pinia"
import { api } from "src/boot/axios"

// todo - move permissions to const
export const usePermissionStore = defineStore("permission", {
	state: () => ({
		user_permissions: [],
		producer: {},
		advertiser: {},
		guarantor: {}
	}),

	actions: {
		syncUserTeamPermissions({team_id, permissions}) {
			this.user_permissions = [
				...this.user_permissions.filter((p) => p.team_id !== team_id),
				...permissions
			]
		},

		setUserPermissions(permissions) {
			this.user_permissions = permissions
		},

		setProducerPermissions(permissions) {
			this.producer = permissions.producer
		},

		async getList() {
			const response = await api.get("permissions")
			this.setProducerPermissions(response.data)
		}
	}
})
