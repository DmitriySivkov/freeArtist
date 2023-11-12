import { defineStore } from "pinia"
import { api } from "src/boot/axios"
import { useTeamStore } from "src/stores/team"
import { LocalStorage } from "quasar"
import { useRoleStore } from "src/stores/role"

export const useUserStore = defineStore("user", {
	state: () => ({
		is_logged: false,
		personal_tab: "user", // todo - make const file for personal tab names
		location: null,
		location_range: null,
		data: {}
	}),

	actions: {
		async registerProducer(payload) {
			const teamStore = useTeamStore()
			const roleStore = useRoleStore()

			const response = await api.post("personal/producers/register", { ...payload })

			teamStore.setUserTeams(response.data.team)

			roleStore.setUserRole(response.data.role)
		},

		switchPersonal(personal_tab) {
			this.personal_tab = personal_tab
			LocalStorage.set("personal_tab", personal_tab)
		},

		setLocation(location) {
			this.location = location

			LocalStorage.set("location", location)
		},

		setLocationRange(range) {
			this.location_range = range
		},

		setIsLogged(isLogged) {
			this.is_logged = isLogged
		},

		setData(data) {
			this.data = data
		}
	}
})
