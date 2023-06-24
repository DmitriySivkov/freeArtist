<template>
	<ProducerProductList
		:is-able-to-manage-product="is_able_to_manage_product"
	/>
</template>

<script setup>
import ProducerProductList from "src/components/producers/ProducerProductList.vue"
import { useRouter } from "vue-router"
import { computed, defineComponent } from "vue"
import { useUserPermission } from "src/composables/userPermission"
import { useUserStore } from "src/stores/user"
import { useTeamStore } from "src/stores/team"

defineComponent({
	ProducerProductList
})

const user_store = useUserStore()
const team_store = useTeamStore()
const $router = useRouter()

const { hasPermission } = useUserPermission()

const team = computed(() =>
	team_store.user_teams.find((t) => t.detailed.id === parseInt($router.currentRoute.value.params.producer_id))
)

const is_able_to_manage_product = computed(() =>
	hasPermission(team.value.id,"producer_product") ||
			team.value.user_id === user_store.data.id
)

</script>
