<template>
	<div class="row">
		<div class="col-12">
			<q-btn
				flat
				label="Добавить изображение"
				class="bg-primary text-white full-width q-pa-lg"
				@click="showFilePrompt"
			/>
		</div>
	</div>
	<div class="row q-mt-md">
		<div
			v-for="(image, index) in product_images"
			:key="image.id"
			class="col-xs-3"
			:class="{
				'q-pr-none': index === product_images.length-1,
				'q-pl-none': index === 0,
				'q-pl-sm q-pr-sm': index !== product_images.length-1 && index !== 0
			}"
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

		const fromCamera = async() => {
			const base64 = await cordovaCamera.getBase64FromCamera()
			cordovaCamera.b64ToBlob(base64)
				.then((res) => img.value = res)
		}

		const showImage = () => {
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
			backend_server
		}
	}
}
</script>
