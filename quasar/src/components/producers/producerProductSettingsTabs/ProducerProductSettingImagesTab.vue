<template>
	<q-card
		bordered
		class="col-xs-12 col-md-4 border-dashed bg-green-3 shadow-0 q-mb-md"
		:class="{'text-white bg-green-6 border-white': is_dragging, 'border-black': !is_dragging}"
		style="height:200px"
	>
		<q-card-section class="row flex-center full-height">
			<span class="text-center">Выберите изображение <br /> или переместите в эту область</span>
			<div
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

	<div
		v-if="modelValue.committed_images"
		class="row q-col-gutter-sm q-mt-md"
	>
		<q-card
			flat
			class="col-xs-12 col-md-6"
			v-for="image in modelValue.committed_images"
			:key="image.key"
		>
			<q-card-section
				horizontal
				class="justify-end bg-indigo-5 q-mb-xs q-pa-xs"
			>
				<q-icon
					class="cursor-pointer"
					size="32px"
					name="clear"
					color="red-3"
					@click="removeCommittedImage(image.key)"
				/>
			</q-card-section>
			<q-card-section horizontal>
				<q-img
					:src="image.src"
					fit="contain"
					style="height: 200px;"
				/>
			</q-card-section>
		</q-card>
	</div>

	<q-separator
		v-if="modelValue.committed_images && modelValue.images"
		class="q-my-md bg-primary"
	/>

	<div class="row q-col-gutter-sm">
		<q-card
			flat
			class="col-xs-12 col-md-6"
			v-for="image in modelValue.images"
			:key="image.id"
		>
			<q-card-section
				horizontal
				class="justify-end bg-indigo-7 q-mb-xs q-pa-xs"
			>
				<q-icon
					class="cursor-pointer"
					size="32px"
					:name="!image.to_delete ? 'clear' : 'restore'"
					:color="!image.to_delete ? 'red-3' : 'white'"
					@click="toggleImageRemoval(image.id)"
				/>
			</q-card-section>
			<q-card-section horizontal>
				<q-img
					:src="backend_server + '/storage/' + image.path"
					fit="contain"
					style="height: 200px;"
				/>
			</q-card-section>
		</q-card>
	</div>

	<q-uploader
		ref="uploader"
		accept=".jpg,image/*"
		multiple
		style="display:none"
		:factory="upload"
		@added="imageCommitted"
		@rejected="imageRejected"
	/>
</template>

<script>
import { useQuasar } from "quasar"
import { ref } from "vue"
import { Plugins, CameraResultType } from "@capacitor/core"
import { cameraService } from "src/services/cameraService"
import AddImageDialog from "src/components/dialogs/AddImageDialog.vue"
import _ from "lodash"
export default {
	components: {
		// eslint-disable-next-line vue/no-unused-components
		AddImageDialog
	},
	props: {
		modelValue: {
			type: Object,
			default: () => ({})
		}
	},
	setup(props, { emit }) {
		const $q = useQuasar()
		const image = ref(null)

		const uploader = ref(null)
		const is_dragging = ref(false)
		const is_loading = ref(false)

		const { base64ToBlob } = cameraService()
		const { Camera } = Plugins

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

		// todo - check camera
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
			const tmp_images = files.map((f) => {
				return {
					key: f.__key,
					src: URL.createObjectURL(f),
					instance: f
				}
			})

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

		const toggleImageRemoval = (image_id) => {
			let images = _.clone(props.modelValue.images)
			let image_index = images.findIndex((i) => i.id === image_id)
			let image = images.find((i) => i.id === image_id)

			if (image.hasOwnProperty("to_delete")) {
				delete image.to_delete
			} else {
				image.to_delete = 1
			}

			images[image_index] = image

			emit("update:modelValue", {
				...props.modelValue,
				images
			})
		}

		// todo rejection handling
		const imageRejected = (rejected_entries) => {
			console.log(rejected_entries)
		}

		// todo - bind uploading to this method
		const upload = (files) => {

		}

		const drop = (e) => {
			is_dragging.value = false
			uploader.value.addFiles([e.dataTransfer.files[0]])
		}

		return {
			image,
			uploader,
			showFilePrompt,
			imageCommitted,
			backend_server,
			is_dragging,
			is_loading,
			drop,
			removeCommittedImage,
			imageRejected,
			upload,
			toggleImageRemoval
		}
	}
}
</script>
