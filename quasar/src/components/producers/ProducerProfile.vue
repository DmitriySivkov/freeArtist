<template>
	<div class="row">
		<div class="col-xs-12 col-md-3">
			<q-card
				class="q-ma-md bg-green-3"
				:class="{'bg-green-6': is_dragging}"
				style="height:300px"
			>
				<q-card-section
					class="row flex-center full-height"
				>
					<q-img
						v-if="team.detailed.logo"
						:src="backend_server + '/storage/' + team.detailed.logo"
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
			<q-markup-table
				dark
				class="bg-indigo-8 q-mt-md"
			>
				<tbody>
					<tr>
						<td class="text-left">Название</td>
						<td class="text-left">{{ team.display_name }}</td>
					</tr>
				</tbody>
			</q-markup-table>
		</div>
	</div>
</template>

<script>
import { ref } from "vue"
import { useNotification } from "src/composables/notification"
import { useStore } from "vuex"
import { useUserPermission } from "src/composables/userPermission"
import AddImageDialog from "components/dialogs/AddImageDialog"
import { Plugins, CameraResultType } from "@capacitor/core"
import { useQuasar } from "quasar"
import { cameraService } from "src/services/cameraService"
export default {
	props: {
		team: {
			type: Object,
			default: () => ({})
		}
	},
	setup(props) {
		const $q = useQuasar()
		const $store = useStore()
		const { hasPermission } = useUserPermission()
		const { Camera } = Plugins
		const { base64ToBlob } = cameraService()

		const image = ref(props.team.detailed.logo)

		const file_picker = ref(null)
		const is_dragging = ref(false)
		const is_loading = ref(false)

		const can_manage_logo = hasPermission(
			parseInt(props.team.id),
			"producer_logo"
		)
		const backend_server = process.env.BACKEND_SERVER
		const { notifySuccess } = useNotification()

		const drop = (e) => {
			file_picker.value.addFiles([e.dataTransfer.files[0]])
		}

		const addImage = () => {
			is_loading.value = true

			let form_data = new FormData()
			form_data.append("logo", image.value)

			// todo validate with q-file help
			$store.dispatch("producer/setProducerLogo", {
				logo: form_data,
				producer_id: props.team.detailed_id
			}).then(() => {
				is_loading.value = false
				is_dragging.value = false
				notifySuccess("Изображение успешно загружено")
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

		return {
			image,
			backend_server,
			is_loading,
			is_dragging,
			drop,
			addImage,
			can_manage_logo,
			showFilePrompt,
			file_picker
		}
	}
}
</script>
