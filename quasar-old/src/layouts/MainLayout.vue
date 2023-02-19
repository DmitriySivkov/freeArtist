<template>
	<q-layout view="lHh Lpr lFf">
		<Header />
		<q-page-container>
			<q-page class="row justify-center">
				<div class="col-xs-12 col-lg-8 bg-white">
					<router-view />
				</div>
			</q-page>
		</q-page-container>
		<Footer />
	</q-layout>
</template>

<script>
import Footer from "src/layouts/Footer"
import Header from "src/layouts/Header"

export default {
	name: "MainLayout",
	components: { Header, Footer },
}
</script>
