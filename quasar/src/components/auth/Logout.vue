<template>
	<q-btn
		label="Выйти"
		color="primary"
		class="q-pa-lg full-width"
		@click="logout"
	/>
</template>

<script setup>
import { useUserStore } from "src/stores/user"
import { useNotification } from "src/composables/notification"
import {useRouter} from "vue-router"

const user_store = useUserStore()
const { notifySuccess } = useNotification()
const $router = useRouter()
const logout = () => {
	user_store.logout().then(() =>
	{
		notifySuccess("До свидания!")
		$router.push({"name": "home"})
	}
	)
}

</script>
