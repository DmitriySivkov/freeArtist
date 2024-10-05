<script setup>
import { ref, computed, onMounted } from "vue"
import { api } from "src/boot/axios"
import { useUserStore } from "src/stores/user"
import { useRouter } from "vue-router"
import ProducerIncomingRequests from "src/components/producers/ProducerIncomingRequests.vue"

const $router = useRouter()
const userStore = useUserStore()

const team = computed(() =>
	userStore.teams.find((t) =>
		t.detailed_id === parseInt($router.currentRoute.value.params.producer_id)
	)
)

const requests = ref([])

const isMounting = ref(true)

const changeRequestStatus = ({ requestId, status }) => {
	requests.value.find((r) => r.id === requestId).status = status
}

onMounted(() => {
	const promise = api.get(`personal/producers/${$router.currentRoute.value.params.producer_id}/relation-requests`)

	promise.then((response) => {
		requests.value = response.data
	})

	//todo catch

	promise.finally(() => isMounting.value = false)
})
</script>

<template>
	<q-page class="row justify-center">
		<div class="col-xs-12 col-sm-8 col-md-7 col-lg-6 col-xl-5 bg-white q-pa-xs">
			<q-linear-progress
				v-if="isMounting"
				indeterminate
				color="primary"
			/>
			<ProducerIncomingRequests
				v-if="!isMounting"
				:requests="requests"
				:producer-id="Number($router.currentRoute.value.params.producer_id)"
				@request-accepted="changeRequestStatus"
				@request-rejected="changeRequestStatus"
			/>
		</div>
	</q-page>
</template>
