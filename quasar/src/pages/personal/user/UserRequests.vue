<script setup>
import { ref, computed, onMounted } from "vue"
import { api } from "src/boot/axios"
import UserOutgoingRequests from "src/components/users/UserOutgoingRequests.vue"
import UserIncomingRequests from "src/components/users/UserIncomingRequests.vue"
import { useUserStore } from "src/stores/user"

const userStore = useUserStore()

const tabs = [
	{ name: "incoming", icon: "mail_outline", label: "Входящие" },
	{ name: "outgoing", icon: "forward_to_inbox", label: "Исходящие" }
]

const selectedTab = ref("incoming")

const requests = ref([])

const isMounting = ref(true)

const incomingRequests = computed(() =>
	requests.value.filter((r) =>
		r.to_id === userStore.data.id &&
		r.to_type === "App\\Models\\User"
	)
)

const outgoingRequests = computed(() =>
	requests.value.filter((r) =>
		r.from_id === userStore.data.id &&
		r.from_type === "App\\Models\\User"
	)
)

const addRequest = (request) => {
	requests.value.unshift(request)
}

const changeRequestStatus = ({ requestId, status }) => {
	requests.value.find((r) => r.id === requestId).status = status
}

onMounted(() => {
	const promise = api.get("personal/users/relation-requests")

	promise.then((response) => {
		requests.value = response.data
	})

	//todo catch

	promise.finally(() => isMounting.value = false)
})
</script>

<template>
	<q-page class="row justify-center">
		<div class="col-xs-12 col-sm-8 col-md-7 col-lg-6 col-xl-5 bg-white">
			<q-tabs
				v-model="selectedTab"
				dense
				inline-label
				active-color="white"
				active-bg-color="primary"
				indicator-color="transparent"
				align="justify"
				class="text-white bg-indigo-8"
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

			<q-linear-progress
				v-if="isMounting"
				indeterminate
				color="primary"
			/>

			<q-tab-panels
				:model-value="selectedTab"
				animated
				class="full-width"
			>
				<q-tab-panel
					v-for="(tab, index) in tabs"
					:key="index"
					:name="tab.name"
					class="q-pa-xs"
				>
					<UserIncomingRequests
						v-if="selectedTab === 'incoming' && !isMounting"
						:requests="incomingRequests"
						@request-accepted="changeRequestStatus"
					/>
					<UserOutgoingRequests
						v-if="selectedTab === 'outgoing' && !isMounting"
						:requests="outgoingRequests"
						@request-created="addRequest"
						@request-canceled="changeRequestStatus"
					/>
				</q-tab-panel>
			</q-tab-panels>
		</div>
	</q-page>
</template>
