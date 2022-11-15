<template>
	<div class="row">
		<div class="col-xs-12 col-md-3">
			<q-card
				bordered
				class="q-ma-md border-dashed bg-green-3 shadow-0"
				:class="{'text-white bg-green-6 border-white': isDragging, 'border-black': !isDragging}"
				style="height:300px"
			>
				<q-card-section
					class="row flex-center full-height"
				>
					<q-img
						v-if="image"
						:src="backend_server + '/storage/' + image"
						fit="contain"
					/>
					<span v-else>Добавить фото</span>
					<div
						v-if="canManageLogo"
						class="full-height full-width absolute cursor-pointer"
						@dragenter.prevent="isDragging = true"
						@dragleave.prevent="isDragging = false"
						@dragover.prevent
						@drop.prevent="drop"
					></div>
					<q-inner-loading :showing="isLoading">
						<q-spinner-gears
							size="50px"
							color="primary"
						/>
					</q-inner-loading>
				</q-card-section>
			</q-card>
			<q-file
				v-model="image"
				ref="file_picker"
				accept=".jpg, image/*"
				style="display:none"
			/>
		</div>
	</div>
</template>

<script>
import { ref, computed } from "vue"
import { useRouter } from "vue-router"
import { useNotification } from "src/composables/notification"
import { useStore } from "vuex"
import { useUserPermission } from "src/composables/userPermission"
export default {
	setup() {
		const $store = useStore()
		const $router = useRouter()
		const { hasPermission, isUserTeamOwner } = useUserPermission()
		const image = computed(() =>
			$store.state.userProducer.producers
				.find((team) => team.id === parseInt($router.currentRoute.value.params.team_id))
				.detailed.logo
		)
		const isDragging = ref(false)
		const isLoading = ref(false)
		const canManageLogo = hasPermission(
			parseInt($router.currentRoute.value.params.team_id),
			"producer_manage_logo"
		)
		const backend_server = process.env.BACKEND_SERVER
		const { notifySuccess } = useNotification()

		const drop = (e) => {
			isLoading.value = true

			let formData = new FormData()
			formData.append("logo", e.dataTransfer.files[0])

			// todo validate with q-file help
			$store.dispatch("userProducer/setProducerLogo", {
				logo: formData,
				producer_id: parseInt($router.currentRoute.value.params.team_id)
			}).then(() => {
				isLoading.value = false
				isDragging.value = false
				notifySuccess("Изображение успешно загружено")
			})
		}

		return {
			image,
			backend_server,
			isLoading,
			isDragging,
			drop,
			canManageLogo
		}
	}
}
</script>
