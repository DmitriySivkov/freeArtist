import { defineStore } from "pinia"
import { api } from "src/boot/axios"

export const usePermissionStore = defineStore("permission", {
	state: () => ({
		user_permissions: [],
		producer: {},
		advertiser: {},
		guarantor: {}
	}),

	actions: {
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
