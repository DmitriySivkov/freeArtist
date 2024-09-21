import userRoutes from "src/router/personal/user"
import producerRoutes from "src/router/personal/producer"
import teamRoutes from "src/router/personal/team"

import Personal from "src/pages/personal/Index.vue"
import PersonalUser from "src/pages/personal/User.vue"

export default [
	...userRoutes,
	...producerRoutes,
	...teamRoutes,
	{
		name: "personal",
		path: "personal",
		component: Personal,
	},
	{
		name: "personal_user",
		path: "personal/user",
		component: PersonalUser,
	}
]
