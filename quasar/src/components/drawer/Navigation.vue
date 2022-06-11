<template>
	<q-list>
		<NavigationItem
			v-for="link in authLink"
			:key="link.name"
			:link="link"
		/>
	</q-list>
	<q-separator />
	<q-list v-if="user.isLogged">
		<NavigationItem
			v-for="link in authLinkList"
			:key="link.name"
			:link="link"
		/>
	</q-list>
	<q-list v-else>
		<NavigationItem
			v-for="link in unauthLinkList"
			:key="link.name"
			:link="link"
		/>
	</q-list>
</template>

<script>
import { useStore } from "vuex"
import { computed } from "vue"
import { useRouter } from "vue-router"
import NavigationItem from "src/components/drawer/NavigationItem"

export default {
	components: { NavigationItem },
	setup() {
		const $store = useStore()
		const $router = useRouter()
		const user = computed(() => $store.state.user)

		const linkList = $router.options.routes
			.find((routeBatch) => routeBatch.path === "/").children
			.map((route) => ({name: route.name, meta: route.meta}))

		const authLink = computed(() =>
			linkList.filter(
				(route) => user.value.isLogged ?
					route.name === "logout" :
					route.name === "login"
			)
		)

		const unauthLinkList = computed(() =>
			linkList.filter(
				(route) =>
					route.meta.show_in_drawer &&
					(route.name !== "logout" && route.name !== "login") &&
					!route.meta.hasOwnProperty("requires_auth")
			)
		)

		const authLinkList = computed(() =>
			linkList.filter(
				(route) =>
					route.meta.show_in_drawer &&
					(route.name !== "logout" && route.name !== "login") &&
					(!route.meta.hasOwnProperty("requires_auth") || route.meta.requires_auth === true)
			)
		)
		console.log(authLinkList.value)
		return {
			user,
			authLinkList,
			unauthLinkList,
			authLink
		}
	},
}
</script>
