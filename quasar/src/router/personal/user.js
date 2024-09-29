import RegisterProducer from "src/pages/personal/RegisterProducer.vue"
import UserRequests from "src/pages/personal/user/UserRequests.vue"

export default [
	{
		name: "personal_register_producer",
		path: "personal/register-producer",
		component: RegisterProducer,
	},
	{
		name: "personal_user_requests",
		path: "personal/user/requests",
		component: UserRequests,
	},
]
