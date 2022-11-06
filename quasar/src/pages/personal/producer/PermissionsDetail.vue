<template>
	<q-scroll-area style="height: 250px;">
		<ProducerUserList
			v-model="selected_user"
			:users="team.users"
		/>
	</q-scroll-area>
	<q-separator />
	<q-scroll-area style="height: 400px;">
		<ProducerPermissionList
			:user-id="selected_user"
			:producer="team"
		/>
	</q-scroll-area>
</template>

<script>
import ProducerUserList from "src/components/producers/ProducerUserList"
import ProducerPermissionList from "src/components/producers/ProducerPermissionList"
import { useRoute } from "vue-router"
import { useUserProducer } from "src/composables/userProducer"
import { computed, ref } from "vue"
import { useStore } from "vuex"

export default {
	components: { ProducerUserList, ProducerPermissionList },
	setup() {
		const $route = useRoute()
		const { producerTeams } = useUserProducer()
		const team = computed(() =>
			producerTeams.value.find((team) => team.id === parseInt($route.params.team_id))
		)
		const $store = useStore()
		const user = computed(() => $store.state.user)
		const selected_user = ref(
			team.value.users.find((u) => u.id === user.value.data.id).id
		)

		return {
			team,
			selected_user
		}
	}
}
</script>
