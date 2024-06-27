<template>
	<div class="column absolute fit">
		<div class="row justify-center fit">
			<div class="col-xs-12 col-md-8 col-lg-7 col-xl-6 bg-white">
				<OrderCheckout :producerId="Number($route.params.producerId)" />
			</div>
		</div>
	</div>
</template>

<script setup>
import OrderCheckout from "src/components/checkout/OrderCheckout.vue"
</script>
