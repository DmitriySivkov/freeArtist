<template>
	<q-layout view="lHh Lpr lFf">
		<MainHeader />
		<q-page-container>
			<q-page class="row justify-center">
				<div class="col-xs-12 col-lg-8 bg-white">
					<router-view />
				</div>
			</q-page>
		</q-page-container>
		<MainFooter />
	</q-layout>
</template>

<script>
import MainFooter from "src/layouts/MainFooter.vue"
import MainHeader from "src/layouts/MainHeader.vue"
export default {
	name: "MainLayout",
	components: { MainHeader, MainFooter },
}
</script>
