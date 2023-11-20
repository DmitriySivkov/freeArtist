import { defineStore } from "pinia"
import { LocalStorage } from "quasar"

export const useUserStore = defineStore("user", {
	state: () => ({
		is_logged: false,
		personal_tab: "user", // todo - make const file for personal tab names
		location: null,
		location_range: null,
		data: {}
	}),

	actions: {
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
