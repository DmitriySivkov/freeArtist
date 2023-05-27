<template>
	<div class="row q-pa-md">
		<q-responsive
			:ratio="16/9"
			class="col"
			style="max-height: 300px"
		>
			<q-card
				class="column bg-green-3"
				:class="{'bg-green-6 text-white': is_dragging}"
			>
				<!-- todo - bring to this format all picture boxes (remove q-card-section container) -->
				<cropper
					v-if="tmp_image"
					ref="cropper"
					class="cropper full-width"
					backgroundClass="bg-green-4"
					:src="tmp_image"
					:stencil-props="{aspectRatio: 16/9}"
					style="z-index:9999"
				/>

				<q-img
					v-else-if="team.detailed.storefront_image"
					:src="backend_server + '/storage/' + team.detailed.storefront_image.path"
					fit="contain"
				/>

				<span
					v-else
					class="text-center"
				>
					Выберите изображение <br /> или переместите в эту область
				</span>

				<div
					v-if="can_manage_storefront || is_team_admin"
					class="full-height full-width absolute cursor-pointer"
					@dragenter.prevent="is_dragging = true"
					@dragleave.prevent="is_dragging = false"
					@dragover.prevent
					@drop.prevent="drop"
					@click="showFilePrompt"
				></div>
				<q-inner-loading
					:showing="is_loading"
					style="z-index:99999"
				>
					<q-spinner-gears
						size="50px"
						color="primary"
					/>
				</q-inner-loading>
			</q-card>
		</q-responsive>
	</div>
	<div
		v-if="tmp_image"
		class="row q-col-gutter-md q-px-md"
	>
		<div class="col-6">
			<q-btn
				color="secondary"
				icon="done"
				class="q-pa-lg full-height full-width"
				@click="loadImage"
			/>
		</div>
		<div class="col-6">
			<q-btn
				color="red"
				icon="clear"
				class="q-pa-lg full-height full-width"
				@click="cancelImage"
			/>
		</div>
	</div>
	<q-file
		v-model="image"
		ref="file_picker"
		accept=".jpg, image/*"
		style="display:none"
		@update:model-value="addImage"
	/>


<!--	<q-carousel-->
<!--		v-if="!is_fetching_thumbnails && thumbnails.length > 0"-->
<!--		v-model="slide"-->
<!--		transition-prev="slide-right"-->
<!--		transition-next="slide-left"-->
<!--		swipeable-->
<!--		animated-->
<!--		control-color="white"-->
<!--		infinite-->
<!--		padding-->
<!--		arrows-->
<!--		class="q-mt-md q-mx-md bg-primary rounded-borders"-->
<!--	>-->
<!--		<q-carousel-slide-->
<!--			v-for="i in Math.ceil(thumbnails.length/3)"-->
<!--			:key="i"-->
<!--			:name="i"-->
<!--			class="q-pa-none column no-wrap"-->
<!--		>-->
<!--			<div class="row fit justify-start items-center q-gutter-xs q-col-gutter no-wrap">-->
<!--				<q-img-->
<!--					v-for="thumbnail in thumbnails.slice((i-1)*3, i*3)"-->
<!--					:key="thumbnail.id"-->
<!--					class="col-4 full-height"-->
<!--					fit="contain"-->
<!--					:src="backendServer + '/storage/' + thumbnail.path"-->
<!--				/>-->
<!--			</div>-->
<!--		</q-carousel-slide>-->
<!--	</q-carousel>-->
<!--	<div-->
<!--		v-else-if="!is_fetching_thumbnails"-->
<!--		class="full-height flex flex-center"-->
<!--	>-->
<!--		Выберите заставки своим продуктам чтобы добавить картинки витрине-->
<!--	</div>-->
<!--	<q-inner-loading :showing="is_fetching_thumbnails">-->
<!--		<q-spinner-gears-->
<!--			color="primary"-->
<!--			size="50px"-->
<!--		/>-->
<!--	</q-inner-loading>-->

</template>

<script>
import { useUserStore } from "src/stores/user"
import { useTeamStore } from "src/stores/team"
import { useProducerStore } from "src/stores/producer"
import { usePermissionStore } from "src/stores/permission"
import { computed, ref } from "vue"
import { useRouter } from "vue-router"
import { useNotification } from "src/composables/notification"
import { Cropper } from "vue-advanced-cropper"
export default {
	components: { Cropper },
	setup() {
		const $router = useRouter()
		const user_store = useUserStore()
		const team_store = useTeamStore()
		const producer_store = useProducerStore()
		const permission_store = usePermissionStore()

		const backendServer = process.env.BACKEND_SERVER

		const team = computed(() =>
			team_store.user_teams.find((t) => t.detailed.id === parseInt($router.currentRoute.value.params.producer_id))
		)

		const image = ref(null)

		const cropper = ref(null)

		const file_picker = ref(null)
		const is_dragging = ref(false)
		const is_loading = ref(false)

		const is_team_admin = computed(() => user_store.data.id === team.value.user_id)

		const can_manage_storefront = computed(() =>
			permission_store.user_permissions.filter((p) => p.team_id === parseInt(team.value.id))
				.map((p) => p.name)
				.includes("producer_storefront")
		)

		const backend_server = process.env.BACKEND_SERVER
		const { notifySuccess, notifyError } = useNotification()

		const drop = (e) => {
			is_dragging.value = false
			file_picker.value.addFiles([e.dataTransfer.files[0]])
		}

		const tmp_image = ref(null)

		const addImage = () => {
			tmp_image.value = URL.createObjectURL(image.value)
		}

		const cancelImage = () => {
			tmp_image.value = null
			image.value = null
		}

		const loadImage = async() => {
			is_loading.value = true

			const { canvas } = cropper.value.getResult()

			let form_data = new FormData()

			const blob = await new Promise(resolve => canvas.toBlob(resolve))

			form_data.append("storefront_image", blob)

			// todo validate with q-file help
			const promise = producer_store.setProducerStorefrontImage({
				storefront_image: form_data,
				producer_id: team.value.detailed_id
			})

			promise.then((response) => {
				team_store.setTeamFields({
					team_id: team.value.id,
					fields: {
						storefront_image: response.data
					},
					detailed_id: team.value.detailed_id
				})

				notifySuccess("Изображение успешно загружено")
			})

			promise.catch((error) => {
				notifyError(error.response.data)
			})

			promise.finally(() => {
				is_loading.value = false
				is_dragging.value = false
				tmp_image.value = null
			})
		}

		const showFilePrompt = () => {
			file_picker.value.pickFiles()
		}

		const onCropperChange = ({ canvas }) => {
			this.image = canvas.toDataURL()
		}

		return {
			team,
			image,
			cropper,
			backend_server,
			is_loading,
			is_dragging,
			drop,
			addImage,
			loadImage,
			cancelImage,
			can_manage_storefront,
			is_team_admin,
			showFilePrompt,
			file_picker,
			backendServer,
			tmp_image
		}
	}
}
</script>
