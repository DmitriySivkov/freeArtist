<script setup>
import MainDrawer from "src/layouts/MainDrawer.vue"
</script>

<template>
	<q-layout view="lHh Lpr lFr">
		<MainDrawer v-if="$q.screen.width >= $q.screen.sizes.md"/>

		<transition
			enter-active-class="animated slideInLeft"
			leave-active-class="animated slideOutRight"
			appear
		>
			<router-view v-slot="{ Component, route }">
				<q-page-container :key="route.name">
					<component :is="Component" />
				</q-page-container>
			</router-view>
		</transition>
	</q-layout>
</template>
