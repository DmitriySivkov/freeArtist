<template>
	<div class="row justify-center">
		<q-card class="col-xs-12 col-sm-9 bg-green-3 q-mb-xs">
			<q-responsive
				:ratio="16/9"
				:class="{'bg-green-6 text-white': isDragging}"
			>
				<div>
					<Cropper
						v-if="tmpImage"
						ref="cropper"
						class="fit"
						:src="tmpImage"
						:stencil-props="{aspectRatio: 16/9}"
					/>

					<div
						v-else
						class="flex flex-center fit text-center cursor-pointer text-body1"
						@dragenter.prevent="isDragging = true"
						@dragleave.prevent="isDragging = false"
						@dragover.prevent
						@drop.prevent="drop"
						@click="showFilePrompt"
					>
						Выберите изображение
					</div>
				</div>
			</q-responsive>
		</q-card>
	</div>

	<div
		v-if="tmpImage"
		class="row flex-center"
	>
		<div class="col-xs-12 col-sm-9 q-mb-xs">
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

	<div class="row">
		<q-carousel
			ref="carousel"
			v-model="slide"
			class="col-12 bg-grey-4 product__card-carousel rounded-borders"
			transition-prev="slide-right"
			transition-next="slide-left"
			swipeable
			animated
			:draggable="false"
			keep-alive
			:arrows="modelValue.images.length > imagesPerSlide"
		>
			<q-carousel-slide
				v-for="i in Math.ceil(modelValue.images.length/2)"
				:key="i"
				:name="i"
				class="q-pa-none overflow-hidden"
			>
				<div
					class="col row"
					:class="
						modelValue.images.length > 2 ?
							(
								i === 1 ? 'justify-start'
								: (i === Math.ceil(modelValue.images.length/2) ? 'justify-end' : 'justify-center')
							)
							: 'justify-center'
					"
				>
					<div class="col-10">
						<div
							class="row q-gutter-xs no-wrap"
							:class="
								modelValue.images.length > 2 ?
									(
										i === 1 ? 'justify-start'
										: (i === Math.ceil(modelValue.images.length/2) ? 'justify-end' : 'justify-center')
									)
									: 'justify-center'
							"
						>
							<q-card
								v-for="image in modelValue.images.slice((i-1)*3 - (i === 1 ? 0 : i), i*3 - (i === 1 ? 0 : i-1))"
								:key="image.id"
								class="col-6 full-height"
							>
								<q-img
									class="cursor-pointer"
									no-spinner
									:src="`${backendServer}/storage/${image.path}`"
								>
									<div
										v-if="image.id === modelValue.thumbnail_id"
										class="absolute-bottom text-center"
										style="padding:0 !important;"
									>
										Заставка
									</div>
								</q-img>
							</q-card>
							<div
								v-if="
									modelValue.images.length > 2 &&
										modelValue.images.length % 2 !== 0 &&
										i === Math.ceil(modelValue.images.length/2)
								"
								class="col-6"
							></div>
						</div>
					</div>
				</div>
			</q-carousel-slide>

			<template #control>
				<q-carousel-control
					v-if="slide !== 1"
					position="left"
					:offset="[0, 0]"
					class="flex flex-center"
				>
					<q-btn
						push
						round
						dense
						color="orange"
						text-color="black"
						icon="arrow_left"
						@click="carousel.previous()"
					/>
				</q-carousel-control>

				<q-carousel-control
					v-if="modelValue.images.length > 2 && slide !== Math.ceil(modelValue.images.length/2)"
					position="right"
					:offset="[0, 0]"
					class="flex flex-center"
				>
					<q-btn
						push
						round
						dense
						color="orange"
						text-color="black"
						icon="arrow_right"
						@click="carousel.next()"
					/>
				</q-carousel-control>
			</template>
		</q-carousel>
	</div>

	<div class="row justify-center">
		<div class="col-xs-12 col-sm-9">
			<div
				v-if="modelValue.committed_images?.length"
				class="column"
			>
				<q-img
					v-for="(image, i) in modelValue.committed_images"
					:key="i"
					class="col-auto cursor-pointer rounded-borders q-mt-xs"
					:src="image.src"
					no-spinner
				>
					<div
						class="row absolute-bottom"
						style="padding:0 !important;"
					>
						<div class="col text-center content-center">
							<q-btn
								dense
								flat
								no-caps
								:label="image.src !== modelValue.thumbnail_id ? 'Сделать заставкой' : 'Заставка'"
								class="fit text-center text-body1 q-pa-sm"
								@click="toggleThumbnail(image.src)"
							/>
						</div>
						<q-separator
							vertical
							color="white"
							class="q-my-xs"
						/>
						<div class="col text-center content-center">
							<q-btn
								dense
								flat
								no-caps
								label="Не загружать"
								class="fit text-center text-body1 q-pa-sm"
								@click="showRemoveCommittedImageDialog(image.src)"
							/>
						</div>
					</div>
				</q-img>
			</div>
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
import { clone, omit } from "lodash"
import { Cropper } from "vue-advanced-cropper"
import { useScreen } from "src/composables/screen"
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
const { isSmallScreen } = useScreen()

const image = ref(null)
const tmpImage = ref(null)

const carousel = ref(null)
const slide = ref(1)
const imagesPerSlide = isSmallScreen.value ? 1 : 3

const filePicker = ref(null)
const cropper = ref(null)

const isDragging = ref(false)

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
	filePicker.value.pickFiles()
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

const toggleThumbnail = (imageId) => {
	if (props.modelValue.thumbnail_id === imageId) return

	emit("update:modelValue", Object.assign(props.modelValue, {
		thumbnail_id: imageId
	}))
}

// todo - if images are deleted from the last slide then all images disappear (reset count slide on save)
</script>
