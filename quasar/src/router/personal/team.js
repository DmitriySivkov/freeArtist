import TeamList from "src/pages/personal/TeamList.vue"
import Permissions from "src/pages/personal/team/Permissions.vue"
import Requests from "src/pages/personal/team/Requests.vue"

export default [
	{
		name: "personal_teams",
		path: "personal/teams/:navigationRouteName",
		component: TeamList,
		props: true
	},
	{
		name: "personal_team_permissions",
		path: "personal/team/:team_id/permissions",
		component: Permissions,
	},
	{
		name: "personal_team_requests",
		path: "personal/team/:team_id/requests",
		component: Requests,
	},
]
