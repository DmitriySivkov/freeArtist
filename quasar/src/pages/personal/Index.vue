<template>
	<q-tabs
		v-if="userRoles.length > 0"
		v-model="selectedTab"
		dense
		inline-label
		active-color="white"
		active-bg-color="secondary"
		indicator-color="transparent"
		align="justify"
		class="text-white bg-indigo-10"
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

	<q-separator />

	<q-tab-panels
		v-model="selectedTab"
		animated
	>
		<q-tab-panel
			v-for="(tab, index) in tabs"
			:key="index"
			:name="tab.name"
		>
			<NavigationUser v-if="selectedTab === 'user'" />
			<NavigationProducer v-if="selectedTab === 'producer'" />
		</q-tab-panel>
	</q-tab-panels>
</template>

<script>
import NavigationUser from "src/pages/personal/user/Navigation"
import NavigationProducer from "src/pages/personal/producer/Navigation"
import { useUserRole } from "src/composables/userRole"
import { ref } from "vue"
import { useStore } from "vuex"
export default {
	// todo - change tab appearance logic (check roles against the tabs)
	components: { NavigationUser, NavigationProducer },
	setup() {
		const $store = useStore()
		const { user, userRoles } = useUserRole()
		const selectedTab = ref(user.value.personalTab)
		const tabs = [
			{name: "user", icon: "account_box", label: "Пользователь"},
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
