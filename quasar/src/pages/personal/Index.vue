<template>
	<div>
		<q-tabs
			v-if="user_roles.length > 0"
			v-model="selected_tab"
			dense
			inline-label
			active-color="white"
			active-bg-color="primary"
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
	</div>
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
		const { user_roles } = useUserRole()
		const selected_tab = ref(user_store.personal_tab)
		const tabs = [
			{ name: "user", icon: "account_box", label: "Пользователь" },
			{ name: "producer", icon: "work_outline", label: "Изготовитель" }
		]

		const switchPersonal = (personal_tab) =>
			user_store.switchPersonal(personal_tab)

		return {
			tabs,
			selected_tab,
			user_roles,
			switchPersonal
		}
	}
}
</script>
