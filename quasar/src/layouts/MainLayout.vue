<template>
	<q-layout view="lHh Lpr lFf">
		<Header />
		<q-page-container>
			<div class="row justify-center">
				<div class="col-xs-12 col-lg-8 bg-white main">
					<router-view />
				</div>
			</div>
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
