<template>
	<q-btn
		label="Выйти"
		color="primary"
		class="q-pa-lg full-width"
		@click="logout"
	/>
</template>

<script>
import { useUserStore } from "src/stores/user"
import { useNotification } from "src/composables/notification"
import { computed } from "vue"
export default {
	setup() {
		const user_store = useUserStore()
		const user = computed(() => user_store.$state)
		const { notifySuccess } = useNotification()
		const logout = () => {
			user_store.logout().then(() =>
				notifySuccess("До свидания!")
			)
		}
		return {
			user,
			logout
		}
	}
}
</script>
