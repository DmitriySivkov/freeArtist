<template>
	<q-layout view="lHh Lpr lFr">
		<q-page-container>
			<q-page class="row">
				<div
					v-if="$q.screen.width >= $q.screen.sizes.md"
					class="col-md-3 col-lg-2"
				>
					<MenuMain />
				</div>
				<div
					class="col-xs-12 col-md-grow"
					:class="isBackgroundColorPrimary ? 'bg-primary' : 'bg-white'"
				>
					<div class="column full-height">
						<q-scroll-area
							class="col scroll-area__main"
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

					<MainFooter v-if="$q.screen.width < $q.screen.sizes.md" />

				</div>
			</q-page>
		</q-page-container>

	</q-layout>
</template>

<script setup>
import MainFooter from "src/layouts/MainFooter.vue"
import MenuMain from "src/components/menus/MenuMain.vue"
import { useRouter } from "vue-router"
import { computed } from "vue"

const $router = useRouter()

const isBackgroundColorPrimary = computed(() =>
	!$router.currentRoute.value.path.includes("personal") &&
	!["register", "login"].includes($router.currentRoute.value.name)
)

</script>
