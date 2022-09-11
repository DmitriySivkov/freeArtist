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
			<q-btn
				v-if="scope.uploadedFiles.length > 0"
				label="Удалить загруженные изображения"
				@click="scope.removeUploadedFiles"
				class="full-width"
			/>
			<q-spinner
				v-if="scope.isUploading"
				class="q-uploader__spinner"
			/>

			<q-btn
				v-if="scope.canAddFiles"
				type="a"
				label="Добавить изображение"
				class="full-width q-pa-md"
			>
				<q-uploader-add-trigger />
			</q-btn>
		</template>
		<template v-slot:list="scope">
			<div class="row q-col-gutter-md q-mt-sm">
				<div
					class="col-xs-12 col-md-6 col-lg-3"
					v-for="file in scope.files"
					:key="file.__key"
				>
					<q-card>
						<q-img
							:src="file.__img.src"
							fit="scale-down"
							style="max-height: 250px;"
							class="q-mt-sm"
						/>
						<q-separator class="q-mt-sm q-mb-sm"/>
						<q-card-section
							horizontal
							class="q-col-gutter-xs"
						>
							<div class="col-6">
								<q-btn
									flat
									square
									icon="cloud_upload"
									class="bg-secondary text-white full-width q-pa-lg"
									@click="scope.upload"
								/>
							</div>
							<div class="col-6">
								<q-btn
									flat
									square
									icon="clear"
									class="bg-red text-white full-width q-pa-lg"
									@click="scope.removeFile(file)"
								/>
							</div>
						</q-card-section>
					</q-card>
				</div>
			</div>
		</template>
	</q-uploader>
</template>

<script>
import { useRouter } from "vue-router"
import { useNotification } from "src/composables/notification"
import { ref } from "vue"
import { useStore } from "vuex"

export default {
	props: {
		selectedProduct: {
			type: Object,
			default: () => {}
		}
	},
	setup(props) {
		const $store = useStore()
		const $router = useRouter()
		const { notifySuccess, notifyError } = useNotification()

		const image = ref(null)

		const syncImages = async (files) => {
			image.value = files[0]

			const fileData = new FormData()
			fileData.append("image", image.value)

			$store.dispatch("userProducer/addProducerProductImage", {
				producer_id: parseInt($router.currentRoute.value.params.team_id),
				product_id: props.selectedProduct.id,
				image: fileData
			}).then(() => {
				notifySuccess("Изображения продукта " + props.selectedProduct.title + " успешно добавлено")
			}).catch((error) => {
				notifyError(error.response.data)
			})

		}

		const onImagesRejected  = (rejectedEntries) =>
			notifyError("Не получилось загрузить " + rejectedEntries.length + " изображений")

		return {
			onImagesRejected,
			syncImages
		}
	}
}
</script>
