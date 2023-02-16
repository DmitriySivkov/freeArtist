import { defineStore } from "pinia"
import * as teamProducerActions from "src/stores/extra/teamProducer"

export const useTeamStore = defineStore("team", {
	state: () => ({
    user_teams: []
	}),

	actions: {
    ...teamProducerActions,

    setUserTeams(team) {
      if (this.user_teams.length > 0)
        this.user_teams = [...this.user_teams, team]
      else
        this.user_teams = Array.isArray(team) ? team : [team]
    },

    emptyUserTeams() {
      this.user_teams = []
    },

	}
})
