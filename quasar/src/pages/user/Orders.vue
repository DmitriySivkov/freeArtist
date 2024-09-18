<template>
	<q-page class="row justify-center bg-primary">
		<div class="col-xs-12 col-sm-8 col-md-7 col-lg-6 col-xl-5 q-pa-sm">
			<UserOrderList />
		</div>
	</q-page>
</template>

<script setup>
import UserOrderList from "src/components/orders/User/UserOrderList.vue"
</script>
