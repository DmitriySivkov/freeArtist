<template>
	<div class="row q-mt-md">
		<div class="col-xs-12 col-md-3">
			<q-card
				class="q-mx-md bg-green-3"
				:class="{'bg-green-6': is_dragging}"
				style="height:300px"
			>
				<q-card-section class="row flex-center full-height">
					<q-img
						v-if="team.detailed.logo"
						:src="backend_server + '/storage/' + team.detailed.logo.path"
						fit="contain"
					/>
					<span
						v-else
						class="text-center"
					>
						Выберите изображение <br /> или переместите в эту область
					</span>
					<div
						v-if="can_manage_logo || is_team_admin"
						class="full-height full-width absolute cursor-pointer"
						@dragenter.prevent="is_dragging = true"
						@dragleave.prevent="is_dragging = false"
						@dragover.prevent
						@drop.prevent="drop"
						@click="showFilePrompt"
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
				@update:model-value="addImage"
			/>
		</div>
		<div class="col-xs-12 col-md-3">
			<q-card>
				<q-list class="bg-primary text-white">
					<q-item clickable>
						<q-item-section avatar>
							<q-icon
								color="white"
								name="edit"
							/>
						</q-item-section>
						<q-item-section class="text-center">
							{{ team.display_name }}
						</q-item-section>
						<q-popup-edit
							:model-value="team.display_name"
							@update:model-value="teamPropChanged($event, 'display_name')"
							auto-save
							v-slot="scope"
						>
							<q-input
								:model-value="scope.value"
								@update:model-value="scope.value = $event"
								dense
								autofocus
								@keyup.enter="scope.set"
							/>
						</q-popup-edit>
					</q-item>

					<q-item clickable>
						<q-item-section avatar>
							<q-icon
								color="white"
								name="edit"
							/>
						</q-item-section>
						<q-item-section class="text-center">
							{{ team.description }}
						</q-item-section>
						<q-popup-edit
							:model-value="team.description"
							@update:model-value="teamPropChanged($event, 'description')"
							auto-save
							v-slot="scope"
						>
							<q-input
								:model-value="scope.value"
								@update:model-value="scope.value = $event"
								dense
								autofocus
								@keyup.enter="scope.set"
							/>
						</q-popup-edit>
					</q-item>
				</q-list>
			</q-card>
		</div>
	</div>
</template>

<script>
import { computed, ref } from "vue"
import { useNotification } from "src/composables/notification"
import AddImageDialog from "components/dialogs/AddImageDialog.vue"
import { Plugins, CameraResultType } from "@capacitor/core"
import { useQuasar } from "quasar"
import { cameraService } from "src/services/cameraService"
import { useProducerStore } from "src/stores/producer"
import { usePermissionStore } from "src/stores/permission"
import { useUserStore } from "src/stores/user"
import { useTeamStore } from "src/stores/team"
export default {
	props: {
		team: {
			type: Object,
			default: () => ({})
		}
	},
	setup(props) {
		const $q = useQuasar()
		const user_store = useUserStore()
		const team_store = useTeamStore()
		const producer_store = useProducerStore()
		const permission_store = usePermissionStore()
		const { Camera } = Plugins
		const { base64ToBlob } = cameraService()

		const image = ref(null)

		const file_picker = ref(null)
		const is_dragging = ref(false)
		const is_loading = ref(false)

		const is_team_admin = computed(() => user_store.data.id === props.team.user_id)

		const can_manage_logo = computed(() =>
			permission_store.user_permissions.filter((p) => p.team_id === parseInt(props.team.id))
				.map((p) => p.name)
				.includes("producer_logo")
		)

		const backend_server = process.env.BACKEND_SERVER
		const { notifySuccess, notifyError } = useNotification()

		const drop = (e) => {
			file_picker.value.addFiles([e.dataTransfer.files[0]])
		}

		const addImage = () => {
			is_loading.value = true

			let form_data = new FormData()
			form_data.append("logo", image.value)

			// todo validate with q-file help
			const promise = producer_store.setProducerLogo({
				logo: form_data,
				producer_id: props.team.detailed_id
			})

			promise.then((response) => {
				team_store.setTeamFields({
					team_id: props.team.id,
					fields: {
						logo: response.data
					},
					detailed_id: props.team.detailed_id
				})

				notifySuccess("Изображение успешно загружено")
			})

			promise.catch((error) => {
				notifyError(error.response.data)
			})

			promise.finally(() => {
				is_loading.value = false
				is_dragging.value = false
			})
		}

		const showFilePrompt = () => {
			if ($q.platform.is.desktop) {
				fromGallery()
				return
			}

			$q.dialog({
				component: AddImageDialog
			}).onOk((option) => {
				if (option === 1)
					fromGallery()
				if (option === 2)
					fromCamera()
			})
		}

		const fromGallery = () => {
			file_picker.value.pickFiles()
		}

		const fromCamera = async () => {
			const img = await Camera.getPhoto({
				quality: 90,
				allowEditing: false,
				resultType: CameraResultType.DataUrl
			})
			const blob = await base64ToBlob(img.dataUrl)
			const img_file = new File([blob], "no-matter.jpg")
			file_picker.value.addFiles([img_file])
		}

		const teamPropChanged = (val, field) => {
			const promise = team_store.updateTeamFields({
				team_id: props.team.id,
				fields: { [field]: val }
			})

			promise.catch((error) => {
				notifyError(error.response.data.message)

				team_store.setTeamFields({
					team_id: error.response.data.team.id,
					fields: error.response.data.team
				})
			})
		}

		return {
			image,
			backend_server,
			is_loading,
			is_dragging,
			drop,
			addImage,
			can_manage_logo,
			is_team_admin,
			showFilePrompt,
			file_picker,
			teamPropChanged
		}
	}
}
</script>
