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
import { useStore } from "vuex"
export default {
	components: { Login, Logout },
	setup() {
		const $store = useStore()
		const user = computed(() => $store.state.user)
		return {
			user
		}
	}
}
</script>
