<template>
	<q-card
		class="q-ma-md bg-green-3"
		style="height:300px"
	>
		<q-card-section
			horizontal
			class="full-height"
		>
			<q-card-section class="col-6 q-pa-none">
				<div
					v-if="can_manage_storefront || is_team_admin"
					class="full-height full-width absolute cursor-pointer flex flex-center storefront__setup-box"
					:class="{'bg-green-6 text-white': is_dragging === 1}"
					style="border-right:1px dashed black"
					@dragenter.prevent="is_dragging = 1"
					@dragleave.prevent="onDragLeave"
					@dragover.prevent
					@drop.prevent="drop"
					@click="showFilePrompt"
				>
					<span
						class="text-center"
					>
						1
					</span>
				</div>
				<q-inner-loading :showing="is_loading === 1">
					<q-spinner-gears
						size="50px"
						color="primary"
					/>
				</q-inner-loading>
			</q-card-section>
			<q-card-section class="col-6 column q-pa-none">
				<div class="col-6 relative-position">
					<div
						v-if="can_manage_storefront || is_team_admin"
						class="full-height full-width absolute cursor-pointer flex flex-center storefront__setup-box"
						:class="{'bg-green-6 text-white': is_dragging === 2}"
						style="border-bottom:1px dashed black"
						@dragenter.prevent="is_dragging = 2"
						@dragleave.prevent="onDragLeave"
						@dragover.prevent
						@drop.prevent="drop"
						@click="showFilePrompt"
					>
						<span
							class="text-center"
						>
							2
						</span>
					</div>
					<q-inner-loading :showing="is_loading === 2">
						<q-spinner-gears
							size="50px"
							color="primary"
						/>
					</q-inner-loading>
				</div>
				<div class="col-grow relative-position">
					<div
						v-if="can_manage_storefront || is_team_admin"
						class="full-height full-width absolute cursor-pointer flex flex-center storefront__setup-box"
						:class="{'bg-green-6 text-white': is_dragging === 3}"
						@dragenter.prevent="is_dragging = 3"
						@dragleave.prevent="onDragLeave"
						@dragover.prevent
						@drop.prevent="drop"
						@click="showFilePrompt"
					>
						<span
							class="text-center"
						>
							3
						</span>
					</div>
					<q-inner-loading :showing="is_loading === 3">
						<q-spinner-gears
							size="50px"
							color="primary"
						/>
					</q-inner-loading>
				</div>
			</q-card-section>
		</q-card-section>
	</q-card>
	<!--	<q-file-->
	<!--		v-model="image"-->
	<!--		multiple-->
	<!--		ref="file_picker"-->
	<!--		accept=".jpg, image/*"-->
	<!--		style="display:none"-->
	<!--		@update:model-value="addImage"-->
	<!--	/>-->

	<q-card
		class="q-ma-md"
		style="height: 300px"
	>
		<q-card-section class="q-pa-none full-height">
			<q-carousel
				v-if="!is_fetching_thumbnails && thumbnails.length > 0"
				swipeable
				animated
				v-model="selectedThumbnail"
				infinite
				height="300px"
				class="rounded-borders"
			>
				<q-carousel-slide
					v-for="thumbnail in thumbnails"
					:key="thumbnail.id"
					:name="thumbnail.id"
					class="q-pa-none height"
				>
					<q-img
						height="300px"
						fit="contain"
						:src="backendServer + '/storage/' + thumbnail.path"
					/>
				</q-carousel-slide>
			</q-carousel>
			<div
				v-else-if="!is_fetching_thumbnails"
				class="full-height flex flex-center"
			>
				Выберите заставки своим продуктам чтобы составить витрину
			</div>
			<q-inner-loading :showing="is_fetching_thumbnails">
				<q-spinner-gears
					color="primary"
					size="50px"
				/>
			</q-inner-loading>
		</q-card-section>
	</q-card>
</template>

<script>
import { useUserStore } from "src/stores/user"
import { useTeamStore } from "src/stores/team"
import { useProducerStore } from "src/stores/producer"
import { usePermissionStore } from "src/stores/permission"
import { computed, ref } from "vue"
import { useRouter } from "vue-router"
import { useNotification } from "src/composables/notification"
import { api } from "src/boot/axios"
export default {
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

		const file_picker = ref(null)
		const is_dragging = ref(0)
		const is_loading = ref(0)

		const is_team_admin = computed(() => user_store.data.id === team.value.user_id)

		const can_manage_storefront = computed(() =>
			permission_store.user_permissions.filter((p) => p.team_id === parseInt(team.value.id))
				.map((p) => p.name)
				.includes("producer_storefront")
		)

		const backend_server = process.env.BACKEND_SERVER
		const { notifySuccess, notifyError } = useNotification()

		const drop = (e) => {
			file_picker.value.addFiles([e.dataTransfer.files[0]])
		}

		const addImage = () => {
			is_loading.value = true

			let form_data = new FormData()
			form_data.append("logo", image.value)

			// todo validate with q-file help
			const promise = producer_store.setProducerLogo({
				logo: form_data,
				producer_id: team.value.detailed_id
			})

			promise.then((response) => {
				team_store.setTeamFields({
					team_id: team.value.id,
					fields: {
						logo: response.data
					},
					detailed_id: team.value.detailed_id
				})

				notifySuccess("Изображение успешно загружено")
			})

			promise.catch((error) => {
				notifyError(error.response.data)
			})

			promise.finally(() => {
				is_loading.value = 0
				is_dragging.value = 0
			})
		}

		const showFilePrompt = () => {
			file_picker.value.pickFiles()
		}

		const onDragLeave = (e) => {
			if (e.relatedTarget.classList.contains("storefront__setup-box")) return
			is_dragging.value = 0
		}

		const thumbnails = ref([])

		const is_fetching_thumbnails = ref(false)

		const fetchThumbnails = () => {
			is_fetching_thumbnails.value = true

			const promise = api.get(
				"personal/producers/" + $router.currentRoute.value.params.producer_id + "/products/thumbnails"
			)

			promise.then((response) => {
				if (response.data.length === 0) return

				thumbnails.value = response.data

				selectedThumbnail.value = thumbnails.value[0].id
			})

			promise.catch((error) => {
				notifyError(error.response.data)
			})

			promise.finally(() => is_fetching_thumbnails.value = false)
		}

		fetchThumbnails()

		const selectedThumbnail = ref(null)

		return {
			image,
			backend_server,
			is_loading,
			is_dragging,
			drop,
			addImage,
			can_manage_storefront,
			is_team_admin,
			showFilePrompt,
			file_picker,
			onDragLeave,
			selectedThumbnail,
			thumbnails,
			backendServer,
			is_fetching_thumbnails
		}
	}
}
</script>
