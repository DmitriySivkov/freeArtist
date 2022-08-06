<template>
	<q-table
		grid
		:rows="user.data.producers"
		:row-key="row => row.id"
		hide-header
		hide-pagination
	>
		<template v-slot:item="props">
			<q-card
				class="col-xs-12 text-center"
				square
				:class="{'q-mb-xs': user.data.producers.length-1 !== props.rowIndex }"
			>
				<q-card-section>
					<span class="text-h6">{{ props.row.title }}</span>
				</q-card-section>
				<q-separator />
				<q-card-section @click="props.expand = !props.expand">
					<q-btn
						size="md"
						color="primary"
						round
						dense
						:icon="props.expand ? 'expand_less' : 'expand_more'"
					/>
				</q-card-section>
				<q-card-section
					v-show="props.expand"
					class="q-pa-none"
				>
					<OutgoingProducerPartnershipRequests :producer="props.row" />
					<IncomingProducerPartnershipRequests :producer="props.row" />
					<OutgoingProducerCoworkingInvitations :producer="props.row" />
					<IncomingProducerCoworkingRequests :producer="props.row" />
				</q-card-section>
			</q-card>
		</template>
	</q-table>
</template>

<script>
import OutgoingProducerPartnershipRequests from "src/components/requests/producer/OutgoingProducerPartnershipRequests"
import IncomingProducerPartnershipRequests from "src/components/requests/producer/IncomingProducerPartnershipRequests"
import OutgoingProducerCoworkingInvitations from "src/components/requests/producer/OutgoingProducerCoworkingInvitations"
import IncomingProducerCoworkingRequests from "src/components/requests/producer/IncomingProducerCoworkingRequests"
import { useStore } from "vuex"
import { computed } from "vue"

export default {

	components: {
		OutgoingProducerPartnershipRequests,
		IncomingProducerPartnershipRequests,
		OutgoingProducerCoworkingInvitations,
		IncomingProducerCoworkingRequests
	},
	setup() {
		const $store = useStore()
		const user = computed(() => $store.state.user)
		return {
			user
		}
	}
}
</script>
