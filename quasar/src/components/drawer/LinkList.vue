<template>
	<q-list>
		<q-item-label header>
			Навигация
		</q-item-label>

		<Link
			v-for="link in linkList"
			:key="link.title"
			v-bind="link"
		/>
	</q-list>
</template>

<script>
import { useStore } from "vuex"
import { computed } from "vue"
import Link from "src/components/drawer/Link"

export default {
	components: { Link },
	setup() {
		const $store = useStore()
		const user = computed(() => $store.state.user)

		const linkList = computed(() =>
			Object.keys(user.value.data).length > 0 ?
				user.value.linkList.auth :
				user.value.linkList.unauth
		)
		return {
			linkList
		}
	},
}
</script>
