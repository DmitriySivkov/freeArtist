<template>
	<div class="q-pa-md row justify-center">
		<div class="col-xs-12 col-md-6 col-lg-3">
			<q-btn
				label="Выйти"
				color="primary"
				class="q-pa-lg full-width"
				@click="logout"
			/>
		</div>
	</div>
</template>

<script>
import { useRouter } from "vue-router"
import { useUserStore } from "src/stores/user"
import { useNotification } from "src/composables/notification"
import { computed } from "vue"
export default {
	setup() {
		const $router = useRouter()
		const user_store = useUserStore()
		const user = computed(() => user_store.$state)
		const { notifySuccess } = useNotification()
		const logout = () => {
			user_store.logout().then(() => {
				$router.push({name: "home"})
				notifySuccess("До свидания!")
			})
		}
		return {
			user,
			logout
		}
	}
}
</script>
