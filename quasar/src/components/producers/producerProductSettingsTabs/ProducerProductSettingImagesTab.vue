<template>
	<q-uploader
		multiple
		accept=".jpg, image/*"
		:factory="syncImages"
		@rejected="onImagesRejected"
		class="full-width q-pa-md"
		style="max-height:100%"
		flat
	>
		<template v-slot:header="scope">
			<q-spinner
				v-if="scope.isUploading"
				class="q-uploader__spinner"
			/>
			<div class="row">
				<div class="col-9">
					<q-btn
						:disable="!scope.canUpload"
						flat
						icon="cloud_upload"
						label="Загрузить изображения"
						class="bg-secondary text-white full-width q-pa-lg"
						@click="scope.upload"
					/>
				</div>
				<div class="col-3">
					<q-btn
						:disable="!scope.canAddFiles"
						flat
						type="a"
						icon="add_box"
						class="full-width full-height"
					>
						<q-uploader-add-trigger />
					</q-btn>
				</div>
			</div>
		</template>
		<template v-slot:list="scope">
			<div class="row q-col-gutter-md q-mt-sm">
				<div
					class="col-xs-12 col-md-6 col-lg-4"
					v-for="file in scope.files"
					:key="file.__key"
				>
					<q-card style="min-height:250px">
						<q-card-section horizontal>
							<q-img
								:src="file.__img.src"
								fit="contain"
								style="max-height: 250px;"
							/>
						</q-card-section>
						<q-separator />
						<q-btn
							flat
							square
							icon="clear"
							class="bg-red text-white full-width q-pa-md"
							@click="scope.removeFile(file)"
						/>
					</q-card>
				</div>
			</div>
		</template>
	</q-uploader>

	<div class="row q-col-gutter-md q-mt-sm">
		<div
			class="col-xs-12 col-md-6 col-lg-4"
			v-for="image in selectedProduct.images"
			:key="image.id"
		>
			<q-card style="min-height:250px">
				<q-card-section horizontal>
					<q-img
						:src="common_site.dev_backend_addr + '/storage/' + image.path"
						fit="contain"
						style="max-height: 250px;"
					/>
				</q-card-section>
				<q-separator />
				<q-btn
					flat
					square
					icon="clear"
					class="bg-red text-white full-width q-pa-md"
					@click="deleteImage(image)"
				/>
			</q-card>
		</div>
	</div>
</template>

<script>
import { useRouter } from "vue-router"
import { useNotification } from "src/composables/notification"
import { ref, computed } from "vue"
import { useStore } from "vuex"
export default {
	props: {
		selectedProduct: {
			type: Object,
			default: () => {}
		}
	},
	setup(props) {
		//todo - @factory-failed
		const $store = useStore()
		const $router = useRouter()
		const { notifySuccess, notifyError } = useNotification()
		const common_site = computed(() => $store.state.common)

		const image = ref(null)
		const syncImages = async (files) => {
			image.value = files[0]

			const fileData = new FormData()
			fileData.append("image", image.value)

			$store.dispatch("userProducer/syncProducerProductImagesSettings", {
				producer_id: parseInt($router.currentRoute.value.params.team_id),
				product_id: props.selectedProduct.id,
				image: fileData
			}).then(() => {
				notifySuccess("Изображения продукта " + props.selectedProduct.title + " успешно добавлены")
			}).catch((error) => {
				notifyError(error.response.data)
			})

		}

		const onImagesRejected  = (rejectedEntries) =>
			notifyError("Не получилось загрузить " + rejectedEntries.length + " изображений")

		return {
			onImagesRejected,
			syncImages,
			common_site
		}
	}
}
</script>
