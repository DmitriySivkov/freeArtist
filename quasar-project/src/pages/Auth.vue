<template>
	<q-page class="column justify-center">
		<Login v-if="!user.is_logged"/>
		<Logout v-if="user.is_logged" />
	</q-page>
</template>

<script>
import Login from "src/components/auth/Login"
import Logout from "src/components/auth/Logout"
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
