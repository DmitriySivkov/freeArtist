<template>
	<div class="row">
		<div class="col-xs-12 col-md-3">
			<q-card
				bordered
				class="q-ma-md border-dashed bg-green-3 shadow-0"
				:class="{'text-white bg-green-6 border-white': is_dragging, 'border-black': !is_dragging}"
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
						v-if="can_manage_logo"
						class="full-height full-width absolute cursor-pointer"
						@dragenter.prevent="is_dragging = true"
						@dragleave.prevent="is_dragging = false"
						@dragover.prevent
						@drop.prevent="drop"
					></div>
					<q-inner-loading :showing="is_loading">
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
		const { hasPermission } = useUserPermission()
		const image = computed(() =>
			$store.state.team.user_teams
				.find((team) => team.id === parseInt($router.currentRoute.value.params.team_id))
				.detailed.logo
		)
		const is_dragging = ref(false)
		const is_loading = ref(false)
		const can_manage_logo = hasPermission(
			parseInt($router.currentRoute.value.params.team_id),
			"producer_manage_logo"
		)
		const backend_server = process.env.BACKEND_SERVER
		const { notifySuccess } = useNotification()

		const drop = (e) => {
			is_loading.value = true

			let formData = new FormData()
			formData.append("logo", e.dataTransfer.files[0])

			// todo validate with q-file help
			$store.dispatch("userProducer/setProducerLogo", {
				logo: formData,
				producer_id: parseInt($router.currentRoute.value.params.team_id)
			}).then(() => {
				is_loading.value = false
				is_dragging.value = false
				notifySuccess("Изображение успешно загружено")
			})
		}

		return {
			image,
			backend_server,
			is_loading,
			is_dragging,
			drop,
			can_manage_logo
		}
	}
}
</script>
