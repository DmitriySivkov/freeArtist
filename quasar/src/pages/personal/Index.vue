<template>
	<q-tabs
		v-if="getUserRoles().length > 1"
		v-model="selectedTab"
		dense
		inline-label
		narrow-indicator
		class="text-white bg-indigo-10 q-pa-sm"
		@update:model-value="switchPersonal"
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
import { useStore } from "vuex"
export default {
	components: { NavigationCustomer, NavigationProducer },
	setup() {
		const $store = useStore()
		const { user, getUserRoles } = useUserRole()
		const selectedTab = ref(user.value.roles[user.value.personalTab])
		const tabs = [
			{name: user.value.roles.customer, icon: "account_box", label: "Пользователь"},
			{name: user.value.roles.producer, icon: "work_outline", label: "Производитель"}
		]

		const switchPersonal = (personalTab) =>
			$store.dispatch(
				"user/switchPersonal",
				Object.keys(user.value.roles).find(
					(roleName) => user.value.roles[roleName] === personalTab
				)
			)

		return {
			user,
			tabs,
			selectedTab,
			getUserRoles,
			switchPersonal
		}
	}
}
</script>
