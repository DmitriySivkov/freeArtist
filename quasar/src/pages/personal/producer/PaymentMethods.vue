<template>
	<ProducerPaymentMethodList
		:is-able-to-manage-payment-methods="isAbleToManagePaymentMethods"
	/>
</template>

<script setup>
import ProducerPaymentMethodList from "src/components/producers/ProducerPaymentMethodList.vue"
import { useRouter } from "vue-router"
import { computed } from "vue"
import { useUserPermission } from "src/composables/userPermission"
import { useUserStore } from "src/stores/user"
import { useTeamStore } from "src/stores/team"

const userStore = useUserStore()
const teamStore = useTeamStore()
const $router = useRouter()

const { hasPermission } = useUserPermission()

const team = computed(() =>
	teamStore.user_teams.find((t) => t.detailed.id === parseInt($router.currentRoute.value.params.producer_id))
)

const isAbleToManagePaymentMethods = computed(() =>
	hasPermission(team.value.id,"producer_payment_methods") ||
			team.value.user_id === userStore.data.id
)

</script>
