<template>
	<div class="row flex-center">
		<q-card class="col-xs-12 col-sm-7 col-lg-6 col-xl-5 bg-green-3 q-mb-xs">
			<q-responsive
				:ratio="16/9"
				:class="{'bg-green-6 text-white': isDragging}"
			>
				<div>
					<Cropper
						v-if="tmpImage"
						ref="cropper"
						class="absolute"
						:src="tmpImage"
						:stencil-props="{aspectRatio: 16/9}"
					/>

					<div
						v-else
						class="absolute fit flex flex-center text-center"
					>
						Выберите изображение <br /> или переместите в эту область
					</div>

					<div
						class="absolute fit cursor-pointer"
						@dragenter.prevent="isDragging = true"
						@dragleave.prevent="isDragging = false"
						@dragover.prevent
						@drop.prevent="drop"
						@click="showFilePrompt"
					></div>
				</div>
			</q-responsive>
		</q-card>
	</div>

	<div
		v-if="tmpImage"
		class="row flex-center"
	>
		<div class="col-xs-12 col-sm-7 col-lg-6 col-xl-5 q-mb-xs">
			<div class="row q-gutter-xs q-mb-xs justify-center">
				<q-btn
					color="primary"
					icon="done"
					class="col q-pa-lg"
					@click="commitImage"
				/>
				<q-btn
					color="red"
					icon="clear"
					class="col q-pa-lg"
					@click="cancelImage"
				/>
			</div>
		</div>
	</div>

	<div class="row justify-center">
		<div class="col-xs-12 col-sm-11 col-md-10 col-lg-9">
			<div
				v-if="modelValue.committed_images && modelValue.committed_images.length"
				class="row q-col-gutter-xs justify-center q-mb-xs"
			>
				<div
					v-for="(image, i) in modelValue.committed_images"
					:key="i"
					class="col-xs-4 col-sm-3 col-md-2 cursor-pointer"
					@click="showRemoveCommittedImageDialog(image.src)"
				>
					<q-card>
						<q-img
							:src="image.src"
							no-spinner
							:ratio="16/9"
							style="opacity:0.8"
						>
							<div
								class="row absolute-top full-height"
								style="padding:0"
							>
								<div class="col text-right">
									<q-icon
										class="cursor-pointer"
										size="xs"
										name="clear"
										color="red-4"
									/>
								</div>
							</div>
						</q-img>
					</q-card>
				</div>
			</div>
		</div>
	</div>

	<div class="row justify-center">
		<div class="col-xs-12 col-sm-11 col-md-10 col-lg-9 col-xl-8">
			<q-carousel
				ref="carousel"
				v-model="slide"
				transition-prev="fade"
				transition-next="fade"
				control-type="regular"
				control-color="primary"
				control-text-color="white"
				swipeable
				animated
				height="auto"
				:arrows="modelValue.images.length > 2"
			>
				<template v-if="isWidthThreshold">
					<q-carousel-slide
						v-for="n in Math.ceil(modelValue.images.length/2)"
						:key="n"
						:name="n"
						class="column no-wrap q-px-none q-py-sm"
					>
						<div
							v-if="isWidthThreshold"
							class="row fit flex-center"
						>
							<div class="col-9">
								<div class="row fit flex-center q-gutter-xs q-col-gutter no-wrap">
									<q-img
										v-for="image in modelValue.images.slice((n-1)*2, n*2)"
										:key="image.id"
										no-spinner
										class="rounded-borders col-6 full-height"
										fit="cover"
										:src="`${backendServer}/storage/${image.path}`"
										:ratio="16/9"
									>
										<div
											class="row absolute-bottom"
											:class="{'full-height': image.to_delete}"
											style="padding:8px 12px"
										>
											<div class="col self-end">
												<q-icon
													class="cursor-pointer"
													size="md"
													name="wallpaper"
													:color="modelValue.thumbnail_id === image.id ? 'white' : 'black'"
													@click="toggleImageThumbnail(image.id)"
												/>
											</div>
											<div class="col text-right self-end">
												<q-icon
													class="cursor-pointer"
													size="md"
													:name="!image.to_delete ? 'clear' : 'restore'"
													:color="!image.to_delete ? 'red-4' : 'green-4'"
													@click="toggleImageRemoval(image.id)"
												/>
											</div>
										</div>
									</q-img>
								</div>
							</div>
						</div>
					</q-carousel-slide>
				</template>
				<template v-else>
					<q-carousel-slide
						v-for="(image, i) in modelValue.images"
						:key="image.id"
						:name="i+1"
						class="q-pa-xs"
					>
						<q-img
							no-spinner
							class="rounded-borders"
							fit="cover"
							:src="`${backendServer}/storage/${image.path}`"
							:ratio="16/9"
						>
							<div
								class="row absolute-bottom"
								:class="{'full-height': image.to_delete}"
								style="padding:8px 12px"
							>
								<div class="col self-end">
									<q-icon
										class="cursor-pointer"
										size="md"
										name="wallpaper"
										:color="modelValue.thumbnail_id === image.id ? 'white' : 'black'"
										@click="toggleImageThumbnail(image.id)"
									/>
								</div>
								<div class="col text-right self-end">
									<q-icon
										class="cursor-pointer"
										size="md"
										:name="!image.to_delete ? 'clear' : 'restore'"
										:color="!image.to_delete ? 'red-4' : 'green-4'"
										@click="toggleImageRemoval(image.id)"
									/>
								</div>
							</div>
						</q-img>
					</q-carousel-slide>
				</template>
			</q-carousel>
		</div>
	</div>

	<q-file
		v-model="image"
		ref="filePicker"
		accept=".jpg, image/*"
		style="display:none"
		@update:model-value="addImage"
	/>
</template>

<script setup>
import { useQuasar, Dialog } from "quasar"
import { computed, ref} from "vue"
import { Plugins, CameraResultType } from "@capacitor/core"
import { cameraService } from "src/services/cameraService"
import AddImageDialog from "src/components/dialogs/AddImageDialog.vue"
import { clone, omit } from "lodash"
import { Cropper } from "vue-advanced-cropper"
import CommonConfirmationDialog from "src/components/dialogs/CommonConfirmationDialog.vue"

const props = defineProps({
	modelValue: {
		type: Object,
		default: () => ({})
	}
})

const emit = defineEmits([
	"update:modelValue"
])

const $q = useQuasar()
const image = ref(null)
const tmpImage = ref(null)

const slide = ref(1)

const filePicker = ref(null)
const cropper = ref(null)

const isDragging = ref(false)

const { base64ToBlob } = cameraService()
const { Camera } = Plugins

const backendServer = process.env.BACKEND_SERVER

const isWidthThreshold = computed(() => $q.screen.width >= $q.screen.sizes.sm)

const commitImage = () => {
	const { canvas } = cropper.value.getResult()

	const blobPromise = new Promise((resolve) => canvas.toBlob(resolve, "image/jpeg"))

	blobPromise.then((result) => {
		const img = {
			src: URL.createObjectURL(result),
			instance: result
		}

		if (!props.modelValue.committed_images) {
			emit("update:modelValue", {
				...props.modelValue,
				committed_images: [img]
			})
		} else {
			emit("update:modelValue", {
				...props.modelValue,
				committed_images: [img, ...props.modelValue.committed_images]
			})
		}

		cancelImage()
	})
}

const showRemoveCommittedImageDialog = (imgSrc) => {
	Dialog.create({
		component: CommonConfirmationDialog,
		componentProps: {
			text: "Не загружать выбранное изображение?",
			headline: "Подтвердите действие"
		}
	}).onOk(() => {
		let committedImages = props.modelValue.committed_images.filter((ci) => ci.src !== imgSrc)

		if (committedImages.length > 0) {
			emit("update:modelValue", {
				...props.modelValue,
				committed_images: committedImages
			})
		} else {
			emit("update:modelValue", omit(props.modelValue, "committed_images"))
		}
	})
}

const addImage = () => {
	tmpImage.value = URL.createObjectURL(image.value)
}

const cancelImage = () => {
	tmpImage.value = null
	image.value = null
}

const drop = (e) => {
	isDragging.value = false
	filePicker.value.addFiles([e.dataTransfer.files[0]])
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
	filePicker.value.pickFiles()
}

// todo - check camera
const fromCamera = async () => {
	const img = await Camera.getPhoto({
		quality: 90,
		allowEditing: false,
		resultType: CameraResultType.DataUrl
	})

	const blob = await base64ToBlob(img.dataUrl)
	const imgFile = new File([blob], "no-matter.jpg")

	filePicker.value.addFiles([imgFile])
}

const toggleImageRemoval = (image_id) => {
	let images = clone(props.modelValue.images)
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

const toggleImageThumbnail = (image_id) => {
	let thumbnail_id = props.modelValue.thumbnail_id === image_id ? null : image_id

	emit("update:modelValue", Object.assign(props.modelValue, { thumbnail_id }))
}
</script>
