import { defineStore } from "pinia"
import { api } from "src/boot/axios"

export const usePermissionStore = defineStore("permission", {
	state: () => ({
		producer: {},
		advertiser: {},
		guarantor: {}
	}),

	actions: {
		setPermissions(permissions) {
			this.producer = permissions.producer
		},

		async getList() {
			const response = await api.get("permissions")
			this.setPermissions(response.data)
		}
	}
})
