import TeamList from "src/pages/personal/TeamList.vue"

export default [
	{
		name: "personal_teams",
		path: "personal/teams/:entity/:navigationRouteName",
		component: TeamList,
		props: true
	},
]
