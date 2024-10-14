<script setup>
import MainDrawer from "src/layouts/MainDrawer.vue"
import MainFooter from "src/layouts/MainFooter.vue"
</script>

<template>
	<q-layout view="lHh Lpr lFr">
		<MainDrawer v-if="$q.screen.width >= $q.screen.sizes.md"/>
		<MainFooter v-if="$q.screen.width < $q.screen.sizes.md" />

		<q-page-container>
			<router-view v-slot="{ Component }">
				<transition
					enter-active-class="animated fadeInRight fast"
					leave-active-class="animated fadeOutLeft fast"
					appear
					:duration="120"
					mode="out-in"
				>
					<component :is="Component" />
				</transition>
			</router-view>
		</q-page-container>
	</q-layout>
</template>
