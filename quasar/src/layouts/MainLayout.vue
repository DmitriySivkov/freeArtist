<template>
	<q-layout view="lHh Lpr lFf">
		<q-header elevated>
			<q-toolbar class="q-mb-md q-mt-md">
				<q-btn
					flat
					dense
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
			class="bg-grey-2"
		>
			<template v-slot:default>
				<Navigation />
			</template>
		</q-drawer>

		<q-page-container>
			<router-view />
		</q-page-container>
		<q-footer elevated>
			<q-toolbar class="q-mb-md q-mt-md">
				<q-btn
					icon="shopping_cart"
					label="Добавить в корзину"
					stretch
				/>
				<q-separator dark/>
			</q-toolbar>
			<q-toolbar class="q-mb-md q-mt-md">
				<q-btn
					stretch
					label="123"
					icon-right="shopping_cart"
					class="text-right"
				/>
			</q-toolbar>
		</q-footer>
	</q-layout>
</template>

<script>
import Navigation from "src/components/drawer/Navigation"
import { useRoute } from "vue-router"
import { defineComponent, ref } from "vue"

export default defineComponent({
	name: "MainLayout",
	components: { Navigation },
	setup () {
		const route = useRoute()
		const leftDrawerOpen = ref(false)
		const toggleLeftDrawer = () => {
			leftDrawerOpen.value = !leftDrawerOpen.value
		}
		return {
			leftDrawerOpen,
			toggleLeftDrawer,
			route
		}
	}
})
</script>
