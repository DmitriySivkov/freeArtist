<template>
	<q-uploader
		multiple
		accept=".jpg, image/*"
		:factory="syncImages"
		@rejected="onImagesRejected"
		class="full-width"
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
				class="full-width"
			>
				<q-uploader-add-trigger />
			</q-btn>
		</template>
		<template v-slot:list="scope">
			<div class="q-col-gutter-md row items-start">
				<div
					class="col-xs-12 col-md-6 col-lg-4"
					v-for="file in scope.files"
					:key="file.__key"
				>
					<q-img
						:src="file.__img.src"
						fit="contain"
						style="height: 250px;"
					>
						<div class="absolute-right">
							<q-btn
								icon="delete"
								@click="scope.removeFile(file)"
							/>
							<q-btn
								icon="cloud_upload"
								@click="scope.upload"
							/>
						</div>
					</q-img>
				</div>
			</div>
		</template>
	</q-uploader>
</template>

<script>
import { useRouter } from "vue-router"
import { useNotification } from "src/composables/notification"
import { ref } from "vue"
import { api } from "boot/axios"
import { Loading } from "quasar"

export default {
	setup() {
		const $router = useRouter()
		const { notifySuccess, notifyError } = useNotification()

		const product_pictures = ref(null)

		const syncImages = async (files) => {
			product_pictures.value = files[0]

			const fileData = new FormData()
			fileData.append("product_pictures", product_pictures.value)


			await api.post(
				"/personal/producers/" + $router.currentRoute.value.params.team_id + "/products/" + product.value.id +"/syncImages",
				fileData,
				{
					headers: {
						"Content-Type": "multipart/form-data"
					}
				}).then(() => {
				notifySuccess("Изображение успешно загружено")
			}).catch(() => {
				Loading.hide()
			})

		}

		const onImagesRejected  = (rejectedEntries) =>
			notifyError(rejectedEntries.length + " изображений не прошло валидацию")

		return {
			onImagesRejected,
			syncImages
		}
	}
}
</script>
