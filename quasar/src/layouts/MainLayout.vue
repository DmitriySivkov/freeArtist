<template>
	<q-layout view="lHh Lpr lFr">
		<q-page-container>
			<q-page class="row no-wrap justify-center">
				<div class="col-xs-12 col-md-8 col-xl-7 bg-white">
					<div class="column full-height">
						<q-scroll-area
							class="col"
							:thumb-style="{ opacity: 0 }"
						>
							<router-view v-slot="{ Component }">
								<!-- todo -->
								<!--								<transition-->
								<!--									enter-active-class="animated slideInRight"-->
								<!--									leave-active-class="animated slideOutRight"-->
								<!--									appear-->
								<!--									:duration="300"-->
								<!--								>-->
								<component :is="Component" />
								<!--								</transition>-->
							</router-view>
						</q-scroll-area>
					</div>
					<MainFooter />
				</div>
			</q-page>
		</q-page-container>

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
