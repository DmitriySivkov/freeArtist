<template>
	<div
		v-if="permissions.update"
		class="row"
	>
		<div class="col-xs-12 col-md-4">
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
						v-if="img"
						:src="backend_server + '/storage/' + image"
						fit="contain"
					/>
					<span v-else>Добавить фото</span>
					<div
						v-if="permissions.update"
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
		v-model="img"
		ref="file_picker"
		accept=".jpg, image/*"
		style="display:none"
		@update:model-value="showImage"
	/>
</template>

<script>
import { useQuasar } from "quasar"
import { useRouter } from "vue-router"
import { useNotification } from "src/composables/notification"
import { ref, computed } from "vue"
import { useStore } from "vuex"
import cordovaCamera from "src/services/cordova-camera"
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
		permissions: {
			type: Object,
			default: () => ({})
		}
	},
	setup(props) {
		const $q = useQuasar()
		const $store = useStore()
		const $router = useRouter()
		const img = ref(null)
		const img_path = ref("")
		const file_picker = ref(null)
		const img_source = ref(null)
		const is_dragging = ref(false)
		const is_loading = ref(false)
		const { notifySuccess, notifyError } = useNotification()

		const backend_server = process.env.BACKEND_SERVER

		const product_images = computed(() =>
			props.selectedProduct.id ?
				$store.state.userProducer.producers
					.find((p) => p.id === parseInt($router.currentRoute.value.params.team_id))
					.products
					.find((p) => p.id === props.selectedProduct.id)
					.images :
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

		const fromCamera = () => {
			img_source.value = 2
			navigator.camera.getPicture(imageURI => {
				window.resolveLocalFileSystemURL(imageURI,
					function (fileEntry) {
						fileEntry.file(
							async function (fileObject) {
								img_path.value = await cordovaCamera.getBase64FromFileObject(fileObject)
								file_picker.value.addFiles([fileObject])
							},
							function (err) {
								console.log("error file") // todo - notify
							}
						)
					},
					function () { } // todo - notify
				)
			},
			console.error, // todo - notify camera error
			{
				quality: 50,
				destinationType: navigator.camera.DestinationType.FILE_URI,
				encodingType: navigator.camera.EncodingType.JPEG,
				mediaType: navigator.camera.MediaType.PICTURE,
				saveToPhotoAlbum: false,
				correctOrientation: true
			})
		}

		const showImage = () => {
			if (!img_path.value)
				img_path.value = URL.createObjectURL(img.value)

			$q.dialog({
				component: ShowImageDialog,
				componentProps: {
					imagePath: img_path.value
				}
			}).onOk(() => {
				let formData = new FormData()
				formData.append("image", img.value)

				$store.dispatch("userProducer/addProducerProductImage", {
					image: img_source.value === 1 ? formData : img_path.value,
					producer_id: parseInt($router.currentRoute.value.params.team_id),
					product_id: props.selectedProduct.id
				}).then(() => {
					notifySuccess("Изображение успешно загружено")
					img.value = null
					img_path.value = ""
				})

			}).onCancel(() => {
				img.value = null
				img_path.value = ""
			}).onDismiss(() => {
				img.value = null
				img_path.value = ""
			})
		}

		return {
			img,
			img_path,
			file_picker,
			showFilePrompt,
			showImage,
			product_images,
			backend_server,
			is_dragging,
			is_loading
		}
	}
}
</script>
