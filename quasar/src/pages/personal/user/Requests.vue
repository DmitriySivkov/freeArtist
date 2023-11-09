<template>
	<div class="column absolute fit no-wrap bg-primary">
		<div class="row full-height justify-center">
			<div class="col-xs-12 col-sm-8 col-md-7 col-lg-6 col-xl-5 bg-white">
				<OutgoingUserRequests />
			</div>
		</div>
	</div>
</template>

<script setup>
import OutgoingUserRequests from "src/components/requests/user/OutgoingUserRequests.vue"
</script>
