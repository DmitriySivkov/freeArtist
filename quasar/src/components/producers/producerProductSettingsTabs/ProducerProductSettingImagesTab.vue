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
					<q-inner-loading
						:showing="isLoading"
						style="z-index:99999"
					>
						<q-spinner-gears
							size="50px"
							color="primary"
						/>
					</q-inner-loading>
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
					@click="addImage"
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
											style="padding:8px 12px"
										>
											<div class="col">
												<q-icon
													class="cursor-pointer"
													size="md"
													name="wallpaper"
													:color="modelValue.thumbnail_id === image.id ? 'white' : 'black'"
													@click="toggleImageThumbnail(image.id)"
												/>
											</div>
											<div class="col text-right">
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
								style="padding:8px 12px"
							>
								<div class="col">
									<q-icon
										class="cursor-pointer"
										size="md"
										name="wallpaper"
										:color="modelValue.thumbnail_id === image.id ? 'white' : 'black'"
										@click="toggleImageThumbnail(image.id)"
									/>
								</div>
								<div class="col text-right">
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
import { useQuasar } from "quasar"
import { computed, ref} from "vue"
import { Plugins, CameraResultType } from "@capacitor/core"
import { cameraService } from "src/services/cameraService"
import AddImageDialog from "src/components/dialogs/AddImageDialog.vue"
import { clone } from "lodash"
import { Cropper } from "vue-advanced-cropper"

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
const isDragging = ref(false)
const isLoading = ref(false)

const { base64ToBlob } = cameraService()
const { Camera } = Plugins

const backendServer = process.env.BACKEND_SERVER

const isWidthThreshold = computed(() => $q.screen.width >= $q.screen.sizes.sm)

const addImage = () => {
	tmpImage.value = URL.createObjectURL(image.value)
}

const cancelImage = () => {
	tmpImage.value = null
	image.value = null
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
	const img_file = new File([blob], "no-matter.jpg")
	filePicker.value.addFiles([img_file])
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

const drop = (e) => {
	isDragging.value = false
	filePicker.value.addFiles([e.dataTransfer.files[0]])
}

</script>
