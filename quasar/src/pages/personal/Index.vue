<template>
	<q-tabs
		v-if="getUserRoles().length > 1"
		v-model="selectedTab"
		dense
		inline-label
		narrow-indicator
		class="text-white bg-indigo-10 q-pa-sm"
	>
		<q-tab
			v-for="(tab, index) in tabs"
			:key="index"
			:name="tab.name"
			:icon="tab.icon"
			:label="tab.label"
			class="q-pa-md"
		/>
	</q-tabs>
	<NavigationCustomer v-if="selectedTab === user.roles.customer"/>
	<NavigationProducer v-if="selectedTab === user.roles.producer"/>
</template>

<script>
import NavigationCustomer from "src/pages/personal/customer/Navigation"
import NavigationProducer from "src/pages/personal/producer/Navigation"
import { useUserRole } from "src/composables/userRole"
import { ref } from "vue"
export default {
	components: { NavigationCustomer, NavigationProducer },
	setup() {
		const { user, getUserRoles } = useUserRole()
		const selectedTab = ref(user.value.roles.customer)
		const tabs = [
			{name: user.value.roles.customer, icon: "account_box", label: "Пользователь"},
			{name: user.value.roles.producer, icon: "work_outline", label: "Производитель"}
		]

		return {
			user,
			tabs,
			selectedTab,
			getUserRoles
		}
	}
}
</script>
