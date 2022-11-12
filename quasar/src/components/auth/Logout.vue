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
import { useStore } from "vuex"
import { useNotification } from "src/composables/notification"
import { computed } from "vue"
export default {
	setup() {
		const $router = useRouter()
		const $store = useStore()
		const user = computed(() => $store.state.user)
		const { notifySuccess } = useNotification()
		const logout = () => {
			$store.dispatch("user/logout").then(() => {
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
