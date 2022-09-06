<template>
	<q-form
		@submit="onSubmit"
		@reset="onReset"
		class="q-gutter-md"
	>
		<q-tabs
			v-model="tab"
			active-color="white"
			active-bg-color="secondary"
			indicator-color="transparent"
			align="justify"
			class="text-white bg-indigo-10"
		>
			<q-tab
				name="common"
				label="Общие"
				class="q-pa-sm"
			/>
			<q-tab
				name="composition"
				label="Состав"
				class="q-pa-sm"
			/>
			<q-tab
				name="images"
				label="Изображения"
				class="q-pa-sm"
			/>
		</q-tabs>

		<q-tab-panels
			v-model="tab"
			animated
		>
			<q-tab-panel name="common">
				<q-input
					filled
					label="Название *"
					v-model="product.title"
					lazy-rules
					:rules="[ val => val && val.length > 3 || 'Название должно быть длиннее 3 символов']"
				/>

				<q-input
					filled
					type="number"
					label="Стоимость *"
					v-model="product.price"
					lazy-rules
					:rules="[
						val => val !== null && val !== '' || 'Укажите стоимость',
					]"
				/>

				<q-input
					filled
					type="number"
					label="Доступное количество"
					v-model="product.amount"
				/>
			</q-tab-panel>

			<q-tab-panel name="composition">
				<q-btn
					label="Добавить ингридиент"
					color="primary"
					class="q-mb-md"
					@click="addIngredient"
				/>
				<q-card
					v-for="(ingredient, index) in product.composition"
					:key="index"
					flat
				>
					<q-input
						:disable="ingredient.to_delete"
						filled
						label="Название ингридиента"
						v-model="ingredient.name"
						lazy-rules
						:rules="[ val => val && val.length > 2 || 'Название ингридиента должно быть длиннее 2 символов']"
						class="q-pb-lg"
					>
						<template v-slot:after>
							<q-btn
								v-if="ingredient.to_delete"
								flat
								square
								icon="restore"
								class="bg-secondary text-white full-height"
								@click="restoreIngredient(index)"
							/>
							<q-btn
								v-else
								flat
								square
								icon="clear"
								class="bg-red text-white full-height"
								@click="deleteIngredient(index)"
							/>

						</template>
					</q-input>
					<q-input
						:disable="ingredient.to_delete"
						filled
						type="textarea"
						label="Описание ингридиента (необязательно)"
						v-model="ingredient.description"
					/>

					<q-separator
						v-if="index !== product.composition.length-1"
						class="q-mt-sm q-mb-sm bg-primary"
					/>
				</q-card>
			</q-tab-panel>

			<q-tab-panel
				name="images"
				class="full-height"
			>
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
			</q-tab-panel>
		</q-tab-panels>
	</q-form>
</template>

<script>
import { computed, ref } from "vue"
import { useNotification } from "src/composables/notification"
import _ from "lodash"
import { useRouter } from "vue-router"
import { api } from "boot/axios"
import { Loading } from "quasar"
export default {
	props: {
		selectedProduct: {
			type: Object,
			default: () => {}
		}
	},
	setup(props) {
		const $router = useRouter()
		const { notifySuccess, notifyError } = useNotification()

		const product = ref(_.cloneDeep(props.selectedProduct))

		const tab = ref("images")
		const product_pictures = ref(null)

		const onSubmit = () => 123
		const onReset = () => 123

		const addIngredient = () =>
			product.value.composition.unshift({name:"", description:"", is_new:true})

		const deleteIngredient = (item_index) => {
			if (product.value.composition[item_index].is_new) {
				product.value.composition = product.value.composition.filter((i, index) => index !== item_index)
				return
			}
			product.value.composition.find((i, index) => index === item_index).to_delete = true
		}

		const restoreIngredient = (item_index) =>
			product.value.composition.find((i, index) => index === item_index).to_delete = false

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
			product,
			tab,
			onSubmit,
			onReset,
			addIngredient,
			deleteIngredient,
			restoreIngredient,
			onImagesRejected,
			syncImages
		}
	}
}
</script>
