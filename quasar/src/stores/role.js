import { defineStore } from "pinia"
import { api } from "src/boot/axios"

export const useRoleStore = defineStore("role", {
	state: () => ({
		user_roles: [],
		data: {},
		types: {
			producer: 1,
			advertiser: 2,
			guarantor: 3,
		}
	}),

	actions: {
		setUserRole(role) {
			if (this.user_roles.find((r) => r.id === role.id))
				return

			this.user_roles = [...this.user_roles, role]
		},

		setUserRoles(roles) {
			this.user_roles = roles
		},

		async getList() {
			const response = await api.get("roles")

			this.data = response.data
		}
	}
})
