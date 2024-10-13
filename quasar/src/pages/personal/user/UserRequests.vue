<script setup>
import { ref, computed, onMounted } from "vue"
import { api } from "src/boot/axios"
import UserOutgoingRequests from "src/components/users/UserOutgoingRequests.vue"
// import UserIncomingRequests from "src/components/users/UserIncomingRequests.vue"
import { useUserStore } from "src/stores/user"

const userStore = useUserStore()

// const tabs = [
// 	{ name: "incoming", icon: "mail_outline", label: "Входящие" },
// 	{ name: "outgoing", icon: "forward_to_inbox", label: "Исходящие" }
// ]

const selectedTab = ref("incoming")

const requests = ref([])

const isMounting = ref(true)

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
			<UserOutgoingRequests
				v-if="!isMounting"
				:requests="outgoingRequests"
				@request-created="addRequest"
				@request-canceled="changeRequestStatus"
			/>
		</div>
	</q-page>
</template>
