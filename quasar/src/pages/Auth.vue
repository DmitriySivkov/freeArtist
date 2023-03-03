<template>
	<div class="q-pa-md row justify-center items-center full-height">
		<div class="col-xs-12 col-md-6 col-lg-3">
			<Login v-if="!is_logged"/>
			<Logout v-if="is_logged" />
		</div>
	</div>
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
		const is_logged = computed(() => user_store.is_logged)

		return {
			is_logged
		}
	}
}
</script>
