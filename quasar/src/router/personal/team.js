import Permissions from "src/pages/personal/team/Permissions.vue"
import PermissionsDetail from "src/pages/personal/team/PermissionsDetail.vue"
import Requests from "src/pages/personal/team/Requests.vue"
import RequestsDetail from "src/pages/personal/team/RequestsDetail.vue"

export default [
	{
		name: "personal_team_permissions",
		path: "personal/team/permissions",
		component: Permissions,
		meta: {
			route_name: "Разрешения"
		}
	},
	{
		name: "personal_team_permissions_detail",
		path: "personal/team/:team_id/permissions",
		component: PermissionsDetail,
		meta: {
			route_name: "Разрешения"
		}
	},
	{
		name: "personal_team_requests",
		path: "personal/team/requests",
		component: Requests,
		meta: {
			route_name: "Заявки"
		}
	},
	{
		name: "personal_team_requests_detail",
		path: "personal/team/:team_id/requests",
		component: RequestsDetail,
		meta: {
			route_name: "Заявки"
		}
	},
]
