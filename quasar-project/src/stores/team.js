import { defineStore } from "pinia"
import { LocalStorage } from "quasar"
import { useNotification } from "src/composables/notification"

export const useTeamStore = defineStore("team", {
	state: () => ({
    user_teams: []
	}),

	actions: {
    setUserTeams(team) {
      if (this.user_teams.length > 0)
        this.user_teams = [...this.user_teams, team]
      else
        this.user_teams = Array.isArray(team) ? team : [team]
    }
	}
})
