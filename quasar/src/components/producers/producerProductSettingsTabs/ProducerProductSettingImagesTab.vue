<template>
	<q-card
		bordered
		class="col-xs-12 col-md-4 border-dashed bg-green-3 shadow-0 q-mb-xs"
		:class="[
			{'text-white bg-green-6 border-white': is_dragging},
			{'border-black': !is_dragging}
		]"
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
					no-spinner
					fit="contain"
					style="height: 200px;"
				/>
			</q-card-section>
		</q-card>
	</div>

	<q-carousel
		ref="carousel"
		v-model="slide"
		class="bg-secondary"
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
					<div class="col-sm-8 col-lg-6">
						<div class="row fit flex-center q-gutter-xs q-col-gutter no-wrap">
							<q-img
								v-for="image in modelValue.images.slice((n-1)*2, n*2)"
								:key="image.id"
								no-spinner
								class="rounded-borders col-6 full-height"
								fit="cover"
								:src="`${backendServer}/storage/${image.path}`"
								:ratio="16/9"
							/>
						</div>
					</div>
				</div>
			</q-carousel-slide>
		</template>
		<template v-else>
			<!--			<q-carousel-slide-->
			<!--				v-for="(image, i) in modelValue.images"-->
			<!--				:key="image.id"-->
			<!--				:name="i+1"-->
			<!--				:img-src="`${backendServer}/storage/${image.path}`"-->
			<!--			/>-->
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
				/>
			</q-carousel-slide>
		</template>

	</q-carousel>

	<!--	<div class="row q-col-gutter-sm">-->
	<!--		<q-card-->
	<!--			flat-->
	<!--			class="col-xs-12 col-md-6"-->
	<!--			v-for="image in modelValue.images"-->
	<!--			:key="image.id"-->
	<!--		>-->
	<!--			<q-card-section-->
	<!--				horizontal-->
	<!--				class="bg-indigo-7 q-mb-xs q-pa-xs"-->
	<!--			>-->
	<!--				<div class="col text-left">-->
	<!--					<q-icon-->
	<!--						class="cursor-pointer"-->
	<!--						size="32px"-->
	<!--						name="wallpaper"-->
	<!--						:color="modelValue.thumbnail_id === image.id ? 'white' : 'black'"-->
	<!--						@click="toggleImageThumbnail(image.id)"-->
	<!--					/>-->
	<!--				</div>-->
	<!--				<div class="col self-end text-right">-->
	<!--					<q-icon-->
	<!--						class="cursor-pointer"-->
	<!--						size="32px"-->
	<!--						:name="!image.to_delete ? 'clear' : 'restore'"-->
	<!--						:color="!image.to_delete ? 'black' : 'white'"-->
	<!--						@click="toggleImageRemoval(image.id)"-->
	<!--					/>-->
	<!--				</div>-->
	<!--			</q-card-section>-->
	<!--			<q-card-section horizontal>-->
	<!--				<q-img-->
	<!--					:src="backendServer + '/storage/' + image.path"-->
	<!--					no-spinner-->
	<!--					fit="contain"-->
	<!--					style="height: 200px;"-->
	<!--				/>-->
	<!--			</q-card-section>-->
	<!--		</q-card>-->
	<!--	</div>-->

	<q-file
		v-model="image"
		ref="file_picker"
		accept=".jpg, image/*"
		style="display:none"
		@update:model-value="addImage"
	/>
</template>

<script setup>
import { useQuasar } from "quasar"
import {computed, ref} from "vue"
import { Plugins, CameraResultType } from "@capacitor/core"
import { cameraService } from "src/services/cameraService"
import AddImageDialog from "src/components/dialogs/AddImageDialog.vue"
import _ from "lodash"

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

const slide = ref(1)

const uploader = ref(null)
const is_dragging = ref(false)
const is_loading = ref(false)

const { base64ToBlob } = cameraService()
const { Camera } = Plugins

const backendServer = process.env.BACKEND_SERVER

const isWidthThreshold = computed(() => $q.screen.width >= $q.screen.sizes.sm)

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

const toggleImageThumbnail = (image_id) => {
	let thumbnail_id = props.modelValue.thumbnail_id === image_id ? null : image_id

	emit("update:modelValue", Object.assign(props.modelValue, { thumbnail_id }))
}

const drop = (e) => {
	is_dragging.value = false
	uploader.value.addFiles([e.dataTransfer.files[0]])
}

</script>
