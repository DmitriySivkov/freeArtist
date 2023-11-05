<template>
	<div class="row flex flex-center fit absolute q-pa-md">
		<div class="col-xs-12 col-sm-8 col-md-6 col-lg-5 col-xl-4">
			<UserRegister />
		</div>
	</div>
</template>

<script>
import UserRegister from "src/components/users/UserRegister.vue"
export default {
	components: { UserRegister }
}
</script>
