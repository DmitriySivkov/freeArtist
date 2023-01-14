<template>
	<div
		v-if="isAbleToManageProduct"
		class="row"
	>
		<div class="col-xs-12 col-md-4">
			<q-card
				bordered
				class="border-dashed bg-green-3 shadow-0"
				:class="{'text-white bg-green-6 border-white': is_dragging, 'border-black': !is_dragging}"
				style="height:300px"
			>
				<q-card-section
					class="row flex-center full-height"
				>
					<span>Добавить фото</span>
					<div
						v-if="isAbleToManageProduct"
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
		</div>
	</div>
	<div class="row q-mt-md q-col-gutter-sm">
		<div
			v-for="image in product_images"
			:key="image.id"
			class="col-xs-12 col-md-6 col-lg-4"
		>
			<q-card class="full-height">
				<q-card-section>
					<q-img
						:src="backend_server + '/storage/' + image.path"
						fit="contain"
					/>
				</q-card-section>
			</q-card>
		</div>
	</div>
	<q-file
		v-model="image"
		ref="file_picker"
		accept=".jpg, image/*"
		style="display:none"
		@update:model-value="addImage"
	/>
</template>

<script>
import { useQuasar } from "quasar"
import { useRouter } from "vue-router"
import { useNotification } from "src/composables/notification"
import { ref, computed } from "vue"
import { useStore } from "vuex"
import { useUserTeam } from "src/composables/userTeam"
import { Plugins, CameraResultType } from "@capacitor/core"
import { cameraService } from "src/services/cameraService"
import AddImageDialog from "src/components/dialogs/AddImageDialog"
import ShowImageDialog from "src/components/dialogs/ShowImageDialog"
export default {
	components: {
		// eslint-disable-next-line vue/no-unused-components
		AddImageDialog,
		// eslint-disable-next-line vue/no-unused-components
		ShowImageDialog
	},
	props: {
		selectedProduct: {
			type: Object,
			default: () => ({})
		},
		isAbleToManageProduct: Boolean
	},
	setup(props) {
		const $q = useQuasar()
		const $store = useStore()
		const $router = useRouter()
		const image = ref(null)
		const file_picker = ref(null)
		const img_source = ref(null)
		const is_dragging = ref(false)
		const is_loading = ref(false)
		const { base64ToBlob } = cameraService()
		const { Camera } = Plugins
		const { notifySuccess, notifyError } = useNotification()
		const { user_teams } = useUserTeam()

		const backend_server = process.env.BACKEND_SERVER

		const product_images = computed(() =>
			props.selectedProduct.id ?
				user_teams.value.find((t) => t.detailed_id === parseInt($router.currentRoute.value.params.producer_id))
					.products.find((p) => p.id === props.selectedProduct.id).images :
				[]
		)

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
			img_source.value = 1
			file_picker.value.pickFiles()
		}

		const fromCamera = async () => {
			img_source.value = 2
			const img = await Camera.getPhoto({
				quality: 90,
				allowEditing: false,
				resultType: CameraResultType.DataUrl
			})
			const blob = await base64ToBlob(img.dataUrl)
			const img_file = new File([blob], "no-matter.jpg")
			file_picker.value.addFiles([img_file])
		}

		const addImage = () => {
			is_loading.value = true
			let formData = new FormData()
			formData.append("image", image.value)

			$store.dispatch("producer/addProducerProductImage", {
				image: formData,
				producer_id: parseInt($router.currentRoute.value.params.producer_id),
				product_id: props.selectedProduct.id
			}).then(() => {
				is_loading.value = false
				is_dragging.value = false
				notifySuccess("Изображение успешно загружено")
			})
		}


		return {
			image,
			file_picker,
			showFilePrompt,
			addImage,
			product_images,
			backend_server,
			is_dragging,
			is_loading
		}
	}
}
</script>
