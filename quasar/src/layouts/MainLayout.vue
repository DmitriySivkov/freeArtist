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
				<!-- todo - впринципе работает, но нужно починить некоторые переходы: пример - с главной на личный кабинет-->
				<transition
					enter-active-class="animated slideInRight"
					leave-active-class="animated slideOutRight"
					appear
					:duration="300"
				>
					<component :is="Component" />
				</transition>
			</router-view>
		</q-page-container>
	</q-layout>
</template>
