<template>
	<q-tabs
		v-if="userRoles.length > 0"
		:model-value="selectedTab"
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
		:model-value="selectedTab"
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

<script setup>
import NavigationUser from "src/pages/personal/user/Navigation.vue"
import NavigationProducer from "src/pages/personal/producer/Navigation.vue"
import { computed } from "vue"
import { useUserStore } from "src/stores/user"
import { useRoleStore } from "src/stores/role"

const userStore = useUserStore()
const roleStore = useRoleStore()

const userRoles = computed(() => roleStore.user_roles)

const selectedTab = computed(() => userStore.personal_tab)

const tabs = [
	{ name: "user", icon: "account_box", label: "Пользователь" },
	{ name: "producer", icon: "work_outline", label: "Изготовитель" }
]

const switchPersonal = (personalTab) => {
	userStore.switchPersonal(personalTab)
}
</script>
