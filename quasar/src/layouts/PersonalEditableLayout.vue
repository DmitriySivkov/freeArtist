<script setup>
import MainDrawer from "src/layouts/MainDrawer.vue"
</script>

<template>
	<q-layout view="lHh Lpr lFr">
		<MainDrawer v-if="$q.screen.width >= $q.screen.sizes.md"/>

		<router-view v-slot="{ Component, route }">
			<transition
				enter-active-class="animated slideInLeft"
				leave-active-class="animated slideOutRight"
				appear
			>
				<q-page-container :key="route.name">
					<component :is="Component" />
				</q-page-container>
			</transition>
		</router-view>
	</q-layout>
</template>
