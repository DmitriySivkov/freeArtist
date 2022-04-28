<template>
	<q-list>
		<NavigationItem
			v-for="link in linkList"
			:key="link.title"
			v-bind="link"
		/>
	</q-list>
</template>

<script>
import { useStore } from "vuex"
import { computed } from "vue"
import NavigationItem from "src/components/drawer/NavigationItem"

export default {
	components: { NavigationItem },
	setup() {
		const $store = useStore()
		const user = computed(() => $store.state.user)

		const linkList = computed(() =>
			user.value.isLogged === true ?
				user.value.linkList.auth :
				user.value.linkList.unauth
		)
		return {
			linkList
		}
	},
}
</script>
