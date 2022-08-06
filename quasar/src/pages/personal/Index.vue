<template>
	<q-tabs
		v-if="userRoles().length > 0"
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
	<NavigationCustomer v-if="selectedTab === 'customer'" />
	<NavigationProducer v-if="selectedTab === 'producer'" />
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
		const { user, userRoles } = useUserRole()
		const selectedTab = ref(user.value.personalTab)
		const tabs = [
			{name: "customer", icon: "account_box", label: "Пользователь"},
			{name: "producer", icon: "work_outline", label: "Изготовитель"}
		]

		const switchPersonal = (personalTab) =>
			$store.dispatch(
				"user/switchPersonal",
				personalTab
			)

		return {
			user,
			tabs,
			selectedTab,
			userRoles,
			switchPersonal
		}
	}
}
</script>
