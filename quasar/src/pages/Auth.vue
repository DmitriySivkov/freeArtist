<template>
	<Login v-if="!user.is_logged"/>
	<Logout v-if="user.is_logged" />
</template>

<script>
import Login from "src/components/auth/Login.vue"
import Logout from "src/components/auth/Logout.vue"
import { computed } from "vue"
import { useUserStore } from "src/stores/user"
export default {
	components: { Login, Logout },
	setup() {
		const user_store = useUserStore()

		const user = computed(() => user_store.$state)

		return {
			user
		}
	}
}
</script>
