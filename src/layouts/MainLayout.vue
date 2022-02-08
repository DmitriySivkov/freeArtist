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
			<template v-slot:default>
				<LinkList />
			</template>
		</q-drawer>

		<q-page-container>
			<router-view />
		</q-page-container>
	</q-layout>
</template>

<script>
import LinkList from "src/components/drawer/LinkList"
import { useRoute } from "vue-router"
import { defineComponent, ref } from "vue"

export default defineComponent({
	name: "MainLayout",
	components: { LinkList },
	setup () {
		const route = useRoute()
		const leftDrawerOpen = ref(false)

		return {
			leftDrawerOpen,
			toggleLeftDrawer () {
				leftDrawerOpen.value = !leftDrawerOpen.value
			},
			route
		}
	}
})
</script>
