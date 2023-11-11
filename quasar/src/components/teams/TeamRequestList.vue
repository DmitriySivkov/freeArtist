<template>
	<div class="column absolute full-width no-wrap bg-primary">
		<div class="row full-height justify-center">
			<div class="col-xs-12 col-sm-8 col-md-7 col-lg-6 col-xl-5 bg-white">
				<div v-if="team.detailed_type === 'App\\Models\\Producer'">
					<!--					<OutgoingProducerRequests :team="team" />-->
					<IncomingProducerRequests :team="team" />
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import IncomingProducerRequests from "src/components/requests/producer/IncomingProducerRequests.vue"

defineProps({
	team: {
		type: Object,
		default: () => ({})
	}
})
</script>
