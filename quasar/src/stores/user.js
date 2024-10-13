import { defineStore } from "pinia"
import { LocalStorage } from "quasar"
import {api} from "boot/axios"

export const useUserStore = defineStore("user", {
	state: () => ({
		is_logged: false,
		personal_tab: "user", // todo - make const file for personal tab names
		location: null,
		location_range: null,
		data: {},
		teams: []
	}),

	persist: {
		pick: ["personal_tab"],
		storage: sessionStorage,
	},

	actions: {
		switchPersonal(personal_tab) {
			this.personal_tab = personal_tab
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
		},

		setTeams(teams) {
			if (!Array.isArray(teams)) {
				teams = [teams]
			}

			this.teams = [...teams, ...this.teams]
		},

		emptyTeams() {
			this.teams = []
		},

		setTeamFields({ teamId, fields, detailedId }) {
			let team = this.teams.find((t) => t.id === teamId)

			if (!team) return

			// if 'detailedId' is not set then we update 'teams' entity
			// otherwise we update an entity morphed to teams

			if (!detailedId) {
				Object.assign(team, fields)
			} else {
				team.detailed = { ...team.detailed, ...fields }
			}
		},
	}
})
