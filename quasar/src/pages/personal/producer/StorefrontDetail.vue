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
</template>

<script>
import { useUserStore } from "src/stores/user"
import { useTeamStore } from "src/stores/team"
import { useProducerStore } from "src/stores/producer"
import { usePermissionStore } from "src/stores/permission"
import { computed, ref } from "vue"
import { useRouter } from "vue-router"
import { useNotification } from "src/composables/notification"
export default {
	setup() {
		const $router = useRouter()
		const user_store = useUserStore()
		const team_store = useTeamStore()
		const producer_store = useProducerStore()
		const permission_store = usePermissionStore()

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
			onDragLeave
		}
	}
}
</script>
