<template>
	<q-layout
		view="lHh Lpr lFf"
	>
		<MainHeader />
		<q-page-container>

			<q-page class="row no-wrap justify-center">
				<div class="col-xs-12 col-lg-8 bg-white">
					<div class="column full-height">
						<q-scroll-area
							class="col"
							:thumb-style="{ opacity: 0 }"
						>
							<router-view />
						</q-scroll-area>
					</div>
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
