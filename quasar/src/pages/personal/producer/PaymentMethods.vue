<script setup>
import { useRouter } from "vue-router"
import { computed } from "vue"
import { useUser } from "src/composables/user"
import { useUserStore } from "src/stores/user"
import { PRODUCER_PERMISSION_NAMES } from "src/const/permissions"
import ProducerPaymentMethodList from "src/components/producers/ProducerPaymentMethodList.vue"

const userStore = useUserStore()
const $router = useRouter()

const { hasPermission } = useUser()

const team = computed(() =>
	userStore.teams.find((t) =>
		t.detailed_id === parseInt($router.currentRoute.value.params.producer_id)
	)
)

const isAbleToManagePaymentMethods = computed(() =>
	hasPermission(team.value.id, PRODUCER_PERMISSION_NAMES.PAYMENT_METHODS) ||
	team.value.user_id === userStore.data.id
)

</script>

<template>
	<q-page class="row justify-center">
		<div class="col-xs-12 col-sm-9 col-md-6 col-lg-5 bg-white">
			<ProducerPaymentMethodList
				:producer-id="team.detailed_id"
				:is-able-to-manage-payment-methods="isAbleToManagePaymentMethods"
			/>
		</div>
	</q-page>
</template>
