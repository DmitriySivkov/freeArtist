<template>
	<q-tabs
		v-if="userRoles.length > 0"
		v-model="selected_tab"
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
		v-model="selected_tab"
		animated
	>
		<q-tab-panel
			v-for="(tab, index) in tabs"
			:key="index"
			:name="tab.name"
		>
			<NavigationUser v-if="selected_tab === 'user'" />
			<NavigationProducer v-if="selected_tab === 'producer'" />
		</q-tab-panel>
	</q-tab-panels>
</template>

<script>
import NavigationUser from "src/pages/personal/user/Navigation.vue"
import NavigationProducer from "src/pages/personal/producer/Navigation.vue"
import { useUserRole } from "src/composables/userRole"
import { ref } from "vue"
import { useUserStore } from "src/stores/user"

export default {
	components: { NavigationUser, NavigationProducer },
	setup() {
		const user_store = useUserStore()
		const { user, userRoles } = useUserRole()
		const selected_tab = ref(user.value.personal_tab)
		const tabs = [
			{ name: "user", icon: "account_box", label: "Пользователь" },
			{ name: "producer", icon: "work_outline", label: "Изготовитель" }
		]

		const switchPersonal = (personal_tab) =>
			user_store.switchPersonal(personal_tab)

		return {
			user,
			tabs,
			selected_tab,
			userRoles,
			switchPersonal
		}
	}
}
</script>
