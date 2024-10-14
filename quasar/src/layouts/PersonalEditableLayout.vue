<script setup>
import MainDrawer from "src/layouts/MainDrawer.vue"
</script>

<template>
	<q-layout view="lHh Lpr lFr">
		<MainDrawer v-if="$q.screen.width >= $q.screen.sizes.md"/>

		<transition
			enter-active-class="animated fadeInRight"
			leave-active-class="animated fadeOutLeft"
			appear
			:duration="200"
			mode="out-in"
		>
			<q-page-container>
				<router-view v-slot="{ Component }">
					<component :is="Component" />
				</router-view>
			</q-page-container>
		</transition>
	</q-layout>
</template>
