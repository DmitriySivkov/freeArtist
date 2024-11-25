<script setup>
import MainDrawer from "src/layouts/MainDrawer.vue"
import MainFooter from "src/layouts/MainFooter.vue"
</script>

<template>
	<q-layout view="lHh Lpr lFr">
		<MainDrawer v-if="$q.screen.width >= $q.screen.sizes.md"/>
		<MainFooter v-if="$q.screen.width < $q.screen.sizes.md" />

		<router-view v-slot="{ Component, route }">
			<transition
				enter-active-class="animated slideInLeft"
				leave-active-class="animated slideOutRight"
				appear
			>
				<q-page-container
					:key="route.name"
					class="main-layout"
				>
					<component :is="Component" />
				</q-page-container>
			</transition>
		</router-view>
	</q-layout>
</template>

<style>
.main-layout {
	position: absolute;
	width: 100%
}
</style>
