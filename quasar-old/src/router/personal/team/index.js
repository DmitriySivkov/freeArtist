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
	},
	{
		name: "personal_team_requests",
		path: "personal/team/requests",
		component: () => import("pages/personal/team/Requests"),
		meta: {
			route_name: "Заявки"
		}
	},
	{
		name: "personal_team_requests_detail",
		path: "personal/team/:team_id/requests",
		component: () => import("pages/personal/team/RequestsDetail"),
		meta: {
			route_name: "Заявки"
		}
	},
]
