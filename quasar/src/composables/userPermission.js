import { computed } from "vue"
import { useStore } from "vuex"

export const useUserPermission = () => {
	const $store = useStore()
	const user = computed(() => $store.state.user)
	const user_producer = computed(() => $store.state.userProducer)

	const hasPermission = (role, team_id, permission_name) => {
		if (role === "producer") {
			return user_producer.value.producers.find((p) => p.id === team_id)
				.users.find((u) => u.id === user.value.data.id)
				.permissions
				.map((p) => p.name)
				.includes(permission_name)
		}
	}

	return { hasPermission }
}
