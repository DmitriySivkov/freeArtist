import { defineStore } from "pinia"

export const useRoleStore = defineStore("role", {
	state: () => ({
		user_roles: []
	}),

	actions: {
		setUserRole(role) {
			if (this.user_roles.find((r) => r.id === role.id))
				return

			this.user_roles = [role, ...this.user_roles]
		},

		setUserRoles(roles) {
			this.user_roles = roles
		},
	}
})
