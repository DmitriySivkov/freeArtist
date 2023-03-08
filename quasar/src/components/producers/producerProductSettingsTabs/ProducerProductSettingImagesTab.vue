<template>
	<q-card
		v-if="isAbleToManageProduct"
		bordered
		class="col-xs-12 col-md-4 border-dashed bg-green-3 shadow-0"
		:class="{'text-white bg-green-6 border-white': is_dragging, 'border-black': !is_dragging}"
		style="height:200px"
	>
		<q-card-section class="row flex-center full-height">
			<span class="text-center">Выберите изображение <br /> или переместите в эту область</span>
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

	<!--	&lt;!&ndash; todo - all photos in one carousel &ndash;&gt;-->
	<!--	<div class="row q-col-gutter-sm q-mt-md"> -->
	<!--		<q-card-->
	<!--			flat-->
	<!--			class="col-xs-12 col-md-3"-->
	<!--			v-for="image in modelValue.images"-->
	<!--			:key="image.id"-->
	<!--		>-->
	<!--			<q-img-->
	<!--				:src="backend_server + '/storage/' + image.path"-->
	<!--				fit="contain"-->
	<!--				style="height: 200px;"-->
	<!--			/>-->
	<!--		</q-card>-->
	<!--	</div>-->

	<!-- todo - continue with committed images and upload it all in a bulk to the server-->
	<div
		v-if="modelValue.committed_images"
		class="row q-col-gutter-sm q-mt-md"
	>
		<q-card
			flat
			class="col-xs-12 col-md-3"
			v-for="image in modelValue.committed_images"
			:key="image.key"
		>
			<q-img
				:src="image.src"
				fit="contain"
				style="height: 200px;"
			>
				<!-- todo - change inline style -->
				<div
					style="padding:8px"
					class="absolute-top text-right"
				>
					<q-icon
						class="cursor-pointer"
						size="32px"
						name="clear"
						color="black"
						@click="removeCommittedImage(image.key)"
					/>
				</div>
			</q-img>
		</q-card>
	</div>

	<q-uploader
		ref="uploader"
		accept=".jpg,image/*"
		multiple
		style="display:none"
		@added="imageCommitted"
		@rejected="imageRejected"
	/>
</template>

<script>
import { useQuasar } from "quasar"
import { useRouter } from "vue-router"
import { useNotification } from "src/composables/notification"
import { ref, computed } from "vue"
import { useUserTeam } from "src/composables/userTeam"
import { Plugins, CameraResultType } from "@capacitor/core"
import { cameraService } from "src/services/cameraService"
import AddImageDialog from "src/components/dialogs/AddImageDialog.vue"
import { useProducerStore } from "src/stores/producer"
export default {
	components: {
		// eslint-disable-next-line vue/no-unused-components
		AddImageDialog
	},
	props: {
		modelValue: {
			type: Object,
			default: () => ({})
		},
		isAbleToManageProduct: Boolean
	},
	setup(props, { emit }) {
		const $q = useQuasar()
		const $router = useRouter()
		const producer_store = useProducerStore()
		const image = ref(null)

		const uploader = ref(null)
		const is_dragging = ref(false)
		const is_loading = ref(false)
		const { base64ToBlob } = cameraService()
		const { Camera } = Plugins
		const { notifySuccess, notifyError } = useNotification()

		const backend_server = process.env.BACKEND_SERVER

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
			uploader.value.pickFiles()
		}

		const fromCamera = async () => {
			const img = await Camera.getPhoto({
				quality: 90,
				allowEditing: false,
				resultType: CameraResultType.DataUrl
			})
			const blob = await base64ToBlob(img.dataUrl)
			const img_file = new File([blob], "no-matter.jpg")
			uploader.value.addFiles([img_file])
		}

		const imageCommitted = (files) => {
			const tmp_images = files.map((f) => ({
				key: f.__key,
				src: URL.createObjectURL(f),
				instance: f
			}))

			if (!props.modelValue.committed_images) {
				emit("update:modelValue", {...props.modelValue, committed_images: tmp_images })
				return
			}

			emit("update:modelValue", {
				...props.modelValue,
				committed_images: [...tmp_images, ...props.modelValue.committed_images]
			})

		}

		const removeCommittedImage = (committed_image_key) => {
			uploader.value.removeFile(
				props.modelValue.committed_images.find((i) => i.key === committed_image_key).instance
			)

			// if deleting the last committed image
			if (props.modelValue.committed_images.length === 1) {
				emit(
					"update:modelValue",
					{
						...Object.keys(props.modelValue)
							.filter((prop) => prop !== "committed_images")
							.reduce((carry, prop) => {
								carry[prop] = props.modelValue[prop]
								return carry
							}, {})
					}
				)
				return
			}

			emit("update:modelValue", {
				...props.modelValue,
				committed_images: props.modelValue.committed_images.filter((i) => i.key !== committed_image_key)
			})
		}

		// todo rejection handling
		const imageRejected = (rejected_entries) => {
			console.log(rejected_entries)
		}

		// const addImage = () => {
		// 	is_loading.value = true
		// 	let form_data = new FormData()
		// 	form_data.append("image", image.value)
		//
		// 	producer_store.addProducerProductImage({
		// 		image: form_data,
		// 		producer_id: parseInt($router.currentRoute.value.params.producer_id),
		// 		product_id: props.modelValue.id
		// 	}).then(() => {
		// 		is_loading.value = false
		// 		is_dragging.value = false
		// 		notifySuccess("Изображение успешно загружено")
		// 	})
		// }

		const drop = (e) => {
			is_dragging.value = false
			uploader.value.addFiles([e.dataTransfer.files[0]])
		}

		return {
			image,
			uploader,
			showFilePrompt,
			// addImage,
			imageCommitted,
			backend_server,
			is_dragging,
			is_loading,
			drop,
			removeCommittedImage,
			imageRejected
		}
	}
}
</script>
