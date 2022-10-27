<template>
	<q-layout view="lHh Lpr lFf">
		<Header />
		<q-page-container>
			<router-view />
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
