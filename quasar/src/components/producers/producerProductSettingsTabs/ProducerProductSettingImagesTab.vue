<template>
	<div class="row">
		{{ img_path }} <br/>
		{{ test }} <br/>
		{{ img }}
		<div class="col-12">
			<q-btn
				flat
				label="Добавить изображение"
				class="bg-primary text-white full-width q-pa-lg"
				@click="showFilePrompt"
			/>
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
			default: () => {}
		}
	},
	setup(props) {
		const $q = useQuasar()
		const $store = useStore()
		const $router = useRouter()
		const img = ref(null)
		const img_path = ref("")
		const file_picker = ref(null)
		const test = ref(0)

		const backend_server = process.env.BACKEND_SERVER

		const product_images = computed(() =>
			$store.state.userProducer.producers
				.find((p) => p.id === parseInt($router.currentRoute.value.params.team_id))
				.products
				.find((p) => p.id === props.selectedProduct.id)
				.images
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
			file_picker.value.pickFiles()
		}

		const showImage = () => {
			test.value = 1

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
					image: formData,
					producer_id: parseInt($router.currentRoute.value.params.team_id),
					product_id: props.selectedProduct.id
				})
				img.value = null
				img_path.value = ""
			}).onCancel(() => {
				img.value = null
				img_path.value = ""
			}).onDismiss(() => {
				img.value = null
				img_path.value = ""
			})
		}
		// todo - load to server
		const fromCamera = () => {
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
				encodingType: navigator.camera.EncodingType.JPG,
				mediaType: navigator.camera.MediaType.PICTURE,
				saveToPhotoAlbum: false,
				correctOrientation: true
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
			test
		}
	}
}
</script>
