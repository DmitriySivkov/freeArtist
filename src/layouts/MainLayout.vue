<template>
	<q-layout view="lHh Lpr lFf">
		<q-header elevated>
			<q-toolbar class="q-mb-md q-mt-md">
				<q-btn
					flat
					dense
					round
					icon="menu"
					aria-label="Menu"
					@click="toggleLeftDrawer"
					size="lg"
				/>

				<q-toolbar-title class="text-h5">
					{{ this.route.meta.route_name || ''}}
				</q-toolbar-title>

			</q-toolbar>
		</q-header>

		<q-drawer
			v-model="leftDrawerOpen"
			show-if-above
			bordered
			elevated
			class="bg-grey-2"
		>
			<q-list>
				<q-item-label
					header
				>
					Навигация
				</q-item-label>

				<EssentialLink
					v-for="link in linkList"
					:key="link.title"
					v-bind="link"
				/>
			</q-list>
		</q-drawer>

		<q-page-container>
			<router-view />
		</q-page-container>
	</q-layout>
</template>

<script>
import EssentialLink from "components/EssentialLink.vue"
import { useRoute } from "vue-router"
import { useStore } from "vuex"
import { computed, defineComponent, ref } from "vue"

export default defineComponent({
	name: "MainLayout",

	components: {
		EssentialLink
	},

	setup () {
		const $store = useStore()
		const route = useRoute()

		const leftDrawerOpen = ref(false)

		const user = computed(() => $store.state.user.data)
		const linkList = computed(() =>
			Object.keys(user.value).length > 0 ?
				$store.state.drawer.linkList.auth :
				$store.state.drawer.linkList.unauth
		)
		/** if API call is requiring params. Then params are appended here */

		return {
			leftDrawerOpen,
			toggleLeftDrawer () {
				leftDrawerOpen.value = !leftDrawerOpen.value
			},
			route,
			linkList,
			user
		}
	}
})
</script>
