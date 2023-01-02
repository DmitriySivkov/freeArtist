export default [
	{
		name: "personal_team_permissions",
		path: "personal/team/permissions",
		component: () => import("pages/personal/team/Permissions"),
		meta: {
			route_name: "Разрешения"
		}
	},
	{
		name: "personal_team_permissions_detail",
		path: "personal/team/:team_id/permissions",
		component: () => import("pages/personal/team/PermissionsDetail"),
		meta: {
			route_name: "Разрешения"
		}
	}
]
