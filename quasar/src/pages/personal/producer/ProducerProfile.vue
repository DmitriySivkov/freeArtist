<script setup>
import { computed, ref } from "vue"
import { useNotification } from "src/composables/notification"
import { useRouter } from "vue-router"
import { useUser } from "src/composables/user"
import { useUserStore } from "src/stores/user"
import { Cropper } from "vue-advanced-cropper"
import { api } from "src/boot/axios"
import { PRODUCER_PERMISSION_NAMES } from "src/const/permissions"

const $router = useRouter()

const userStore = useUserStore()

const { hasPermission } = useUser()

const team = computed(() =>
	userStore.teams.find((t) =>
		t.detailed_id === parseInt($router.currentRoute.value.params.producer_id)
	)
)

const backendServer = process.env.BACKEND_SERVER

const image = ref(null)
const tmpImage = ref(null)

const cropper = ref(null)
const filePicker = ref(null)

const isDragging = ref(false)
const isLoading = ref(false)

const isTeamAdmin = computed(() =>
	userStore.data.id === team.value.user_id
)

const canManageLogo = computed(() =>
	hasPermission(parseInt(team.value.id), PRODUCER_PERMISSION_NAMES.LOGO)
)

const { notifySuccess, notifyError } = useNotification()

const drop = (e) => {
	isDragging.value = false
	filePicker.value.addFiles([e.dataTransfer.files[0]])
}

const addImage = () => {
	tmpImage.value = URL.createObjectURL(image.value)
}

const cancelImage = () => {
	tmpImage.value = null
	image.value = null
}

const loadImage = async() => {
	isLoading.value = true

	const { canvas } = cropper.value.getResult()

	const blobPromise = new Promise((resolve) => canvas.toBlob(resolve, "image/jpeg"))

	blobPromise.then((logo) => {
		let formData = new FormData()

		formData.append("logo", logo)

		const promise = api.post(
			`/personal/producers/${team.value.detailed.id}/setLogo`,
			formData,
		)

		promise.then((response) => {
			userStore.setTeamFields({
				teamId: team.value.id,
				fields: {
					logo: response.data
				},
				detailedId: team.value.detailed.id
			})
		})

		promise.catch((error) => {
			notifyError(error.response.data)
		})

		promise.finally(() => isLoading.value = false)

		cancelImage()
	})
}

const showFilePrompt = () => {
	filePicker.value.pickFiles()
}

// todo - check if working
const teamPropChanged = (val, field) => {
	const promise = api.put(`personal/teams/${team.value.id}`, {
		fields: {[field]: val}
	})

	promise.then(() => {
		userStore.setTeamFields({
			teamId: team.value.id,
			fields: {[field]: val}
		})
	})

	promise.catch((error) => {
		notifyError(error.response.data.message)

		userStore.setTeamFields({
			teamId: error.response.data.team.id,
			fields: error.response.data.team
		})
	})
}
</script>

<template>
	<q-page class="row justify-center q-pa-xs">
		<div class="col-xs-12 col-sm-7 col-lg-6 col-xl-5">
			<div class="row">
				<div class="col-12">
					<q-responsive
						:ratio="16/9"
						class="bg-green-4 rounded-borders"
						:class="{'bg-green-6 text-white': isDragging}"
					>
						<div>
							<q-img
								v-if="team.detailed.logo && !tmpImage"
								:src="backendServer + '/storage/' + team.detailed.logo.path"
								class="absolute rounded-borders"
							/>

							<Cropper
								v-else-if="tmpImage"
								ref="cropper"
								class="absolute fit"
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
								v-if="canManageLogo || isTeamAdmin"
								class="absolute fit cursor-pointer"
								@dragenter.prevent="isDragging = true"
								@dragleave.prevent="isDragging = false"
								@dragover.prevent
								@drop.prevent="drop"
								@click="showFilePrompt"
							></div>

							<q-inner-loading :showing="isLoading">
								<q-spinner-gears
									size="md"
									color="primary"
								/>
							</q-inner-loading>
						</div>
					</q-responsive>
				</div>
			</div>
			<div
				v-if="tmpImage"
				class="row q-gutter-xs q-mt-xs"
			>
				<q-btn
					color="primary"
					icon="done"
					class="col q-pa-lg"
					@click="loadImage"
				/>
				<q-btn
					color="red"
					icon="clear"
					class="col q-pa-lg"
					@click="cancelImage"
				/>
			</div>
			<div class="row q-mt-xs">
				<div class="col-12">
					<q-card>
						<q-list class="bg-primary text-white">
							<q-item clickable>
								<q-item-section avatar>
									<q-icon
										color="white"
										name="edit"
									/>
								</q-item-section>
								<q-item-section class="text-center">
									{{ team.display_name }}
								</q-item-section>
								<q-popup-edit
									:model-value="team.display_name"
									@update:model-value="teamPropChanged($event, 'display_name')"
									auto-save
									v-slot="scope"
								>
									<q-input
										:model-value="scope.value"
										@update:model-value="scope.value = $event"
										dense
										autofocus
										@keyup.enter="scope.set"
									/>
								</q-popup-edit>
							</q-item>

							<q-item clickable>
								<q-item-section avatar>
									<q-icon
										color="white"
										name="edit"
									/>
								</q-item-section>
								<q-item-section class="text-center">
									{{ team.description }}
								</q-item-section>
								<q-popup-edit
									:model-value="team.description"
									@update:model-value="teamPropChanged($event, 'description')"
									auto-save
									v-slot="scope"
								>
									<q-input
										:model-value="scope.value"
										@update:model-value="scope.value = $event"
										dense
										autofocus
										@keyup.enter="scope.set"
									/>
								</q-popup-edit>
							</q-item>
						</q-list>
					</q-card>
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
	</q-page>
</template>
